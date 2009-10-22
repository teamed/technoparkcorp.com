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
 * One milestone
 *
 * @package Artifacts
 */
class theMilestone extends theActivity {

    /**
     * Date of milestone
     *
     * @var Zend_Date
     */
    protected $_date;

    /**
     * Factory method
     *
     * @param theActivities Holder of this activity
     * @param theWorkPackage Originator of the activity
     * @param string Unique code for this work package
     * @return theMilestone
     **/
    public static function factoryMilestone(theActivities $activities, $wp, $code) {
        return new theMilestone($activities, $wp, $code);
    }

    /**
     * Reload the milestone date
     *
     * @return void
     **/
    public function reload() {
        $this->_date = parent::_getStart();
        $this->_date->add(1, Zend_Date::MONTH);
    }

    /**
     * Get estimated start of the activity
     *
     * @return Zend_Date
     */
    protected function _getStart() {
        if (!isset($this->_date))
            $this->reload();
        return clone $this->_date;
    }

}
