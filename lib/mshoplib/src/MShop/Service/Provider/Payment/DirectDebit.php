<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Payment provider for direct debit orders.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Payment_DirectDebit
	extends MShop_Service_Provider_Payment_Abstract
	implements MShop_Service_Provider_Payment_Interface
{
	private $_feConfig = array(
		'directdebit.accountowner' => array(
			'code' => 'directdebit.accountowner',
			'internalcode'=> 'accountowner',
			'label'=> 'Account owner',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true
		),
		'directdebit.accountno' => array(
			'code' => 'directdebit.accountno',
			'internalcode'=> 'accountno',
			'label'=> 'Account number',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true
		),
		'directdebit.bankcode' => array(
			'code' => 'directdebit.bankcode',
			'internalcode'=> 'bankcode',
			'label'=> 'Bank code',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true
		),
		'directdebit.bankname' => array(
			'code' => 'directdebit.bankname',
			'internalcode'=> 'bankname',
			'label'=> 'Bank name',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true
		),
	);


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the frontend.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigFE( MShop_Order_Item_Base_Interface $basket )
	{
		$list = array();
		$feconfig = $this->_feConfig;

		try
		{
			$address = $basket->getAddress();

			if( ( $fn = $address->getFirstname() ) !== '' && ( $ln = $address->getLastname() ) !== '' ) {
				$feconfig['directdebit.accountowner']['default'] = $fn . ' ' . $ln;
			}
		}
		catch( MShop_Order_Exception $e ) { ; }

		foreach( $feconfig as $key => $config ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $config );
		}

		return $list;
	}


	/**
	 * Checks the frontend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes entered by the customer during the checkout process
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigFE( array $attributes )
	{
		return $this->_checkConfig( $this->_feConfig, $attributes );
	}

	/**
	 * Sets the payment attributes in the given service.
	 *
	 * @param MShop_Order_Item_Base_Service_Interface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 */
	public function setConfigFE( MShop_Order_Item_Base_Service_Interface $orderServiceItem, array $attributes )
	{
		parent::setConfigFE( $orderServiceItem, $attributes );

		$attributeItems = $orderServiceItem->getAttributes();
		$manager = MShop_Factory::createManager( $this->_getContext(), 'order/base/service/attribute' );

		if( ( $attrItem = $orderServiceItem->getAttributeItem( 'directdebit.accountno' ) ) !== null )
		{
			$ordBaseAttrItem = $manager->createItem();
			$ordBaseAttrItem->setType( $attrItem->getType() . '/hidden' );
			$ordBaseAttrItem->setCode( $attrItem->getCode() . '/hidden' );
			$ordBaseAttrItem->setValue( $attrItem->getValue() );

			$attributeItems[] = $ordBaseAttrItem;

			$value = $attrItem->getValue();
			$attrItem->setValue( str_repeat( 'X', strlen( $value ) - 3 ) . substr( $value, -3 ) );
		}

		$orderServiceItem->setAttributes( $attributeItems );
	}


	/**
	 * Tries to get an authorization or captures the money immediately for the given order if capturing the money
	 * separately isn't supported or not configured by the shop owner.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 * @return MW_Common_Form_Interface Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider or to a local success page)
	 */
	public function process( MShop_Order_Item_Interface $order )
	{
		$order->setPaymentStatus( MShop_Order_Item_Abstract::PAY_AUTHORIZED );

		return parent::process( $order );
	}
}