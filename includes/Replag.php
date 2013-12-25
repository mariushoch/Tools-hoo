<?php

namespace hoo;

use PDO;
use RuntimeException;

/**
 * Class for getting the replag for a database.
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

class Replag {

	/**
	 * @var PDO
	 */
	protected $connection;

	/**
	 * @param PDO $conn
	 */
	public function __construct( PDO $conn ) {
		$this->connection = $conn;
	}

	/**
	 * Get the replication lag for a specific DB
	 *
	 * @throws RuntimeException
	 *
	 * @param string $database
	 *
	 * @return integer
	 */
	public function getReplagForDB( $database ) {
		if ( preg_match( '/[^a-zA-Z0-9_-]/', $database ) ) {
			// @FIXME: This should really check the DB name in a better way
			throw new RuntimeException( 'Invalid database name ' . $database );
		}

		$db = $this->connection;

		$SQL = 'SELECT UNIX_TIMESTAMP() - UNIX_TIMESTAMP(IF((MAX(rc_timestamp) > MAX(log_timestamp)), MAX(rc_timestamp), MAX(log_timestamp))) as replag FROM ' . $database . '.recentchanges, ' . $database . '.logging';

		$statement = $db->prepare( $SQL );
		$state = $statement->execute();

		if ( !$state ) {
			throw new RuntimeException( 'Failed getting the replag for ' . $database );
		}

		$replag = $statement->fetchColumn( 0 );

		if ( !is_numeric( $replag ) ) {
			throw new RuntimeException( 'Failed getting the replag for ' . $database );
		}

		return (int) $replag;
	}
}
