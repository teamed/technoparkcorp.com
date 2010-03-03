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
 * @copyright Copyright (c) FaZend.com
 * @version $Id: YesNo.php 611 2010-02-07 07:43:45Z yegor256@yahoo.com $
 * @category FaZend
 */

/**
 * Returns HTTP GET param, if exists
 *
 * @package helpers
 */
class Helper_HttpGet
{

    /**
     * Returns HTTP GET param, if exists
     *
     * @return string
     */
    public function httpGet($name)
    {
        return Zend_Controller_Front::getInstance()->getRequest()->get($name);
    }

}
