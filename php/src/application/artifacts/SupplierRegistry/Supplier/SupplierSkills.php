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
 * Skills for a supplier, with a grade
 *
 * @package Artifacts
 */
class theSupplierSkills extends ArrayIterator {

    /**
     * Show it as a string
     *
     * @return void
     **/
    public function __toString() {
        $skills = array();
        foreach ($this as $skill)
            $skills[] = strval($skill);
        return implode('; ', $skills);
    }

    /**
     * How compliant this supplier is to the given skill?
     *
     * @param theSupplierSkill Skill to be compliant to
     * @return integer Percents in [0..100] interval
     **/
    public function getCompliance(theSupplierSkill $skill) {
        foreach ($this as $exists)
            if ($exists->isSameSkill($skill))
                return $exists->getCompliance($skill);
        return 0;
    }

    /**
     * This list of skills has this given skill?
     *
     * @param theSupplierSkill Skill to be compliant to
     * @return boolean
     **/
    public function hasSkill(theSupplierSkill $skill) {
        foreach ($this as $exists)
            if ($exists->isSameSkill($skill))
                return true;
        return false;
    }

    
}
