<?php

namespace hoo\Api;
use hoo\Api\Exception;
use hoo\Request;
use hoo\Database\DatabaseConnect;
use hoo\Database\DatabaseNameLookup;
use DateTime;
use PDO;

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

class ActiveSysops extends Base {

	/**
	 * @param Request $req
	 * @param DatabaseConnect $databaseConnect
	 * @param DatabaseNameLookup $DatabaseNameLookup
	 */
	public function __construct(
		Request $req, DatabaseConnect $databaseConnect, DatabaseNameLookup $databaseNameLookup
	) {
		parent::__construct( $req, 'ActiveSysops', $databaseConnect, $databaseNameLookup );
	}

	/**
	 * @return array
	 */
	protected function getParams() {
		return array(
			'wiki' => array(
				'info' => 'The database name of the wiki to search in',
				'required' => true,
				'type' => 'wiki'
			),
			'lastAction' => array(
				'info' => 'Time in seconds since the last action to count the sysop as active (default: one week)',
				'default' => 604800,
				'type' => 'int'
			)
		);
	}

	/**
	 * @return string
	 */
	protected function getDescription() {
		return 'Shows the number of administrators who where recently active';
	}

	public function execute() {
		$params = $this->getInput();

		$params['lastAction'] = time() - $params['lastAction'];
		$date = new DateTime( '@' . $params['lastAction'] );

		$lastActionTime = $date->format( 'YmdHis' );
		$dbName = $params['wiki'] . '_p';
		$db = $this->databaseConnect->getFromDatabaseName( $dbName );

		$query = 'SELECT COUNT(*) AS active_sysops FROM (SELECT log_actor FROM ' . $dbName .
			'.logging WHERE log_type IN ("block","delete","protect") AND log_timestamp > :lastActionTime GROUP BY log_actor) as active_users ' .
			'INNER JOIN ' . $dbName . '.actor ON active_users.log_actor = actor_id ' .
			'INNER JOIN ' . $dbName . '.user_groups ON ug_user = actor_user WHERE ug_group = "sysop"';

		$statement = $db->prepare( $query );
		$statement->bindValue( ':lastActionTime', $lastActionTime, PDO::PARAM_STR );
		$statement->execute();

		$activeSysops = $statement->fetchColumn(0);

		if( $statement->errorCode() && $statement->errorCode() !== '00000' ) {
				throw new Exception(
						'Database error (' . $statement->errorCode() . ') on ' . $dbName
				);
		}
		if( !is_numeric( $activeSysops ) ) {
				throw new Exception(
						'Invalid result: ' . var_export( $activeSysops, true )
				);
		}

		return array(
			'count' => (int)$activeSysops
		);
	}
}
