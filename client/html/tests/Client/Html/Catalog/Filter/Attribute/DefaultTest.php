<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Catalog_Filter_Attribute_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Catalog_Filter_Attribute_Default( TestHelper::getContext(), $paths );
		$this->_object->setView( TestHelper::getView() );
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
		$this->_object->getHeader();
	}


	public function testGetBody()
	{
		$output = $this->_object->getBody();

		$this->assertContains( '<fieldset class="attr-size">', $output );
		$this->assertContains( '<fieldset class="attr-length">', $output );
		$this->assertContains( '<fieldset class="attr-width">', $output );
		$this->assertContains( '<fieldset class="attr-color">', $output );
	}


	public function testGetBodyCategory()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-catalog-id' => -1 ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetBodySearchText()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-search-text' => 'test' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetBodySearchAttributeList()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-attr-id' => array( -1, -2 ) ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetBodySearchAttributeString()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-attr-id' => '-1 -2' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}

}
