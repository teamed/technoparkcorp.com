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
 * @copyright Copyright (c) TechnoPark Corp., 2001-2008
 * @version $Id$
 */

class Model_Cost extends FaZend_Bo_Money
{
}

/**
 * Bootstraper
 *
 * @package application
 */
class Bootstrap extends FaZend_Application_Bootstrap_Bootstrap
{
    
    /**
     * Init forma() helper
     *
     * @return void
     */
    protected function _initFormaHelper() 
    {
        FaZend_View_Helper_Forma::setLabelSuffixes(
            array(
                true => '<span style="color:red">*</span>:',
                false => ':',
            )
        );
    }
    
    /**
     * Init non-explicit ORM mapping rules
     *
     * @return void
     */
    protected function _initOrmMapping() 
    {
        $converters = array(
            'dates' => array(
                'regexs' => array(
                    '/^payment\.date$/',
                ),
                'converter' => 'new Zend_Date(${a1}, Zend_Date::ISO_8601)'
            ),
            'costs' => array(
                'regexs' => array(
                    '/^payment\.(?:amount|volume|balance|paid|earned)$/',
                ),
                'converter' => 'FaZend_Bo_Money::convertFromCents(${a1})'
            ),
        );
        
        foreach ($converters as $converter) {
            foreach ($converter['regexs'] as $regex) {
                FaZend_Db_Table_ActiveRow::addMapping($regex, $converter['converter']);
            }
        }
    }
    
    /**
     * Initialize autoloader for artifacts
     *
     * @return void
     */
    protected function _initAutoLoaders() 
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->pushAutoloader(new Model_Loader_Artifacts(), 'the');
        
        // some artifacts have their own loader
        theMetrics::initAutoloader();
        theDeliverables::initAutoloader();
        theSheetsCollection::initAutoloader();
    }

    /**
     * Initialize Model_Texry
     *
     * @return void
     */
    protected function _initTexry() 
    {        
        Model_Texry::addTemplateDir(APPLICATION_PATH . '/tikz');
    }
    
    /**
     * Initialize SHARED library for connecting to FaZend
     *
     * @return void
     */
    protected function _initSharedLib() 
    {        
        require_once 'Model/Project.php';
        Model_Project::setClassName('Model_Project');

        require_once 'Shared/Cache.php';
        Shared_Cache::setLifecycle(5 * 60); // 5 hours cache lifecycle
    }

    /**
     * Initialize POS
     *
     * @return void
     */
    protected function _initPos() 
    {
        // make sure all artifacts are attached to OUR root
        FaZend_Pos_Properties::setRootClass('Model_Artifact_Root');
    }

    /**
     * Localize, if necessary
     *
     * @return void
     */
    protected function _initLocalization() 
    {
        $locale = new Zend_Locale();
        Zend_Registry::set('Zend_Locale', $locale);
        
        $translate = new Zend_Translate(
            'gettext', 
            realpath(APPLICATION_PATH . '/../languages'), 
            null,
            array(
                'ignore' => '.',
                'scan' => Zend_Translate::LOCALE_FILENAME,
                'disableNotices' => true,
            )
        );
        Zend_Registry::set('Zend_Translate', $translate);
        
        if (!$translate->isAvailable($locale->getLanguage())) {
            // not available languages are rerouted to another language
            $translate->setLocale('en');
        }
    }
    
    /**
     * Make corrections to global logger
     *
     * @return void
     **/
    protected function _initGlobalLogger() 
    {
        // do it after fazend only
        $this->bootstrap('Fazend');
        
        // filter out all INFO messages
        if (APPLICATION_ENV === 'production') {
            FaZend_Log::getInstance()->getWriter('ErrorLog')
                ->addFilter(new Zend_Log_Filter_Priority(Zend_Log::ERR));    
        }
    }

}

// total amount of seconds in day
define('SECONDS_IN_DAY', (24*60*60));
define('SECONDS_IN_HOUR', (60*60));
define('SECONDS_IN_MINUTE', 60);

define('CONTENT_PATH', realpath(APPLICATION_PATH . '/../content'));

/**
 * Return string with plural/singular inside
 *
 * @param string Input line with metas
 * @param integer Variable to substitute
 * @return string
 * @category Supplementary
 * @package Functions
 */
function plural($str, $var) 
{
    $src = array('[s]', '[are]', '[do]', '[have]', '[were]', '[ies]', '[es]', '[people]');
    $singular = array('', 'is', 'does', 'has', 'was', 'y', 'es', 'person');
    $plural = array('s', 'are', 'do', 'have', 'were', 'ies', '', 'people');

    return str_replace(
        $src,
        abs($var) != 1 ? $plural : $singular, 
        $str
    );
}
