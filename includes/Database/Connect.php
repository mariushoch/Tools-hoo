<?php

namespace hoo\Database;
use PDO;
use RuntimeException;

/**
 * Utility class for getting access to databases on Wikimedia tool labs.
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

class DatabaseConnect {

	/**
	 * @var string
	 */
	private $dbPass;

	/**
	 * @var string
	 */
	private $dbUser;

	/**
	 * @var PDO[]
	 */
	protected $connectionCache = array();

	/**
	 * @var DatabaseNameLookup
	 */
	protected $databaseNameLookup;

	public function __construct( $databaseNameLookup = null ) {
		$this->databaseNameLookup = $databaseNameLookup ?
			$databaseNameLookup : new DatabaseNameLookup();
	}

	/**
	 * Load the credentials for database access from ~/replica.my.cnf
	 *
	 * @throws RuntimeException
	 */
	private function loadCredentials() {
		if ( !$this->dbPass ) {
			@$mycnf = parse_ini_file( __DIR__ . '/../../../replica.my.cnf' );

			if ( !is_array( $mycnf ) || !isset( $mycnf['password'] ) || !isset( $mycnf['user'] ) ) {
				throw new RuntimeException( "Couldn't load database credentials from replica.my.cnf" );
			}

			$this->dbPass = $mycnf['password'];
			$this->dbUser = $mycnf['user'];
		}
	}

	/**
	 * Get a database connection by host
	 *
	 * @throws RuntimeException
	 *
	 * @param $cluster string Name of the host to connect to
	 * @param $reConnect bool Create a new connection rather than returning
	 * 		a reference to an existing one
	 *
	 * @return PDO
	 */
	public function getFromHost( $host, $reConnect = false ) {

		if ( !$reConnect && isset( $connectionCache[ $host ] ) && $connectionCache[ $host ] ) {
			return $connectionCache[ $host ];
		}

		try {
			$this->loadCredentials();
			$pdo = new PDO(
				'mysql:host=' . $host . ';',
				$this->dbUser,
				$this->dbPass
			);
			$connectionCache[ $host ] = $pdo;
		} catch( PDOException $exception ) {
			throw new RuntimeException( "Couldn't connect to database server $host" );
		}

		return $connectionCache[ $host ];
	}

	/**
	 * Get a database connection by database
	 *
	 * @throws RuntimeException
	 *
	 * @param $database string Name of the database to get a connection to
	 * @param $reConnect bool Create a new connection rather than returning
	 * 		a reference to an existing one
	 *
	 * @return PDO
	 */
	public function getFromDatabaseName( $database, $reConnect = false ) {
		$host = $this->databaseNameLookup->lookup( $database );

		if ( !$host ) {
			throw new RuntimeException( "Unknown database $database" );
		}
		return $this->getFromHost( $host, $reConnect );
	}
}
