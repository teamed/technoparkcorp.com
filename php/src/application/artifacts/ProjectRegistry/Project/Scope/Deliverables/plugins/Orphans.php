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
 * @version $Id: Queue.php 808 2010-02-28 17:58:12Z yegor256@yahoo.com $
 *
 */
 
require_once 'artifacts/ProjectRegistry/Project/Scope/Deliverables/plugins/Abstract.php';

/**
 * Full list of design elements that are NOT traceable to requirements
 *
 * @package Artifacts
 */
class Deliverables_Plugin_Orphans extends Deliverables_Plugin_Abstract
{

    /**
     * List of orphans
     *
     * @var string[]
     */
    protected $_orphans;

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
        if (!in_array($req->name, $this->_orphans)) {
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
        return count($this->_orphans);
    }
    
    /**
     * Initialize, if necessary
     *
     * @param Iterator
     * @return void
     */
    protected function _init(theDeliverables $deliverables) 
    {
        // very stupid approach, need to be fixed to 
        // find more complex chains
        $this->_orphans = array();
        foreach ($deliverables as $element) {
            if (!($element instanceof Deliverables_Design_Abstract)) {
                continue;
            }
            if ($element instanceof Deliverables_Design_Package) {
                continue;
            }
            if (!$deliverables->ps()->parent->traceability->getCoverageChains($element, 'functional')) {
                $this->_orphans[] = $element;
            }
        }
    }
    
}
