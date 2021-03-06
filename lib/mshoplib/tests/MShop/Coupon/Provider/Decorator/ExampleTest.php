<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Test class for MShop_Coupon_Provider_Decorator_Example.
 * Generated by PHPUnit on 2008-08-04 at 10:20:39.
 */
class MShop_Coupon_Provider_Decorator_ExampleTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MShop_Coupon_Provider_Decorator_Example
	 * @access protected
	 */
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

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Coupon_Provider_Decorator_ExampleTest');
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
		$priceManager = MShop_Price_Manager_Factory::createManager( $context );
		$item = MShop_Coupon_Manager_Factory::createManager( $context )->createItem();

		// Don't create order base item by createItem() as this would already register the plugins
		$this->orderBase = new MShop_Order_Item_Base_Default( $priceManager->createItem(), $context->getLocale() );

		$provider = new MShop_Coupon_Provider_Example($context, $item, 'abcd');
		$this->_object = new MShop_Coupon_Provider_Decorator_Example( $context, $item, 'abcd', $provider );
		$this->_object->setObject( $this->_object );
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
		unset( $this->orderBase );
	}


	public function testAddCoupon()
	{
		$this->_object->addCoupon( $this->orderBase );
	}

	public function testDeleteCoupon()
	{
		$this->_object->deleteCoupon( $this->orderBase );
	}

	public function testUpdateCoupon()
	{
		$this->_object->updateCoupon( $this->orderBase );
	}
}
