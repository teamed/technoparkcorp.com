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
 * @version $Id$
 *
 */

/**
 * Activity list in a project
 *
 * @package Artifacts
 */
class theActivityList extends Model_Artifact_Bag implements Model_Artifact_Passive
{

    /**
     * It is loaded already?
     *
     * @return boolean
     */
    public function isLoaded()
    {
        return $this->_passiveLoader()->isLoaded();
    }
    
    /**
     * Initialize the list
     *
     * @return void
     */
    public function reload()
    {
        $this->_passiveLoader()->reload();
        
        // reload them explicitly
        $this->activities->reload();
    }
    
    /**
     * Create class loader
     *
     * The method creates a standalone loader, which is responsible
     * for adding elements to $this. Also this loader knows how to
     * understand whether $this is loaded now or not.
     *
     * @return Model_Artifact_Passive_Loader
     * @see isLoaded()
     * @see reload()
     **/
    protected function _passiveLoader() 
    {
        return Model_Artifact_Passive_Loader::factory($this)
            ->attach('activities', new theActivities(), 'setActivityList');
    }
    
}
