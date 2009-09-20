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
 * Order made by PM
 *
 * @package Model
 */
class Model_Order_PM extends Model_Order_Abstract {

    /**
     * Constructor
     *
     * @param Model_Decision Initiator of the order
     * @param string Id of the order
     * @return void
     */
    public function __construct(Model_Decision $decision, $id) {
        validate()
            ->instanceOf($decision, 'Model_Decision_PM', 'This kind of order can be created only by PM Decision');
        
        // create the class by parent constructor
        parent::__construct($decision, $id);
        
        // retrieve the ISSUE related to this order
        $this->_issue = Model_Issue::factory($decision->project->tracker, $decision, $id);
    }
    
    /**
     * Execute order and return text summary of its execution
     *
     * @return string
     **/
    public function execute() {

        // make sure it exists and is configured
        $this->_issue
            ->setAssignTo($this->_performer)
            ->setReportedBy($this->_decision->wobot->fullEmail)
            ->setPriority($this->_priority)
            ->setPrice($this->_price)
            ->makeAlive()
            ->makeOpen();
        
        $this->_issue->
        
    }

}
