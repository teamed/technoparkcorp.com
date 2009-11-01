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
 * One skill for a supplier
 *
 * @package Artifacts
 */
class theSupplierSkill extends FaZend_Db_Table_ActiveRow_skill implements Model_Artifact_Interface {

    /**
     * Create new skill
     *
     * @param theSupplier Owner of the skill
     * @param string Text name of the skill
     * @param integer Level of the skill
     * @return theSupplierSkill
     **/
    public static function create(theSupplier $supplier, $name, $level) {
        validate()
            ->type($level, 'integer', "Skill level must be INTEGER")
            ->true($level <= 100 && $level >= 0, "Level must be in [0..100] interval, {$level} provided");
        
        $skill = new theSupplierSkill();
        $skill->supplier = $supplier;
        $skill->name = $name;
        $skill->level = $level;
        $skill->save();
        return $skill;
    }

    /**
     * Return all skills for the given supplier
     *
     * @param theSupplier Owner of the skill
     * @return theSupplierSkill[]
     */
    public static function retrieveBySupplier(theSupplier $supplier) {
        return self::retrieve()
            ->where('supplier = ?', (string)$supplier)
            ->setRowClass('theSupplierSkill')
            ->fetchAll();
    }
    
    /**
     * Get list of default levels
     *
     * @return array
     **/
    public static function getDefaultLevels() {
        return array(
            25 => 'beginner',
            50 => 'average',
            75 => 'intermediate',
            100 => 'professional',
        );
    }

}
