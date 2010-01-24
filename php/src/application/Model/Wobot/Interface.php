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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * One wobot interface
 *
 * @package Model
 */
interface Model_Wobot_Interface {

    /**
     * Text name of the wobot, e.g. 'PM.test'
     *
     * @return string
     */
    public function getFullName();

    /**
     * Calculate name of the wobot and return it, just name, e.g. 'PM'
     *
     * @return string
     */
    public function getName();

    /**
     * Calculate email of the wobot (without domain, which is always self::EMAIL_DOMAIN)
     *
     * @return string
     */
    public function getEmailPrefix();
    
    /**
     * Calculate full email of the wobot
     *
     * @return string
     */
    public function getEmail();

    /**
     * Calculate context, project name for example: 'test', 'ABC', etc.
     *
     * @return string
     */
    public function getContext();

    /**
     * Execute this wobot (make next waiting decision)
     *
     * @return string The decision just made
     */
    public function execute();

}
