<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 */

/**
 * Submanager for catalog.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Index_Price_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Catalog_Manager_Index_Price_Interface
{
	private $_productManager;
	private $_submanagers = array();

	private $_searchConfig = array(
		'catalog.index.price.id' => array(
			'code'=>'catalog.index.price.id',
			'internalcode'=>'mcatinpr."priceid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_catalog_index_price" AS mcatinpr ON mcatinpr."prodid" = mpro."id"' ),
			'label'=>'Product index price ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.index.price.quantity' => array(
			'code'=>'catalog.index.price.quantity',
			'internalcode'=>'mcatinpr."quantity"',
			'label'=>'Product price quantity',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.index.price.value' => array(
			'code'=>'catalog.index.price.value()',
			'internalcode'=>':site AND mcatinpr."listtype" = $1 AND mcatinpr."currencyid" = $2 AND mcatinpr."type" = $3 AND mcatinpr."value"',
			'label'=>'Product price value, parameter(<list type code>,<currency ID>,<price type code>)',
			'type'=> 'decimal',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'sort:catalog.index.price.value' => array(
			'code'=>'sort:catalog.index.price.value()',
			'internalcode'=>'mcatinpr."value"',
			'label'=>'Sort product price value, parameter(<list type code>,<currency ID>,<price type code>)',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'catalog.index.price.costs' => array(
			'code'=>'catalog.index.price.costs()',
			'internalcode'=>':site AND mcatinpr."listtype" = $1 AND mcatinpr."currencyid" = $2 AND mcatinpr."type" = $3 AND mcatinpr."costs"',
			'label'=>'Product (shipping) costs, parameter(<list type code>,<currency ID>,<price type code>)',
			'type'=> 'decimal',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'sort:catalog.index.price.costs' => array(
			'code'=>'sort:catalog.index.price.costs()',
			'internalcode'=>'mcatinpr."costs"',
			'label'=>'Sort product (shipping) costs, parameter(<list type code>,<currency ID>,<price type code>)',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'catalog.index.price.rebate' => array(
			'code'=>'catalog.index.price.rebate()',
			'internalcode'=>':site AND mcatinpr."listtype" = $1 AND mcatinpr."currencyid" = $2 AND mcatinpr."type" = $3 AND mcatinpr."rebate"',
			'label'=>'Product price rebate, parameter(<list type code>,<currency ID>,<price type code>)',
			'type'=> 'decimal',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'sort:catalog.index.price.rebate' => array(
			'code'=>'sort:catalog.index.price.rebate()',
			'internalcode'=>'mcatinpr."rebate"',
			'label'=>'Sort product price rebate, parameter(<list type code>,<currency ID>,<price type code>)',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'catalog.index.price.taxrate' => array(
			'code'=>'catalog.index.price.taxrate()',
			'internalcode'=>':site AND mcatinpr."listtype" = $1 AND mcatinpr."currencyid" = $2 AND mcatinpr."type" = $3 AND mcatinpr."taxrate"',
			'label'=>'Product price taxrate, parameter(<list type code>,<currency ID>,<price type code>)',
			'type'=> 'decimal',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'sort:catalog.index.price.taxrate' => array(
			'code'=>'sort:catalog.index.price.taxrate()',
			'internalcode'=>'mcatinpr."taxrate"',
			'label'=>'Sort product price taxrate, parameter(<list type code>,<currency ID>,<price type code>)',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
	);


	/**
	 * Initializes the manager instance.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$this->_productManager = MShop_Product_Manager_Factory::createManager( $context );


		$site = $context->getLocale()->getSitePath();
		$types = array( 'siteid' => MW_DB_Statement_Abstract::PARAM_INT );

		$search = $this->createSearch();
		$expr = array(
			$search->compare( '==', 'siteid', null ),
			$search->compare( '==', 'siteid', $site ),
		);
		$search->setConditions( $search->combine( '||', $expr ) );

		$string = $search->getConditionString( $types, array( 'siteid' => 'mcatinpr."siteid"' ) );

		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.price.quantity'], 'mcatinpr."siteid"', $site );
		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.price.value'], 'mcatinpr."siteid"', $site );
		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.price.costs'], 'mcatinpr."siteid"', $site );
		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.price.rebate'], 'mcatinpr."siteid"', $site );
		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.price.taxrate'], 'mcatinpr."siteid"', $site );


		$confpath = 'mshop/catalog/manager/index/price/default/submanagers';

		foreach( $context->getConfig()->get( $confpath, array() ) as $domain ) {
			$this->_submanagers[ $domain ] = $this->getSubManager( $domain );
		}
	}


	/**
	 * Counts the number products that are available for the values of the given key.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @param string $key Search key (usually the ID) to aggregate products for
	 * @return array List of ID values as key and the number of counted products as value
	 */
	public function aggregate( MW_Common_Criteria_Interface $search, $key )
	{
		return $this->_aggregate( $search, $key, 'mshop/catalog/manager/index/default/aggregate' );
	}


	/**
	 * Creates new price item object.
	 *
	 * @return MShop_Price_Item_Interface New product item
	 */
	public function createItem()
	{
		return $this->_productManager->createItem();
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		return $this->_productManager->createSearch( $default );
	}


	/**
	 * Removes multiple items from the index.
	 *
	 * @param array $ids list of Product IDs
	 */
	public function deleteItems( array $ids )
	{
		if( empty( $ids ) ) { return; }

		foreach( $this->_submanagers as $submanager ) {
			$submanager->deleteItems( $ids );
		}

		$path = 'mshop/catalog/manager/index/price/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ), true, 'prodid' );
	}


	/**
	 * Returns the price item for the given ID
	 *
	 * @param integer $id Id of item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Product_Item_Interface Returns the product item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_productManager->getItem( $id, $ref );
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		$list = array_merge( $list, $this->_productManager->getSearchAttributes( $withsub ) );

		if( $withsub === true )
		{
			foreach( $this->_submanagers as $submanager ) {
				$list = array_merge( $list, $submanager->getSearchAttributes( $withsub ) );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for product extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'catalog', 'index/price/' . $manager, $name );
	}


	/**
	 * Optimizes the index if necessary.
	 * Execution of this operation can take a very long time and shouldn't be
	 * called through a web server enviroment.
	 */
	public function optimize()
	{
		$context = $this->_getContext();
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$dbname = $config->get( 'resource/default', 'db' );
		$conn = $dbm->acquire( $dbname );

		try
		{
			$path = 'mshop/catalog/manager/index/price/default/optimize';
			foreach( $config->get( $path, array() ) as $sql ) {
				$conn->create( $sql )->execute()->finish();
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}


		foreach( $this->_submanagers as $submanager ) {
			$submanager->optimize();
		}
	}


	/**
	 * Removes all entries not touched after the given timestamp in the catalog index.
	 * This can be a long lasting operation.
	 *
	 * @param string $timestamp Timestamp in ISO format (YYYY-MM-DD HH:mm:ss)
	 */
	public function cleanupIndex( $timestamp )
	{
		$context = $this->_getContext();
		$siteid = $context->getLocale()->getSiteId();


		$dbm = $context->getDatabaseManager();
		$dbname = $context->getConfig()->get( 'resource/default', 'db' );
		$conn = $dbm->acquire( $dbname );

		try
		{
			$this->_begin();

			$stmt = $this->_getCachedStatement( $conn, 'mshop/catalog/manager/index/price/default/cleanup' );

			$stmt->bind( 1, $timestamp ); // ctime
			$stmt->bind( 2, $siteid, MW_DB_Statement_Abstract::PARAM_INT );

			$stmt->execute()->finish();

			$this->_commit();
			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$this->_rollback();
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		foreach ( $this->_submanagers as $submanager ) {
			$submanager->cleanupIndex( $timestamp );
		}
	}


	/**
	 * Rebuilds the catalog index price for searching products or specified list of products.
	 * This can be a long lasting operation.
	 *
	 * @param array $items Associative list of product IDs and items implementing MShop_Product_Item_Interface
	 */
	public function rebuildIndex( array $items = array() )
	{
		if( empty( $items ) ) { return; }

		MW_Common_Abstract::checkClassList( 'MShop_Product_Item_Interface', $items );

		$context = $this->_getContext();
		$siteid = $context->getLocale()->getSiteId();
		$editor = $context->getEditor();
		$date = date( 'Y-m-d H:i:s' );


		$dbm = $context->getDatabaseManager();
		$dbname = $context->getConfig()->get( 'resource/default', 'db' );
		$conn = $dbm->acquire( $dbname );

		try
		{
			foreach( $items as $item )
			{
				$listTypes = array();
				foreach( $item->getListItems( 'price' ) as $listItem ) {
					$listTypes[ $listItem->getRefId() ][] = $listItem->getType();
				}

				$stmt = $this->_getCachedStatement( $conn, 'mshop/catalog/manager/index/price/default/item/insert' );

				foreach( $item->getRefItems( 'price' ) as $refId => $refItem )
				{
					if( !isset( $listTypes[$refId] ) )
					{
						$msg = sprintf( 'List type for price item with ID "%1$s" not available', $refId );
						throw new MShop_Catalog_Exception( $msg );
					}

					foreach( $listTypes[$refId] as $listType )
					{
						$stmt->bind( 1, $item->getId(), MW_DB_Statement_Abstract::PARAM_INT );
						$stmt->bind( 2, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
						$stmt->bind( 3, $refId, MW_DB_Statement_Abstract::PARAM_INT );
						$stmt->bind( 4, $refItem->getCurrencyId() );
						$stmt->bind( 5, $listType );
						$stmt->bind( 6, $refItem->getType() );
						$stmt->bind( 7, $refItem->getValue() );
						$stmt->bind( 8, $refItem->getCosts() );
						$stmt->bind( 9, $refItem->getRebate() );
						$stmt->bind( 10, $refItem->getTaxRate() );
						$stmt->bind( 11, $refItem->getQuantity(), MW_DB_Statement_Abstract::PARAM_INT );
						$stmt->bind( 12, $date );//mtime
						$stmt->bind( 13, $editor );
						$stmt->bind( 14, $date );//ctime

						try {
							$result = $stmt->execute()->finish();
						} catch( MW_DB_Exception $e ) { ; } // Ignore duplicates
					}
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}


		foreach( $this->_submanagers as $submanager ) {
			$submanager->rebuildIndex( $items );
		}
	}


	/**
	 * Stores a new item in the index.
	 *
	 * @param MShop_Common_Item_Interface $item Product item
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$this->rebuildIndex( array( $item->getId() => $item ) );
	}


	/**
	 * Searches for items matching the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Total number of items matched by the given criteria
	 * @return array List of items implementing MShop_Product_Item_Interface with ids as keys
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = $ids = array();
		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$dbname = $context->getConfig()->get( 'resource/default', 'db' );
		$conn = $dbm->acquire( $dbname );

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/catalog/manager/index/price/default/item/search';
			$cfgPathCount =  'mshop/catalog/manager/index/price/default/item/count';
			$required = array( 'product' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )	{
				$ids[] = $row['id'];
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$search = $this->_productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.id', $ids ) );
		$products = $this->_productManager->searchItems( $search, $ref, $total );

		foreach( $ids as $id )
		{
			if( isset( $products[$id] ) ) {
				$items[ $id ] = $products[ $id ];
			}
		}

		return $items;
	}
}