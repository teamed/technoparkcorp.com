<?php
/**
 *
 * Copyright (c) FaZend.com
 * All rights reserved.
 *
 * You can use this product "as is" without any warranties from authors.
 * You can change the product only through Google Code repository
 * at http://code.google.com/p/fazend
 * If you have any questions about privacy, please email privacy@fazend.com
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
