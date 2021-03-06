<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage Container
 */


/**
 * Common abstract class for container objects.
 *
 * @package MW
 * @subpackage Container
 */
abstract class MW_Container_Abstract
{
	private $_options;
	private $_name;


	/**
	 * Initializes the object.
	 *
	 * @param array $options Associative list of key/value pairs for configuration
	 */
	public function __construct( $name, array $options )
	{
		$this->_options = $options;
		$this->_name = $name;
	}


	/**
	 * Returns the path of the actual file.
	 *
	 * @return string Path to the actual file
	 */
	public function getName()
	{
		return $this->_name;
	}


	/**
	 * Returns all options as array.
	 *
	 * @return array Associative list of option keys and values
	 */
	protected function _getOptions()
	{
		return $this->_options;
	}


	/**
	 * Returns the configured value for the given name or the default value if nothing is configured.
	 *
	 * @param string $name Name of the configuration option
	 * @param mixed $default Default value if option is not configured
	 * @return mixed Option value
	 */
	protected function _getOption( $name, $default = null )
	{
		return ( isset( $this->_options[$name] ) ? $this->_options[$name] : $default );
	}
}
