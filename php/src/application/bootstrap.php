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
 * @category Supplementary
 * @package Functions
 */
function logg($message) {
    try {
        FaZend_Log::info($message);
    } catch (Zend_Log_Exception $e) {
        echo '<p>Log missed: ' . $message . '</p>';
    }
}

/**
 * Get ticket and return it's string value
 *
 * @return string
 */
function ticket($name, array $params = array()) {
    return (string) Model_Ticket::factory($name, $params);
}

/**
 * Return string with plural/singular inside
 *
 * @param string Input line with metas
 * @param integer Variable to substitute
 * @return string
 * @category Supplementary
 * @package Functions
 */
function plural($str, $var) {
    $src = array('[s]', '[are]', '[do]', '[have]', '[were]', '[ies]', '[es]', '[people]');
    $singular = array('', 'is', 'does', 'has', 'was', 'y', 'es', 'person');
    $plural = array('s', 'are', 'do', 'have', 'were', 'ies', '', 'people');

    return str_replace($src,
        abs($var) != 1 ? $plural : $singular, $str);
}

