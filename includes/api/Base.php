<?php

namespace hoo\Api;
use hoo\InputValidation;
use hoo\Request;
use hoo\Database\DatabaseConnect;
use hoo\Database\DatabaseNameLookup;

/**
 * Base class for API module to derive from
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

abstract class Base {

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var DatabaseConnect
	 */
	protected $databaseConnect;

	/**
	 * @var DatabaseNameLookup
	 */
	protected $databaseNameLookup;

	/**
	 * @param Request $req
	 * @param string $name
	 * @param DatabaseConnect $databaseConnect
	 * @param DatabaseNameLookup $DatabaseNameLookup
	 */
	public function __construct(
		Request $req, $name, DatabaseConnect $databaseConnect, DatabaseNameLookup $databaseNameLookup
	) {
		$this->request = $req;
		$this->name = $name;
		$this->databaseConnect = $databaseConnect;
		$this->databaseNameLookup = $databaseNameLookup;
	}

	/**
	 * @return array
	 */
	protected function getInput() {
		$validate = new InputValidation( $this->getParams(), $this->databaseNameLookup );
		return $validate->getInputFromRequest( $this->request );
	}

	/**
	 * @return array
	 */
	abstract protected function getParams();

	/**
	 * @return string
	 */
	abstract protected function getDescription();

	abstract public function execute();
}
