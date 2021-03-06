<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Default service list manager for creating and handling service list items.
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Manager_List_Default
	extends MShop_Common_Manager_List_Abstract
	implements MShop_Service_Manager_List_Interface
{
	private $_searchConfig = array(
		'service.list.id' => array(
			'code' => 'service.list.id',
			'internalcode' => 'mserli."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_service_list" AS mserli ON ( mser."id" = mserli."parentid" )' ),
			'label' => 'Service list ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.list.siteid' => array(
			'code' => 'service.list.siteid',
			'internalcode' => 'mserli."siteid"',
			'label' => 'Service list site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.list.parentid' => array(
			'code' => 'service.list.parentid',
			'internalcode' => 'mserli."parentid"',
			'label' => 'Service list parent ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.list.domain' => array(
			'code' => 'service.list.domain',
			'internalcode' => 'mserli."domain"',
			'label' => 'Service list domain',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.typeid' => array(
			'code' => 'service.list.typeid',
			'internalcode' => 'mserli."typeid"',
			'label' => 'Service list type ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.list.refid' => array(
			'code' => 'service.list.refid',
			'internalcode' => 'mserli."refid"',
			'label' => 'Service list reference ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.datestart' => array(
			'code' => 'service.list.datestart',
			'internalcode' => 'mserli."start"',
			'label' => 'Service list start date',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.dateend' => array(
			'code' => 'service.list.dateend',
			'internalcode' => 'mserli."end"',
			'label' => 'Service list end date',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.config' => array(
			'code' => 'service.list.config',
			'internalcode' => 'mserli."config"',
			'label' => 'Service list config',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.position' => array(
			'code' => 'service.list.position',
			'internalcode' => 'mserli."pos"',
			'label' => 'Service list position',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'service.list.status' => array(
			'code' => 'service.list.status',
			'internalcode' => 'mserli."status"',
			'label' => 'Service list status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'service.list.ctime'=> array(
			'code'=>'service.list.ctime',
			'internalcode'=>'mserli."ctime"',
			'label'=>'Service list create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.mtime'=> array(
			'code'=>'service.list.mtime',
			'internalcode'=>'mserli."mtime"',
			'label'=>'Service list modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.editor'=> array(
			'code'=>'service.list.editor',
			'internalcode'=>'mserli."editor"',
			'label'=>'Service list editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Returns the list attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = parent::getSearchAttributes();

		if( $withsub === true )
		{
			$context = $this->_getContext();

			$path = 'classes/service/manager/list/submanagers';
			foreach( $context->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for service list extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'service', 'list/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/service/manager/list/default/item/';
	}


	/**
	 * Returns the search configuration for searching items.
	 *
	 * @return array Associative list of search keys and search definitions
	 */
	protected function _getSearchConfig()
	{
		return $this->_searchConfig;
	}
}