<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Catalog_Stage_Image_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite('Client_Html_Catalog_Stage_Image_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();
		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Catalog_Stage_Image_Default( $context, $paths );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );
		$catItems = $catalogManager->searchItems( $search, array('media') );

		if( ( $catItem = reset( $catItems ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}

		$view = TestHelper::getView();

		$view->stageCatPath = array( $catItem );

		$this->_object->setView( $view );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
		Controller_Frontend_Factory::clear();
		MShop_Factory::clear();
	}


	public function testGetHeader()
	{
		$output = $this->_object->getHeader();
		$this->assertEquals( '', $output );
	}


	public function testGetBody()
	{
		$output = $this->_object->getBody();
		$mediaItems = $this->_object->getView()->get( 'imageItems', array() );

		if( ( $mediaItem = reset( $mediaItems ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$this->assertEquals( 'Cafe Stage image', $mediaItem->getName() );
		$this->assertStringStartsWith( '<div class="catalog-stage-image">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}


	public function testProcess()
	{
		$this->_object->process();
	}
}
