<?php

namespace hoo\Test;
use hoo\InputValidation;
use hoo\Request;
use hoo\Database\DatabaseNameLookup;
use PHPUnit_Framework_TestCase;

/**
 * Tests for the InputValidation class
 *
 * @covers hoo\InputValidation
 *
 * @group Test
 *
 * @licence GNU GPL v2+
 * @author Marius Hoch < hoo@online.de >
 */

class InputValidationTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getRequiredParamsProvider
	 */
	public function testGetRequiredParams( $params, $expected ) {
		$valid = new InputValidation( $params, new DatabaseNameLookup() );

		$this->assertEquals( $expected, $valid->getRequiredParams() );
    }

    /**
     * @return array
     */
    public static function getRequiredParamsProvider() {
		return array(
			array(
				array(
					'foo' => array(),
					'bar' => array( 'required' => true ),
					'blah' => array( 'default' =>  1234 ),
					'meh' => array( 'required' => true ),
				),
				array( 'bar', 'meh' )
			),
			array(
				array(
					'foo' => array(),
					'blah' => array( 'default' =>  1234 ),
					'Русский' => array( 'default' => 'Москва' ),
				),
				array()
			),
		);
	}

	/**
	 * @dataProvider verifyRequestFulfillsProvider
	 */
	public function testVerifyRequestFulfills( $params, $input, $expected ) {
		$valid = new InputValidation( $params, new DatabaseNameLookup() );
		$req = new Request( $input );

		$this->assertEquals( $expected, $valid->verifyRequestFulfills( $req ) );
    }

    /**
     * @return array
     */
    public static function verifyRequestFulfillsProvider() {
		return array(
			array(
				array(
					'foo' => array(),
					'bar' => array( 'required' => true ),
					'blah' => array( 'default' =>  1234 ),
					'meh' => array( 'required' => true ),
				),
				array( 'bar' => 'meh', 'meh' => 123 ),
				true
			),
			array(
				array(
					'foo' => array(),
					'blah' => array( 'default' =>  1234 ),
					'Русский' => array( 'default' => 'Москва' ),
				),
				array(),
				true
			),
			array(
				array(
					'foo' => array( 'required' => true ),
					'blah' => array( 'default' =>  1234 ),
				),
				array(),
				false
			),
			array(
				array(
					'foo' => array(),
					'bar' => array( 'required' => true ),
					'blah' => array( 'default' =>  1234 ),
					'meh' => array( 'required' => true ),
				),
				array( 'meh' => 123 ),
				false
			),
			array(
				array(
					'foo' => array( 'type' => 'wiki' ),
				),
				array( 'foo' => 'this is not a wiki' ),
				false
			),
		);
	}

	/**
	 * @dataProvider verifyRequestFulfillsTypeWikiProvider
	 */
	public function testVerifyRequestFulfillsTypeWiki( $params, $fail, $expected ) {
		$databaseNameLookup = $this->getMock( '\hoo\Database\DatabaseNameLookup' );

		$databaseNameLookup->expects( $this->once() )
			->method( 'lookup' )
			->will( $this->returnValue( $fail ) );

		$valid = new InputValidation( $params, $databaseNameLookup );
		$req = new Request( array( 'foo' => 'whatever' ) );

		$this->assertEquals( $expected, $valid->verifyRequestFulfills( $req ) );
    }

	/**
	 * @return array
	 */
	public static function verifyRequestFulfillsTypeWikiProvider() {
		return array(
			array(
				array(
					'foo' => array( 'type' => 'wiki' ),
				),
				null,
				false
			),
			array(
				array(
					'foo' => array( 'type' => 'wiki' ),
				),
				'df',
				true
			)
		);
	}

	/**
	 * @dataProvider getInputFromRequestProvider
	 */
	public function testGetInputFromRequest( $params, $input, $expected ) {
		$valid = new InputValidation( $params, new DatabaseNameLookup() );
		$req = new Request( $input );

		$output = $valid->getInputFromRequest( $req );

		$this->assertEquals( $expected, $output );
	}

	/**
	 * @return array
	 */
	public static function getInputFromRequestProvider() {
		return array(
			array(
				array(
					'foo' => array(),
					'bar' => array( 'required' => true ),
					'blah' => array( 'default' =>  1234 ),
					'meh' => array( 'required' => true ),
				),
				array( 'bar' => 'meh', 'meh' => 123 ),
				array( 'bar' => 'meh', 'meh' => 123, 'blah' => 1234 ),
			),
			array(
				array(
					'foo' => array(),
					'bar' => array( 'required' => true ),
					'blah' => array( 'default' =>  1234 ),
					'meh' => array( 'type' => 'list', 'required' => true ),
				),
				array( 'bar' => 'meh', 'meh' => 'a|b|c' ),
				array( 'bar' => 'meh', 'meh' => array( 'a', 'b', 'c' ), 'blah' => 1234 ),
			),
		);
	}
}
