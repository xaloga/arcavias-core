<?php

/**
 * @copyright Copyright (c) 2010, Metaways Infosystems GmbH
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS order base coupon controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Order_Base_Coupon_Default extends Controller_ExtJS_Abstract implements Controller_ExtJS_Interface
{
	private $_manager = null;


	/**
	 * Initializes the Order base coupon controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Order_Base_Coupon' );

		$manager = MShop_Order_Manager_Factory::createManager( $context );
		$baseManager =  $manager->getSubManager( 'base' );
		$this->_manager = $baseManager->getSubManager( 'coupon' );
	}


	/**
	 * Creates a new order base product item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the order base product properties
	 * @return array Associative list with nodes and success value
	 */
	public function saveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$ids = array();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $this->_manager->createItem();

			if( isset( $entry->{'order.base.coupon.id'} ) ) { $item->setId( $entry->{'order.base.product.id'} ); }
			if( isset( $entry->{'order.base.coupon.baseid'} ) ) { $item->setBaseId( $entry->{'order.base.coupon.baseid'} ); }
			if( isset( $entry->{'order.base.coupon.productid'} ) ) { $item->setProductId( $entry->{'order.base.coupon.productid'} ); }
			if( isset( $entry->{'order.base.coupon.code'} ) ) { $item->setCode( $entry->{'order.base.coupon.code'} ); }


			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.coupon.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return mixed Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}
}
