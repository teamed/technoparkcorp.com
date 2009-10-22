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
 * Set one milestone in front of every activity from the slice
 * 
 * @package Slice_Plugin
 */
class Slice_Plugin_AfterMilestone extends Slice_Plugin_Abstract {

    /**
     * Set one milestone in front of every activity and return a list of milestones
     *
     * @param array List of options
     * @return $this
     **/
    public function execute(array $options = array()) {

        $this->_normalizeOptions($options, array(
            'codePrefix' => 'm', // prefix to set before each new code of activity
            'sow' => 'milestone', // statement of work to set to each activity
            ));
        
        $names = array();

        // grab activities to work with
        $activities = array();
        foreach ($this as $activity)
            $activities[] = $activity;
            
        // create milestone for each activity
        foreach ($activities as $activity) {
            $milestone = $this->addMilestone($options['codePrefix'] . $this->_nextCode($options['codePrefix']))
                ->setSow($options['sow']);
            $activity->predecessors->add($milestone);
            $names[] = $milestone->name;
        }
        return $this->selectedOnly($names);
    }
        
}
