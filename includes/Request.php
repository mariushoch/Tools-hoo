<?php

namespace hoo;

/**
 * Utility class for data from web requests.
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

class Request {

	/**
	 * @var array
	 */
	protected $post = array();

	/**
	 * @var array
	 */
	protected $get = array();

	/**
	 * @var string
	 */
	protected $output = '';

	/**
	 * @param PDO $conn
	 */
	public function __construct( array $get = array(), array $post = array() ) {
		$this->get = $get;
		$this->post = $post;
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	protected function getInputFromGet( $name ) {
		if ( isset( $this->get[ $name ] ) ) {
			return $this->get[ $name ];
		} else {
			return null;
		}
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	protected function getInputFromPost( $name ) {
		if ( isset( $this->post[ $name ] ) ) {
			return $this->post[ $name ];
		} else {
			return null;
		}
	}

	/**
	 * Get a specific index from the user input (defaults to post and falls back to get)
	 *
	 * @param string $name
	 * @param mixed $fallback
	 *
	 * @return mixed
	 */
	public function getInput( $name, $fallback = null ) {
		$output = $this->getInputFromPost( $name );
		if ( $output === null ) {
			$output = $this->getInputFromGet( $name );
		}

		return $output === null ? $fallback : $output;
	}

	/**
	 * Get all user given input values
	 *
	 * @return array
	 */
	public function getAllInput() {
		return array_merge( $this->get, $this->post );
	}

	/**
	 * Set the output to give
	 *
	 * @param string $output
	 */
	public function setOutput( $output ) {
		$this->output = $output;
	}

	/**
	 * Append to the output
	 *
	 * @param string $output
	 */
	public function appendOutput( $output ) {
		$this->output .= $output;
	}

	/**
	 * Get the output
	 *
	 * @return string
	 */
	public function getOutput() {
		return $this->output;
	}
}
