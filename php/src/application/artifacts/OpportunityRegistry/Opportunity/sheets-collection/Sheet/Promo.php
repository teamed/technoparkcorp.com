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

require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/Sheet/Abstract.php';

/**
 * Static TeX promo pages
 *
 * @package Artifacts
 */
class Sheet_Promo extends Sheet_Abstract
{
    
    /**
     * Defaults
     *
     * @var array
     * @see __get()
     */
    protected $_defaults = array(
        'pages' => array(),
    );
    
    /**
     * Name of the promo page
     *
     * @var string|null
     */
    protected $_page;
    
    /**
     * Set name of promo page
     *
     * @param string Name of page, eg "ProjectModel" or "CompanyInfo"
     * @return $this
     * @throws Sheet_Promo_PageMissedException
     */
    public function setPage($page) 
    {
        if (!$this->_isPageValid($page)) {
            FaZend_Exception::raise(
                'Sheet_Promo_PageMissedException', 
                "Page '{$page}' not found"
            );
        }
        $this->_page = $page;
        return $this;
    }

    /**
     * Get name of the template file, like "Vision.tex", "ROM.tex", etc.
     *
     * @return string|null
     */
    public function getTemplateFile() 
    {
        if (is_null($this->_page)) {
            return null;
        }
        return 'promo/' . $this->_page . '.tex';
    }
    
    /**
     * Get name of the template file, like "Vision.tex", "ROM.tex", etc.
     *
     * @return string|null
     */
    public function getProposalFile() 
    {
        if (is_null($this->_page)) {
            return null;
        }
        return 'proposals/promo/' . $this->_page . '.tex';
    }

    /**
     * Get name of sheet, like "Vision", "ROM", etc.
     *
     * @return string
     */
    public function getSheetName() 
    {
        if (is_null($this->_page)) {
            return parent::getSheetName();
        }
        return parent::getSheetName() . $this->_page;
    }
    
    /**
     * Initialize the clas
     *
     * @return void
     */
    protected function _init() 
    {
        // this sheet is already initialized, and WON'T produce other sheets
        if (!is_null($this->_page)) {
            return;
        }
        
        foreach ($this->pages as $page) {
            $pageName = trim(str_replace(' ', '', $page->attributes()->name), "\t\n\r ");
            if (!$this->_isPageValid($pageName)) {
                logg(
                    'Promo page "%s" ignored since not found',
                    $pageName
                );
                continue;
            }
            $sheet = self::factory($this->getSheetName(), $page);
            $sheet->setPage($pageName);
            $this->sheets->add($sheet);
        }
    }
    
    /**
     * Page name is valid? The page exists?
     *
     * @param string Name of page, eg "ProjectModel" or "CompanyInfo"
     * @return boolean
     */
    protected function _isPageValid($page) 
    {
        return self::isTemplateExists('promo/' . $page . '.tex');
    }

}
