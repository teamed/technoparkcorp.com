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
 * Panel URL builder
 *
 * @package helpers
 */
class Helper_PanelUrl extends FaZend_View_Helper 
{

    /**
     * Builds the URL for a panel document
     *
     * @param string string|false New url to use or current (if FALSE)
     * @return string URL
     */
    public function panelUrl($doc = false) 
    {
        return $this->getView()->url(array(
                'doc' => ($doc ? $doc : $this->getView()->doc)
            ), 'panel', true, false);
    }

}
