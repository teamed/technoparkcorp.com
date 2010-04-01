<?php
/**
 * thePanel v2.0, Project Management Software Toolkit
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are PROHIBITED without prior written permission from 
 * the author. This product may NOT be used anywhere and on any computer 
 * except the server platform of TechnoPark Corp. located at 
 * www.technoparkcorp.com. If you received this code occasionally and 
 * without intent to use it, please report this incident to the author 
 * by email: privacy@technoparkcorp.com or by mail: 
 * 568 Ninth Street South 202, Naples, Florida 34102, USA
 * tel. +1 (239) 935 5429
 *
 * @author Yegor Bugayenko <egor@tpc2.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * Abstract deliverables plugin
 *
 * @package Artifacts
 */
abstract class Deliverables_Plugin_Abstract extends FilterIterator implements Countable
{
    
    /**
     * Construct the class
     *
     * @param Iterator Data to use
     * @return void
     */
    public function __construct(Iterator $iterator)
    {
        parent::__construct($iterator);
        $this->_init($iterator);
    }
    
    /**
     * Create new plugin, if possible
     *
     * @param string Name of plugin
     * @param theDeliverables Deliverables to manage
     * @return Deliverables_Plugin_Abstract
     * @throws Deliverables_Plugin_InvalidPluginException
     */
    public static function factory($name, theDeliverables $deliverables) 
    {
        $className = 'Deliverables_Plugin_' . ucfirst($name);
        $fileName = dirname(__FILE__) . '/' . ucfirst($name) . '.php';
        if (!file_exists($fileName)) {
            FaZend_Exception::raise(
                'Deliverables_Plugin_InvalidPluginException',
                "Plugin '{$name}' not found"
            );
        }
        require_once $fileName;
        return new $className($deliverables);
    }
    
    /**
     * Accept current element?
     *
     * @return boolean
     * @see FilterIterator::accept()
     */
    public function accept()
    {
        return false;
    }
    
    /**
     * Total amount of elements
     *
     * @return void
     * @see Countable::count()
     */
    public function count() 
    {
        return 0;
    }
    
    /**
     * Initialize, if necessary
     *
     * @param Iterator
     * @return void
     */
    protected function _init(theDeliverables $iterator) 
    {
        assert($iterator instanceof theDeliverables); // for ZCA only
    }
        
}
