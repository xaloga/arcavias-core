<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Price
 */


/**
 * Default implementation of a price manager.
 *
 * @package MShop
 * @subpackage Price
 */
class MShop_Price_Manager_Default
	extends MShop_Price_Manager_Abstract
	implements MShop_Price_Manager_Interface
{
	private $_searchConfig = array(
		'price.id' => array(
			'code' => 'price.id',
			'internalcode' => 'mpri."id"',
			'label' => 'Price ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'price.siteid' => array(
			'code' => 'price.siteid',
			'internalcode' => 'mpri."siteid"',
			'label' => 'Price site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'price.typeid' => array(
			'label' => 'Price type ID',
			'code' => 'price.typeid',
			'internalcode' => 'mpri."typeid"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'price.currencyid' => array(
			'code' => 'price.currencyid',
			'internalcode' => 'mpri."currencyid"',
			'label' => 'Price currency code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.domain' => array(
			'code' => 'price.domain',
			'internalcode' => 'mpri."domain"',
			'label' => 'Price domain',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.label' => array(
			'code' => 'price.label',
			'internalcode' => 'mpri."label"',
			'label' => 'Price label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.quantity' => array(
			'code' => 'price.quantity',
			'internalcode' => 'mpri."quantity"',
			'label' => 'Price quantity',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'price.value' => array(
			'code' => 'price.value',
			'internalcode' => 'mpri."value"',
			'label' => 'Price regular value',
			'type' => 'decimal',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.costs' => array(
			'code' => 'price.costs',
			'internalcode' => 'mpri."costs"',
			'label' => 'Price shipping costs',
			'type' => 'decimal',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.rebate' => array(
			'code' => 'price.rebate',
			'internalcode' => 'mpri."rebate"',
			'label' => 'Price rebate amount',
			'type' => 'decimal',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.taxrate' => array(
			'code' => 'price.taxrate',
			'internalcode' => 'mpri."taxrate"',
			'label' => 'Price tax in percent',
			'type' => 'decimal',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.status' => array(
			'code' => 'price.status',
			'internalcode' => 'mpri."status"',
			'label' => 'Price status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'price.mtime'=> array(
			'code'=>'price.mtime',
			'internalcode'=>'mpri."mtime"',
			'label'=>'Price modification date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.ctime'=> array(
			'code'=>'price.ctime',
			'internalcode'=>'mpri."ctime"',
			'label'=>'Price creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.editor'=> array(
			'code'=>'price.editor',
			'internalcode'=>'mpri."editor"',
			'label'=>'Price editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$path = 'classes/price/manager/submanagers';
			foreach( $this->_getContext()->getConfig()->get( $path, array( 'type', 'list' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Instantiates a new price item object.
	 *
	 * @return MShop_Price_Item_Interface
	 */
	public function createItem()
	{
		$locale = $this->_getContext()->getLocale();
		$values = array( 'siteid' => $locale->getSiteId() );

		if( $locale->getCurrencyId() !== null ) {
			$values['currencyid'] = $locale->getCurrencyId();
		}

		return $this->_createItem($values);
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$path = 'mshop/price/manager/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the price item object specificed by its ID.
	 *
	 * @param integer $id Unique price ID referencing an existing price
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Price_Item_Interface $item Returns the price item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'price.id', $id, $ref );
	}


	/**
	 * Saves a price item object.
	 *
	 * @param MShop_Price_Item_Interface $item Price item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 *
	 * @throws MShop_Price_Exception If price couldn't be saved
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Price_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Price_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		$context = $this->_getContext();
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$dbname = $config->get( 'resource/default', 'db' );
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();

			if( $id === null ) {
				$path = 'mshop/price/manager/default/item/insert';
			} else {
				$path = 'mshop/price/manager/default/item/update';
			}

			$stmt = $this->_getCachedStatement($conn, $path);

			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getTypeId() );
			$stmt->bind( 3, $item->getCurrencyId() );
			$stmt->bind( 4, $item->getDomain() );
			$stmt->bind( 5, $item->getLabel() );
			$stmt->bind( 6, $item->getQuantity(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 7, $item->getValue() );
			$stmt->bind( 8, $item->getCosts() );
			$stmt->bind( 9, $item->getRebate() );
			$stmt->bind(10, $item->getTaxRate() );
			$stmt->bind(11, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind(12, date('Y-m-d H:i:s', time()));//mtime
			$stmt->bind(13, $context->getEditor());

			if( $id !== null ) {
				$stmt->bind( 14, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind(14, date('Y-m-d H:i:s', time()));//ctime
			}

			$result = $stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/price/manager/default/item/newid';
					$item->setId( $this->_newId( $conn, $config->get( $path, $path ) ) );
				} else {
					$item->setId( $id );
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * Possible search keys: 'price.id', 'price.currencyid', 'price.quantity',
	 *  'price.value','price.costs', 'price.rebate', 'price.taxrate', 'price.status'.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Price_Item_Interface
	 *
	 * @throws MShop_Price_Exception If creating items failed
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$map = $typeIds = array();
		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$dbname = $context->getConfig()->get( 'resource/default', 'db' );
		$conn = $dbm->acquire( $dbname );

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/price/manager/default/item/search';
			$cfgPathCount =  'mshop/price/manager/default/item/count';
			$required = array( 'price' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$map[ $row['id'] ] = $row;
				$typeIds[ $row['typeid'] ] = null;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		if( !empty( $typeIds ) )
		{
			$typeManager = $this->getSubManager( 'type' );
			$typeSearch = $typeManager->createSearch();
			$typeSearch->setConditions( $typeSearch->compare( '==', 'price.type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[ $row['typeid'] ] ) ) {
					$map[$id]['type'] = $typeItems[ $row['typeid'] ]->getCode();
				}
			}
		}

		return $this->_buildItems( $map, $ref, 'price' );
	}


	/**
	 * creates a search object and sets base criteria
	 *
	 * @param boolean $default Prepopulate object with default criterias
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch( $default = false )
	{
		if( $default === true )
		{
			$object = $this->_createSearch( 'price' );
			$currencyid = $this->_getContext()->getLocale()->getCurrencyId();

			if( $currencyid !== null )
			{
				$expr[] = $object->compare( '==', 'price.currencyid', $currencyid );
				$expr[] = $object->getConditions();

				$object->setConditions( $object->combine( '&&', $expr ) );
			}

			return $object;
		}

		return parent::createSearch();
	}


	/**
	 * Returns a new manager for price extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g type, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'price', $manager, $name );
	}


	/**
	 * Creates a new price item
	 *
	 * @param array $values List of attributes for price item
	 * @return MShop_Price_Item_Interface New price item
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Price_Item_Default( $values );
	}

}
