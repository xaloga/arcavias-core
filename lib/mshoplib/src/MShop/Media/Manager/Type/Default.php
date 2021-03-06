<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Media
 */


/**
 * Default media type manager for creating and handling media type items.
 * @package MShop
 * @subpackage Media
 */
class MShop_Media_Manager_Type_Default
	extends MShop_Common_Manager_Type_Abstract
	implements MShop_Media_Manager_Type_Interface
{
	private $_searchConfig = array(
		'media.type.id' => array(
			'label' => 'Media type ID',
			'code' => 'media.type.id',
			'internalcode' => 'mmedty."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_media_type" AS mmedty ON ( mmed."typeid" = mmedty."id" )' ),
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.type.siteid' => array(
			'label' => 'Media type site ID',
			'code' => 'media.type.siteid',
			'internalcode' => 'mmedty."siteid"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.type.code' => array(
			'label' => 'Media type code',
			'code' => 'media.type.code',
			'internalcode' => 'mmedty."code"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.type.domain' => array(
			'label' => 'Media type domain',
			'code' => 'media.type.domain',
			'internalcode' => 'mmedty."domain"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.type.label' => array(
			'label' => 'Media type label',
			'code' => 'media.type.label',
			'internalcode' => 'mmedty."label"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.type.status' => array(
			'label' => 'Media type status',
			'code' => 'media.type.status',
			'internalcode' => 'mmedty."status"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'media.type.ctime'=> array(
			'code'=>'media.type.ctime',
			'internalcode'=>'mmedty."ctime"',
			'label'=>'Media type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.type.mtime'=> array(
			'code'=>'media.type.mtime',
			'internalcode'=>'mmedty."mtime"',
			'label'=>'Media type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.type.editor'=> array(
			'code'=>'media.type.editor',
			'internalcode'=>'mmedty."editor"',
			'label'=>'Media type editor',
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

			$path = 'classes/media/manager/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for media type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'media', 'type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/media/manager/type/default/item/';
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