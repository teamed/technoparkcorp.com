<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoParck Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2008
 * @version $Id$
 */

/**
 * Bootstraper
 *
 * @package application
 */
class Bootstrap extends FaZend_Application_Bootstrap_Bootstrap {

    /**
     * Initialize autoloader for artifacts
     *
     * @return void
     */
    protected function _initAutoLoader() {

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->pushAutoloader(new Model_Loader_Artifacts(), 'the');

    }

    /**
     * Initialize SHARED library for connecting to FaZend
     *
     * @return void
     */
    protected function _initSharedLib() {
        $this->bootstrap('Fazend');
        
        Model_Project::setClassName('Model_Project');
        Shared_Cache::setLifecycle(5 * 60); // 5 hours cache lifecycle
    }

}

// total amount of seconds in day
define('SECONDS_IN_DAY', (24*60*60));
define('SECONDS_IN_HOUR', (60*60));
define('SECONDS_IN_MINUTE', 60);

define('CONTENT_PATH', realpath(APPLICATION_PATH . '/../content'));

/**
 * Simplified access point to FaZend_Log
 *
 * @param string Message to log
 * @return void
 */
function logg($message) {
    FaZend_Log::info($message);
}
