<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Common_Load_Container_ZIPTest extends MW_Unittest_Testcase
{
	private $_container;
	private $_testfile;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Common_Load_Container_ZIPTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_testdir = 'Controller' . DIRECTORY_SEPARATOR . 'ExtJS' . DIRECTORY_SEPARATOR .
		'Common' . DIRECTORY_SEPARATOR . 'Load' . DIRECTORY_SEPARATOR . 'Container' . DIRECTORY_SEPARATOR .
		'testfiles';
		$this->_testfile = $this->_testdir . DIRECTORY_SEPARATOR . 'testfile.zip';

		$this->_container = new Controller_ExtJS_Common_Load_Container_ZIP( $this->_testfile );
	}

	protected function tearDown()
	{
		$this->_container->finish();
		$this->_container = null;
		unlink( $this->_testdir . DIRECTORY_SEPARATOR . 'unit.zip' );
	}

	public function testAddContent()
	{
		$this->_container = new Controller_ExtJS_Common_Load_Container_ZIP( $this->_testdir . DIRECTORY_SEPARATOR . 'unit' );

		$deCsv = new Controller_ExtJS_Common_Load_Content_CSV( $this->_testdir . DIRECTORY_SEPARATOR . 'unit' . DIRECTORY_SEPARATOR . 'de.csv' , 'de', null );

		$deCsv->addRow( array('This is first line', 'unit test 1', 'test 2') );
		$deCsv->addRow( array('This is second line', 'unit test 3', 'test 4') );

		$this->_container->addContent( $deCsv );

		$de = array();
		foreach( $this->_container->get('de') as $value ) {
			$de[] = $value;
		}

		$file = $this->_container->finish();

		$this->assertTrue( unlink( $this->_testdir . DIRECTORY_SEPARATOR . $file ) );
		$this->assertEquals( 'This is first line' , $de[0][0] );
		$this->assertEquals( 'This is second line' , $de[1][0] );
	}

	public function testAddContentException()
	{
		$de = $this->_container->createContent('de');

		$this->setExpectedException('Controller_ExtJS_Common_Load_Exception');
		$this->_container->addContent($de);
	}


	public function testRemoveContent()
	{
		$this->_container = new Controller_ExtJS_Common_Load_Container_ZIP( $this->_testdir . DIRECTORY_SEPARATOR . 'unit' );

		$deCsv = new Controller_ExtJS_Common_Load_Content_CSV( $this->_testdir . DIRECTORY_SEPARATOR . 'unit' . DIRECTORY_SEPARATOR . 'de.csv' , 'de', null );
		$enCsv = new Controller_ExtJS_Common_Load_Content_CSV( $this->_testdir . DIRECTORY_SEPARATOR . 'unit' . DIRECTORY_SEPARATOR . 'en.csv' , 'en', null );

		$deCsv->addRow( array('This is first line', 'unit test 1', 'test 2') );
		$deCsv->addRow( array('This is second line', 'unit test 3', 'test 4') );

		$enCsv->addRow( array('This is first line', 'unit test 1', 'test 2') );


		$this->_container->addContent( $deCsv );
		$this->_container->addContent( $enCsv );
		$this->_container->finish();

		$this->_container = new Controller_ExtJS_Common_Load_Container_ZIP( $this->_testdir . DIRECTORY_SEPARATOR . 'unit.zip' );

		$de = $this->_container->get('de');

		$this->_container->removeContent( 'de' );

		$this->_container->addContent( $deCsv );
		$this->_container->finish();

		$this->assertTrue( unlink( $this->_testdir . DIRECTORY_SEPARATOR . 'unit.zip' ) );
	}


	public function testGet()
	{
		$en = array();
		foreach( $this->_container->get('en') as $value ) {
			$en[] = $value;
		}

		$this->assertEquals( 'line2' , $en[1][0] );
		$this->assertEquals( 'This is the 3rd line' , $en[2][0] );
		$this->assertEquals( 'The end' , $en[3][0] );
	}


	public function testFinish()
	{
		$this->assertTrue( is_dir( $this->_testdir . DIRECTORY_SEPARATOR . 'testfile' ) );

		$this->_container->finish();

		$this->assertFalse( is_dir( $this->_testdir . DIRECTORY_SEPARATOR . 'testfile' ) );
	}


	public function testLoopThroughFiles()
	{
		$files = array();

		foreach( $this->_container as $key => $content )
		{
			foreach( $content as $value ) {
				$files[ $key ][] = $value;
			}
		}

		$this->assertEquals( 'The end' , $files['en'][3][0] );
		$this->assertEquals( 'Ende' , $files['de'][3][0] );
	}

	public function testRewind()
	{
		$this->_container->rewind();
		$this->assertEquals( 'en', $this->_container->key() );
	}

	public function testNext()
	{
		$this->_container->rewind();
		$this->_container->next();

		$this->assertEquals( 'de', $this->_container->key() );
	}

	public function testCurrent()
	{
		$this->_container->rewind();
		$path = $this->_testdir . DIRECTORY_SEPARATOR . 'testfile' . DIRECTORY_SEPARATOR . 'en.csv';

		$this->assertEquals( $path, $this->_container->current()->getResource() );
	}

	public function testValid()
	{
		$this->_container->rewind();
		$first = $this->_container->valid();

		foreach( $this->_container as $lang ){}

		$this->assertTrue( $first );
		$this->assertFalse( $this->_container->valid() );
	}
}
