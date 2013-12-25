<?php

namespace hoo\Test;
use hoo\Request;
use PHPUnit_Framework_TestCase;

/**
 * Tests for the Request class
 *
 * @covers hoo\Request
 *
 * @group Test
 *
 * @licence GNU GPL v2+
 * @author Marius Hoch < hoo@online.de >
 */

class RequestTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider provideTestGetAllInput
	 */
	public function testGetAllInput( $get, $post, $expected ) {
		$req = new Request( $get, $post );

		$this->assertEquals( $expected, $req->getAllInput() );
	}

	public static function provideTestGetAllInput() {
		return array(
			// pure get
			array( array( 'a' => 'a' ), array(), array( 'a' => 'a' ) ),
			// pure post
			array( array(), array( 'b' => 'b' ), array( 'b' => 'b' ) ),
			// get and post
			array( array( 'a' => 'a' ), array( 'b' => 'b' ), array( 'a' => 'a', 'b' => 'b' ) ),
			// post overrides get
			array( array( 'a' => 'a' ), array( 'a' => 'b' ), array( 'a' => 'b' ) ),
		);
	}

	/**
	 * @dataProvider provideTestGetInput
	 */
	public function testGetInput( $get, $post, $key, $expected, $fallback = null ) {
		$req = new Request( $get, $post );

		$this->assertEquals( $expected, $req->getInput( $key, $fallback ) );
	}

	public static function provideTestGetInput() {
		return array(
			// pure get
			array( array( 'a' => 'b' ), array(), 'a', 'b' ),
			// pure post
			array( array(), array( 'b' => 'c' ), 'b', 'c' ),
			// get and post
			array( array( 'a' => 'b' ), array( 'c' => 'd' ), 'a', 'b' ),
			array( array( 'a' => 'b' ), array( 'c' => 'd' ), 'c', 'd' ),
			// post overrides get
			array( array( 'a' => 'a' ), array( 'a' => 'b' ), 'a', 'b' ),
			// fallback
			array( array( 'a' => 'b' ), array(), 'foo', 'bar', 'bar' ),
			array( array( 'a' => 'b' ), array( 'c' => 'd' ), 'bar', 'foo', 'foo' ),
		);
	}

	public function testOutput() {
		$req = new Request();

		$this->assertEquals( '', $req->getOutput() );

		$req->setOutput( 'foo -  blergh' );
		$this->assertEquals( 'foo -  blergh', $req->getOutput() );

		$req->appendOutput( ' - muhahah' );
		$this->assertEquals( 'foo -  blergh - muhahah', $req->getOutput() );
	}
}
