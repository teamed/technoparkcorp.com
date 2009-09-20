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
 * Identify summary project budget
 *
 * @package wobots
 */
class IdentifySummaryBudget extends Model_Decision_PM {

    /**
     * Make decision, identify summary budget
     *
     * @return string|false
     * @throws FaZend_Validator_Failure If something happens 
     */
    protected function _make() {
        
        validate()
            ->false(isset($this->project->charter->summaryBudget), 'Summary Budget already set')
            ->true($this->project->staffAssignments->hasRole('PM'), 'Role PM is not defined in the project yet');

        return $this->project->workOrders->get($this)
            ->setPerformer($this->project->staffAssignments->PM)
            ->setCloser(null)
            ->setText('to specify summary budget')
            ->setPage('Integration/Charter/setBudget')
            ->setPrice(0.1)
            ->setPriority(Model_Order::BLOCKER)
            ->execute();
        
    }
    
}
