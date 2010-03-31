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
 * Project payments
 *
 * @package Artifacts
 */
class thePayments implements Model_Artifact_Stateless
{
       
    /**
     * Holder of the class
     *
     * @var theProject
     */
    public $project;
    
    /**
     * Create new payment specific for some user
     *
     * @param string Email of the user
     * @param string Original amount of payment, like '125 EUR'
     * @param string Context, for example name of project
     * @param string Details of the payment
     * @return thePayment
     */
    public function createSpecific($user, $original, $context, $details)
    {
        return thePayment::create($user, null, $original, $context, $details);
    }
        
    /**
     * Create new payment WITHOUT info about user, just context
     *
     * @param string Original amount of payment, like '125 EUR'
     * @param string Context, for example name of project
     * @param string Details of the payment
     * @return thePayment
     */
    public function createGeneric($original, $context, $details)
    {
        return thePayment::create(null, null, $original, $context, $details);
    }
        
}
