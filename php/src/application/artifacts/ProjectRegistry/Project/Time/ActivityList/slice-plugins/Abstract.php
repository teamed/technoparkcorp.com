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
 * Abstract plugin
 * 
 * @package Slice_Plugin
 */
abstract class Slice_Plugin_Abstract extends ArrayIterator {

    /**
     * Static plugin loader
     *
     * @var Zend_Loader_PluginLoader
     */
    protected static $_loader;
     
    /**
     * Create new plugin, using list of activities
     *
     * @return void
     **/
    public static function factory($name, array $list) {
        if (!isset(self::$_loader)) {
            self::$_loader = new Zend_Loader_PluginLoader(array('Slice_Plugin_' => dirname(__FILE__)));
        }
        // load this plugin, if possible
        $sliceName = self::$_loader->load($name);
        return new $sliceName($list);        
    }
     
    /**
     * Save list of activities to work with
     *
     * @param array List of activities
     * @return void
     **/
    public function __construct(array $activities) {
        foreach ($activities as $activity)
            $this[] = $activity;
    }
        
    /**
     * Call sub-slice
     *
     * @return Slice_Plugin_Abstract
     **/
    public function __call($method, array $args) {
        $slice = self::factory($method, $this->getArrayCopy());
        if (method_exists($slice, 'execute'))
            return call_user_func_array(array($slice, 'execute'), $args);
        return $slice;
    }
        
}
