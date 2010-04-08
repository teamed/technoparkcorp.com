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
 * Interface to "Pan" in LINUX.FAZEND.COM
 *
 * @package Model
 */
class Model_Asset_Code_Fazend_Pan extends Model_Asset_Code_Abstract
{
    
    /**
     * Shared_Pan connector
     *
     * @var Shared_Pan
     * @see _init()
     */
    protected $_pan;
    
    /**
     * Reintegrate
     *
     * @param string Script unique name
     * @param string Bash script
     * @return mixed
     * @throws Model_Asset_Design_Fazend_Pan_SoapFailure
     * @see ReintegrateBranches
     */
    public function reintegrate($key, $script) 
    {
        try {
            return $this->_pan->reintegrate($key, $script);
        } catch (Shared_Pan_SoapFailure $e) {
            FaZend_Exception::raise(
                'Model_Asset_Design_Fazend_Pan_SoapFailure',
                $e->getMessage()
            );
        }
        return false;
    }
    
    /**
     * Initializer
     *
     * @return void
     */
    protected function _init() 
    {
        $this->_pan = new Shared_Pan($this->_project);
    }

    
}
