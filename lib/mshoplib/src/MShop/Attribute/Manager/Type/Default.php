<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Attribute
 */


/**
 * Default attribute type manager for creating and handling attribute type items.
 * @package MShop
 * @subpackage Attribute
 */
class MShop_Attribute_Manager_Type_Default
	extends MShop_Common_Manager_Type_Abstract
	implements MShop_Attribute_Manager_Type_Interface
{
	private $_searchConfig = array(
		'attribute.type.id' => array(
			'label' => 'Attribute type ID',
			'code' => 'attribute.type.id',
			'internalcode' => 'mattty."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_attribute_type" AS mattty ON ( matt."typeid" = mattty."id" )' ),
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.type.siteid' => array(
			'code' => 'attribute.type.siteid',
			'internalcode' => 'mattty."siteid"',
			'label' => 'Attribute type site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.type.code' => array(
			'label' => 'Attribute type code',
			'code' => 'attribute.type.code',
			'internalcode' => 'mattty."code"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.type.domain' => array(
			'label' => 'Attribute type domain',
			'code' => 'attribute.type.domain',
			'internalcode' => 'mattty."domain"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.type.label' => array(
			'code' => 'attribute.type.label',
			'internalcode' => 'mattty."label"',
			'label' => 'Attribute type label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.type.status' => array(
			'code' => 'attribute.type.status',
			'internalcode' => 'mattty."status"',
			'label' => 'Attribute type status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'attribute.type.ctime'=> array(
			'code'=>'attribute.type.ctime',
			'internalcode'=>'mattty."ctime"',
			'label'=>'Attribute type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.type.mtime'=> array(
			'code'=>'attribute.type.mtime',
			'internalcode'=>'mattty."mtime"',
			'label'=>'Attribute type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.type.editor'=> array(
			'code'=>'attribute.type.editor',
			'internalcode'=>'mattty."editor"',
			'label'=>'Attribute type editor',
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
		$list = parent::getSearchAttributes();

		if( $withsub === true )
		{
			$context = $this->_getContext();

			$path = 'classes/attribute/manager/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for attribute type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'attribute', 'type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/attribute/manager/type/default/item/';
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