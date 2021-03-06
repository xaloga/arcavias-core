<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Coupon_Manager_Default.
 * Generated by PHPUnit on 2010-03-30 at 09:57:05.
 */
class MShop_Coupon_Manager_FactoryTest extends MW_Unittest_Testcase
{

	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Coupon_Manager_FactoryTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * testCreateManager().
	 */
	public function testCreateManager()
	{
		$target = 'MShop_Common_Manager_Interface';
		$manager =  MShop_Coupon_Manager_Factory::createManager( TestHelper::getContext() );
		$this->assertInstanceOf( $target, $manager );

		$manager = MShop_Coupon_Manager_Factory::createManager( TestHelper::getContext(), 'Default' );
		$this->assertInstanceOf( $target, $manager );
	}

	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException('MShop_Coupon_Exception');
		$target = 'MShop_Common_Manager_Interface';
		$manager = MShop_Coupon_Manager_Factory::createManager( TestHelper::getContext(), '%^&' );
		$this->assertInstanceOf( $target, $manager);
	}

	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException('MShop_Exception');
		$target = 'MShop_Common_Manager_Interface';
		$manager = MShop_Coupon_Manager_Factory::createManager( TestHelper::getContext(), 'test' );
		$this->assertInstanceOf( $target, $manager);
	}
}