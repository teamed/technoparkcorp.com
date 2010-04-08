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
 * Document name segment
 *
 * @package helpers
 */
class Helper_Segment extends FaZend_View_Helper
{

    /**
     * Returns segment of document name
     *
     * @param int Number of segment
     * @return string|int
     */
    public function segment($num = null)
    {
        $exp = explode('/', $this->getView()->doc);

        if (is_null($num))
            $segment = array_pop($exp);
        else {
            if ($num < 0)
                $num = count($exp) + $num - 1;
            $segment = $exp[$num];
        }

        if (is_numeric($segment))
            $segment = (int)$segment;

        return $segment;
    }

}
