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
 
require_once 'artifacts/ProjectRegistry/Project/Scope/Deliverables/plugins/Abstract.php';

/**
 * Implementation queue
 *
 * @package Artifacts
 */
class Deliverables_Plugin_Queue extends Deliverables_Plugin_Abstract
{

    /**
     * List of terminates to use
     *
     * @var string[]
     */
    protected $_terminates;

    /**
     * Accept ready-for implementation requirements
     *
     * @return boolean
     * @see FilterIterator::accept()
     */
    public function accept()
    {
        $req = $this->current();

        // @see _init()
        if (!in_array($req->name, $this->_terminates)) {
            return false;
        }

        return true;
    }
        
    /**
     * Total amount of elements
     *
     * @return void
     * @see Countable::count()
     */
    public function count() 
    {
        return count($this->_terminates);
    }
    
    /**
     * Initialize, if necessary
     *
     * @param Iterator
     * @return void
     */
    protected function _init(Iterator $iterator) 
    {
        $terminates = array();
        foreach ($iterator as $req) {
            if (!($req instanceof Deliverables_Requirements_Requirement_Functional)) {
                continue;
            }

            // implemented already
            if ($req->isImplemented()) {
                return false;
            }

            // parent is there? we should kill it
            if ($req->getLevel() && isset($terminates[$req->parentName])) {
                unset($terminates[$req->parentName]);
            }
            
            // kid already there?
            $kidFound = false;
            foreach (array_keys($terminates) as $kid) {
                if (strpos($kid, $req->name . '.') === 0) {
                    $kidFound = true;
                }
            }
            if ($kidFound) {
                continue;
            }
            $terminates[$req->name] = $req;
        }
        
        $this->_terminates = array_keys($terminates);
    }
    
}
