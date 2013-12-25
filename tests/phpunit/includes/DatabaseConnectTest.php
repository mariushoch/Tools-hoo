<?php

namespace hoo\Test;
use hoo\DatabaseConnect;
use PHPUnit_Framework_TestCase;

/**
 * Tests for the DatabaseConnect class
 *
 * @covers hoo\DatabaseConnect
 *
 * @group LabsOnly
 * @group Test
 *
 * @licence GNU GPL v2+
 * @author Marius Hoch < hoo@online.de >
 */

class DatabaseConnectTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider databaseNameProvider
	 */
	public function testGetFromDatabaseName( $database ) {
		if ( !TestHelper::isLabs() ) {
			$this->markTestSkipped( 'The real database is needed for this to work' );
		}

		$dbConn= new DatabaseConnect();
		$conn = $dbConn->getFromDatabaseName( $database );
		$this->assertInstanceOf( 'PDO', $conn );
    }

    /**
     * @return array
     */
    public static function databaseNameProvider() {
		return array(
			array( 'dewiki' ),
			array( 'eswiki' ),
			array( 'ptwiki' ),
			array( 'zhwiki' ),
			array( 'ruwiki_p' )
		);
	}

	public function testNewFromDatabaseFail() {
		$dbConn= new DatabaseConnect();
		$this->setExpectedException( 'RuntimeException' );
		$dbConn->getFromDatabaseName( 'invalid_database_name' );
	}
}