<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * Abstract loader of deliverables
 *
 * @package Artifacts
 */
abstract class DeliverablesLoaders_Abstract
{
    
    /**
     * Where to load
     *
     * @var theDeliverables
     */
    protected $_deliverables;
    
    /**
     * Static plugin loader
     *
     * @var Zend_Loader_PluginLoader
     */
    protected static $_loader;
     
    /**
     * Create new loader
     *
     * @param string Name of plugin to create
     * @param theDeliverables The holder which has to be filled with new items
     * @return DeliverablesLoaders_Abstract
     **/
    public static function factory($name, theDeliverables $deliverables)
    {
        if (!isset(self::$_loader)) {
            self::$_loader = new Zend_Loader_PluginLoader(array('DeliverablesLoaders_' => dirname(__FILE__)));
        }
        // load this plugin, if possible
        $pluginName = self::$_loader->load($name);
        return new $pluginName($deliverables);        
    }
     
    /**
     * Construct it
     *
     * @param theDeliverables The holder which has to be filled with new items
     * @return void
     **/
    protected function __construct(theDeliverables $deliverables)
    {
        $this->_deliverables = $deliverables;
    }
    
    /**
     * Retrieve an array of all loaders, initialized
     *
     * @param theDeliverables The holder which has to be filled with new items
     * @return DeliverablesLoaders_Abstract[]
     **/
    public static function retrieveAll(theDeliverables $deliverables)
    {
        $list = array();
        foreach (new DirectoryIterator(dirname(__FILE__)) as $file) {
            if ($file->isDot())
                continue;
            $name = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            if ($name == 'Abstract')
                continue;
            if (!preg_match('/^\w+$/', $name))
                continue;
            $list[] = self::factory($name, $deliverables);
        }
        return $list;
    }
    
    /**
     * Load all deliverables
     *
     * @return void
     **/
    abstract public function load();
    
}
