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
 * Assign activities to certain performer(s)
 *
 * @package Artifacts
 */
class ActivitySplitter_Assign extends theActivitySplitterAbstract {
            
    /**
     * Split this list
     *
     * @return void
     **/
    public function split(theActivities $activities) {
        $role = $this->_wbs->translateIni($this->_config->role);
        
        // maybe nobody still for this WP in the staff assignments
        if (!isset($this->_project()->staffAssignments[$role]))
            return;
        
        $person = $this->_project()->staffAssignments[$role];
        
        if ($person instanceof theProjectRole)
            $person = $person->random();
        
        foreach ($activities as $activity)
            $activity->setPerformer($person);
    }

}
