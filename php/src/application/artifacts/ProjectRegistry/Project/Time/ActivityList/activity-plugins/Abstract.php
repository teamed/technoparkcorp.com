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
 * Abstract activity plugin
 * 
 * @package Activity_Plugin
 */
abstract class Activity_Plugin_Abstract
{

    /**
     * Activity to work with
     *
     * @var theActivity
     */
    protected $_activity;

    /**
     * Issue
     *
     * @var Model_Asset_Defects_Issue_Abstract
     */
    protected $_issue;

    /**
     * Static plugin loader
     *
     * @var Zend_Loader_PluginLoader
     */
    protected static $_loader;
     
    /**
     * Create new plugin
     *
     * @param string Name of plugin to create
     * @param theActivity Activity to work with
     * @return void
     */
    public static function factory($name, theActivity $activity) 
    {
        if (!isset(self::$_loader)) {
            self::$_loader = new Zend_Loader_PluginLoader(array('Activity_Plugin_' => dirname(__FILE__)));
        }
        // load this plugin, if possible
        $pluginName = self::$_loader->load($name);
        return new $pluginName($activity);        
    }
     
    /**
     * Construct it
     *
     * @param theActivity Activity to work with
     * @return void
     */
    public function __construct(theActivity $activity) 
    {
        $this->_activity = $activity;
        $this->_issue = $activity->project->fzProject()
            ->getAsset(Model_Project::ASSET_DEFECTS)->findByCode($activity->name);
    }
                            
}
