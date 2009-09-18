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
 * Document name segment
 *
 * @package helpers
 */
class Helper_Segment extends FaZend_View_Helper {

    /**
     * Returns segment of document name
     *
     * @param int Number of segment
     * @return string|int
     */
    public function segment($num = null) {

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
