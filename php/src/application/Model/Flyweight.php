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
 * Flyweight Factory
 *
 * You can use it for storage of simple objects, that don't need to be 
 * created every time from scratch. Example:
 *
 * <code>
 * $object = Model_Flyweight::factory('theStakeholder', $this, 'me@example.com');
 * </code>
 *
 * The instance of class theStakeholder won't be created again if it
 * already exists in the factory. The instance will be returned.
 *
 * @package Model
 */
class Model_Flyweight {

    /**
     * Storage of objects
     *
     * @var Model_Artifact_Stateless
     */
    static protected $_storage = array();

    /**
     * Instantiate and return an object
     *
     * @param string Name of the class to create
     * @param mixed Any amount of params to be passed to the constructor
     * @return Model_Artifact_Stateless
     **/
    public static function factory($class /*, many params... */) {
        $args = func_get_args();
        array_shift($args); // pop out the first argument
        
        // unique object ID in the storage
        $id = self::_makeId($class, $args);
        
        // if it's already here - return it
        if (isset(self::$_storage[$id]))
            return self::$_storage[$id];
        
        // initialize validator with dynamic list of params
        $call = '$object = new $class(';
        for ($i=0; $i<count($args); $i++)
            $call .= ($i > 0 ? ', ' : false) . "\$args[{$i}]";
        $call .= ');';
        eval($call);
        
        return self::$_storage[$id] = $object;
    }

    /**
     * Generate ID out of a list of params
     *
     * @param string Name of the class
     * @param array List of args
     * @return string
     */
    public static function _makeId($class, array $args) {
        $args[] = $class;
        $id = '';
        foreach ($args as $arg) {
            if (is_scalar($arg))
                // kill this SPECIAL symbol from scalar arguments
                $arg = str_replace('.', '\.', $arg);
            else
                $arg = '.' . spl_object_hash($arg);
            $id .= '.' . $arg;
        }
        return $id;
    }
    
}
