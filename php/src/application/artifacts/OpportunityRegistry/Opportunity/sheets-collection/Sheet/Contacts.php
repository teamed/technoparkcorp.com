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

require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/Sheet/Abstract.php';

/**
 * Contacts
 *
 * @package Artifacts
 */
class Sheet_Contacts extends Sheet_Abstract
{

    /**
     * Defaults
     *
     * @var array
     * @see __get()
     */
    protected $_defaults = array(
        'title' => 'Custom Software Product',
        'fullname' => 'Customer',
        'company' => 'ACME Inc.',
        'position' => 'director',
        'address' => 'address n/a',
        'city' => 'New York',
        'country' => 'USA',
        'zip' => '10001',
        'phone' => '239 935 5429',
        'email' => 'sales@tpc2.com',
    );
    
    /**
     * Get name of the template file, like "Vision.tex", "ROM.tex", etc.
     *
     * @return string|null
     */
    public function getTemplateFile() 
    {
        return null;
    }

    /**
     * Get name of the template file for proposal
     *
     * @return string|null
     */
    public function getProposalFile() 
    {
        return null;
    }

}
