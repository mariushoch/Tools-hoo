<?php

namespace hoo\Test;
use hoo\Replag;
use PHPUnit_Framework_TestCase;
use stdClass;
use PDO;
use PDOStatement;

// @see http://stackoverflow.com/a/4082629
class ReplagMockPDO extends PDO {
	public function __construct () {}
}
class ReplagMockPDOStatement extends PDOStatement {
	public function __construct () {}
}

/**
 * Tests for the Replag class
 *
 * @covers hoo\Replag
 *
 * @group Test
 *
 * @licence GNU GPL v2+
 * @author Marius Hoch < hoo@online.de >
 */

class ReplagTest extends PHPUnit_Framework_TestCase {

	public function testGetReplagForDB() {
		$pdo = $this->getPDOMock( true, 12 );
		$replag = new Replag( $pdo );
		$ret = $replag->getReplagForDB( 'whatever' );

		$this->assertEquals( 12, $ret );
	}

	public function testGetReplagForDBFail() {
		$pdo = $this->getPDOMock( false );
		$replag = new Replag( $pdo );
		$this->setExpectedException( 'RuntimeException' );

		$ret = $replag->getReplagForDB( 'whatever' );
	}

	public function testGetReplagForDBFailValue() {
		$pdo = $this->getPDOMock( true, new stdClass() );
		$replag = new Replag( $pdo );
		$this->setExpectedException( 'RuntimeException' );

		$ret = $replag->getReplagForDB( 'whatever' );
	}

	public function testGetReplagForDBFailName() {
		$pdo = $this->getPDOMock( true, new stdClass() );
		$replag = new Replag( $pdo );
		$this->setExpectedException( 'RuntimeException' );

		$ret = $replag->getReplagForDB( 'Русский' );
	}

	/**
	 * Get a mock PDO object
	 *
	 * @param bool $success Whether the query will succeed
	 * @param mixed $returnValue
	 *
	 * @return PDO
	 */
	protected function getPDOMock( $success, $returnValue = null ) {
		$pdoMock = $this->getMock( '\hoo\Test\ReplagMockPDO' );

		$statementMock = $this->getMock( '\hoo\Test\ReplagMockPDOStatement' );

		$statementMock->expects( $this->any() )
			->method( 'execute' )
			->will( $this->returnValue( $success ) );

		if ( $success ) {
			$statementMock->expects( $this->any() )
				->method( 'fetchColumn' )
				->will( $this->returnValue( $returnValue ) );
		}

		$pdoMock->expects( $this->any() )
			->method( 'prepare' )
			->will( $this->returnValue( $statementMock ) );

		return $pdoMock;
	}
}
