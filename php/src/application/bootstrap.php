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
