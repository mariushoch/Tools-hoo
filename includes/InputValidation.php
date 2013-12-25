<?php

namespace hoo;
use hoo\Request;
use RuntimeException;

/**
 * Class for verifying that the input is complete and sane.
 *
 * Params definition format (everything despite the name is optional):
 * array( 'name' => array(
 *	'info' => 'A bit of information (human readable)',
 *	'required' => true,
 * 	'default' => 1234,
 *	'type' => 'int' // 'int', 'string', 'wiki' or 'list'
 * )
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @licence GNU GPL v2+
 * @author Marius Hoch < hoo@online.de >
 */

class InputValidation {

	/**
	 * @var array
	 */
	protected $params;

	/**
	 * @var DatabaseNameLookup
	 */
	protected $databaseNameLookup;

	public function __construct( array $params, DatabaseNameLookup $databaseNameLookup ) {
		$this->params = $params;
		$this->databaseNameLookup = $databaseNameLookup;
	}

	/**
	 * Get a list of parameters that are actually required
	 *
	 * @return array
	 */
	public function getRequiredParams() {
		$reqParams = array();

		foreach ( $this->params as $name => $param ) {
			if ( isset( $param['required'] ) && $param['required'] === true ) {
				$reqParams[] = $name;
			}
		}

		return $reqParams;
	}

	/**
	 * Verify whether the given Request can fulfill the type requirements.
	 *
	 * @param Request $req
	 *
	 * @return bool
	 */
	protected function verifyTypeFulfills( Request $req ) {
		$inputParams = $req->getAllInput();

		foreach ( $this->params as $name => $param ) {
			if ( !isset( $inputParams[ $name ] ) ) {
				continue;
			}

			if ( !isset( $param['type'] ) ) {
				continue;
			}

			if (
				$param['type'] === 'wiki' &&
				$this->databaseNameLookup->lookup( $inputParams[ $name ] ) === null
			) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Verify whether the given Request can fulfill the requirements.
	 *
	 * @param Request $req
	 *
	 * @return bool
	 */
	public function verifyRequestFulfills( Request $req ) {
		$inputParams = array_keys( $req->getAllInput() );
		$requiredParams = $this->getRequiredParams();

		return array_diff( $requiredParams, $inputParams ) === array()
			&& $this->verifyTypeFulfills( $req );
	}

	/**
	 * Convert the given data into the wanted format
	 *
	 * @throws RuntimeException
	 *
	 * @param mixed $input
	 * @param string $type
	 *
	 * @return mixed
	 */
	protected function convertInput( $input, $type ) {
		switch( $type ) {
			case 'int':
				return (int)$input;
			break;
			case 'string':
				return (string)$input;
			break;
			case 'list':
				return explode( '|', $input );
			case 'wiki':
				// Databases don't need any conversion
				return $input;
			break;
			default:
				throw new RuntimeException( 'Unknown input data type ' . $type );
			break;
		}
	}

	/**
	 * Get the input from the given request, in comply with the set parameters.
	 *
	 * @throws RuntimeException
	 *
	 * @param Request $req
	 *
	 * @return array
	 */
	public function getInputFromRequest( Request $req ) {
		if ( !$this->verifyRequestFulfills( $req ) ) {
			throw new RuntimeException( "Given request doesn't fulfill the requirements" );
		}

		$data = array();

		foreach ( $this->params as $name => $param ) {
			$input = $req->getInput(
				$name,
				isset( $param['default'] ) ? $param['default'] : null
			);

			if ( $input === null ) {
				continue;
			}

			if ( isset( $param['type'] ) ) {
				$input = $this->convertInput( $input, $param['type'] );
			}

			$data[ $name ] = $input;
		}

		return $data;
	}
}
