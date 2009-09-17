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
 * Collection of projects
 *
 * @package Artifacts
 */
class theProjectRegistry extends Model_Artifact {

    /**
     * Create new project
     *
     * @param string Name of the project to create
     * @return theProject
     */
    public function createNewProject($name) {

        $this->_validator
            ->type($name, 'string', 'Project name should be string')
            ->regexp($name, '/^\w{4,12}$/', 'Invalid project name')
            ->false(isset($this[$name]), 'Project "' . $name . '" already exists');

        FaZend_Log::info("New project '{$name}' created");

        return $this[$name] = new theProject();

    }

}
