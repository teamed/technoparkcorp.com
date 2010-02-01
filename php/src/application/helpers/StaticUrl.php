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
 * @version $Id$
 * @category FaZend
 */

/**
 * Static URL builder
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package FaZend 
 */
class Helper_StaticUrl extends FaZend_View_Helper
{

    /**
     * Builds the static URL
     *
     * @return string
     */
    public function staticUrl($page)
    {
        return $this->getView()->url(array('page'=>$page), 'static', true, false);    
    }

}
