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
 * Database of suppliers in trac PMO
 *
 * @package Model
 */
class Model_Asset_Suppliers_Fazend_Trac extends Model_Asset_Suppliers_Abstract
{
    
    const QUERY_ALL = 'status=closed&supplier!=&resolution=approve';
    const QUERY_SINGLE = 'status=closed&resolution=approve&supplier=';

    /**
     * Instance of Shared_Trac
     *
     * @var string
     */
    protected $_trac;
    
    /**
     * Initializer
     *
     * @return void
     */
    protected function _init() 
    {
        parent::_init();
        $this->_trac = new Shared_Trac($this->_project);
    }
    
    /**
     * Get full list of suppliers (emails)
     *
     * @return string[]
     */
    public function retrieveAll() 
    {
        $list = $this->_trac->query(self::QUERY_ALL);
        
        $emails = array();
        foreach ($list as $ticket) {
            $attributes = $ticket->getAttributes();
            $emails[$attributes['supplier']] = true;
        }
        return array_keys($emails);
    }
    
    /**
     * Get full details of supplier by email
     *
     * @param string Email of the supplier
     * @param theSupplier Object to fill with data
     * @return mixed
     */
    public function deriveByEmail($email, theSupplier $supplier) 
    {
        $list = $this->_trac->query(self::QUERY_SINGLE . $email);

        foreach ($list as $ticket) {
            $attributes = $ticket->getAttributes();
            
            $skills = explode(',', $attributes['skills']);
            foreach ($skills as $skill)
                $supplier->addSkill(trim($skill));
                
            $supplier->addRole($attributes['role']);
            $supplier->setRate(new FaZend_Bo_Money($attributes['price']));
            $supplier->setApprovedOn(new Zend_Date(strtotime($attributes['date'])));
        }
    }
    
}
