<?php

namespace hoo\Api\Test;
use hoo\Api\ActiveSysops;
use hoo\Request;
use PHPUnit_Framework_TestCase;
use PDO;
use PDOStatement;

/**
 * Tests for the ActiveSysops class
 *
 * @covers hoo\ActiveSysops
 *
 * @group LabsOnly
 * @group Test
 *
 * @licence GNU GPL v2+
 * @author Marius Hoch < hoo@online.de >
 */

// @see http://stackoverflow.com/a/4082629
class ActiveSysopsMockPDO extends PDO {
	public function __construct () {}
}
class ActiveSysopsMockPDOStatement extends PDOStatement {
	public function __construct () {}
}

class ActiveSysopsTest extends PHPUnit_Framework_TestCase {

	/**
	 * @param mixed $return
	 *
	 * @return \hoo\DatabaseConnect
	 */
	public function getDatabaseConnectMock( $return ) {
		$dbConn = $this->getMock( '\hoo\DatabaseConnect' );

		$pdoMock = $this->getMock( '\hoo\Api\Test\ActiveSysopsMockPDO' );

		$pdoStatementMock = $this->getMock( '\hoo\Api\Test\ActiveSysopsMockPDOStatement' );

		$pdoStatementMock->expects( $this->any() )
			->method( 'fetchColumn' )
			->will( $this->returnValue( $return ) );

		$pdoStatementMock->expects( $this->any() )
			->method( 'execute' )
			->will( $this->returnValue( true ) );

		$pdoMock->expects( $this->any() )
			->method( 'prepare' )
			->will( $this->returnValue( $pdoStatementMock ) );

		$dbConn->expects( $this->any() )
			->method( 'getFromDatabaseName' )
			->will( $this->returnValue( $pdoMock ) );

		return $dbConn;

	}

	/**
	 * @param mixed $return
	 *
	 * @return \hoo\DatabaseNameLookup
	 */
	public function getDatabaseNameLookupMock( $return ) {
		$databaseNameLookup = $this->getMock( '\hoo\DatabaseNameLookup' );

		$databaseNameLookup->expects( $this->any() )
			->method( 'lookup' )
			->will( $this->returnValue( $return ) );

		return $databaseNameLookup;

	}

	public function testPass() {
		$data = array(
			'wiki' => 'foo'
		);

		date_default_timezone_set( 'UTC' );

		$dbConn = $this->getDatabaseConnectMock( 123 );
		$databaseNameLookup = $this->getDatabaseNameLookupMock( 'blah' );

		$activeSysops = new ActiveSysops( new Request( $data ), $dbConn, $databaseNameLookup );

		$this->assertEquals(
			array( 'count' => 123, 'replag' => 123 ),
			$activeSysops->execute()
		);
    }

	public function testMissingWiki() {
		$data = array();

		date_default_timezone_set( 'UTC' );

		$dbConn = $this->getDatabaseConnectMock( 123 );
		$databaseNameLookup = $this->getDatabaseNameLookupMock( 'blah' );

		$activeSysops = new ActiveSysops( new Request( $data ), $dbConn, $databaseNameLookup );

		$this->setExpectedException( 'Exception' );
		$activeSysops->execute();
    }

	public function testInvalidWiki() {
		$data = array(
			'wiki' => 'foo'
		);

		date_default_timezone_set( 'UTC' );

		$dbConn = $this->getDatabaseConnectMock( 123 );
		$databaseNameLookup = $this->getDatabaseNameLookupMock( null );

		$activeSysops = new ActiveSysops( new Request( $data ), $dbConn, $databaseNameLookup );

		$this->setExpectedException( 'Exception' );
		$activeSysops->execute();
    }

	public function testQueryError() {
		$data = array(
			'wiki' => 'foo'
		);

		date_default_timezone_set( 'UTC' );

		$dbConn = $this->getDatabaseConnectMock( false );
		$databaseNameLookup = $this->getDatabaseNameLookupMock( 'blah' );

		$activeSysops = new ActiveSysops( new Request( $data ), $dbConn, $databaseNameLookup );

		$this->setExpectedException( 'Exception' );
		$activeSysops->execute();
    }
}
