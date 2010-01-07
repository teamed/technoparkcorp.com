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
