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
 * Synchronize activities between schedule and work orders
 *
 * @package wobots
 */
class SynchronizeActivites extends Model_Decision_PM {

    /**
     * Synchronize between work orders and schedule
     *
     * @return string|false
     * @throws FaZend_Validator_Failure If something happens 
     */
    protected function _make() {
        
        validate()
            ->false($this->project->objectives->isApproved(), 'Objectives are not approved yet');

        // full list of activities
        $list = $this->project->schedule->getActivities();

        // synchronize with work orders
        $synchronized = $this->project->schedule->synchronize();        

        // draw a network diagram
        logg(count($list) . ' activities in current schedule:');
        $this->_logger->svg($list->svg()
            ->setTitle('Network Diagram')
            ->highlight($synchronized)
            ->styleNetworkDiagram()));

        // draw a gantt chart
        $this->_logger->svg($list->svg()
            ->setTitle('Gantt Chart')
            ->highlight($synchronized)
            ->styleGanttChart()));

        logg('Here is the list of them:');
        $this->_logger->table()
            ->highlight($synchronized)
            ->setSource($list);
            
    }
    
}
