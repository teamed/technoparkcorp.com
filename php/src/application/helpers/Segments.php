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
 * Document name segments, grouped into line
 *
 * @package helpers
 */
class Helper_Segments extends FaZend_View_Helper {

    /**
     * Returns segments of document name
     *
     * @param int Start with
     * @param int End with
     * @param string Separator
     * @return string
     */
    public function segments($start, $end = null, $separator = '/') {

        $exp = explode('/', $this->getView()->doc);

        if (!$end)
            $end = count($exp) - $start;

        // cut the segment
        $exp = array_slice($exp, $start, $end);

        return implode($separator, $exp);

    }

}
