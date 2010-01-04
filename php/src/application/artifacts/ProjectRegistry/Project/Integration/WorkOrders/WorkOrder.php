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
 * Project work order
 *
 * @package Artifacts
 */
class theWorkOrder implements Model_Artifact_Stateless {

    /**
     * Issue responsible for this order
     *
     * @var Model_Asset_Defects_Issue_Abstract
     */
    protected $_issue;

    /**
     * Create artifact using project
     *
     * @param theWorkOrders Holder of this particular work order
     * @param string Name of decision
     * @param string ID of decision
     * @return void
     **/
    public function __construct(theWorkOrders $workOrders, $decision, $id) {
        $this->_project = Model_Project::findByName($project->name);
        $this->_issue = Model_Asset_Defects_Issue_Abstract::factory(
            $this->_project->tracker, 
            $project->name, 
            $decision . ':' . $id);
                
    }
        
    /**
     * This order is paid?
     *
     * @return boolean
     **/
    public function isPaid() {
        return true;
    }
    
    /**
     * Execute
     *
     * @return boolean
     **/
    public function create() {
        $this->_issue
            ->setAssignTo($this->_performer)
            ->setReportedBy($this->_decision->wobot->fullEmail)
            ->setPriority($this->_priority)
            ->setPrice($this->_price)
            ->makeAlive()
            ->makeOpen();
    }
        
}
