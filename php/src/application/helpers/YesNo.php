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
 * Convert boolean into yes or no
 *
 * @package helpers
 */
class Helper_YesNo {

    /**
     * Builds the mark
     *
     * @return string
     */
    public function yesNo($bool) {

        return "<span style='color:" . ($bool ? Model_Colors::GREEN : Model_Colors::RED). ";'>" . 
            ($bool ? 'yes' : 'no') . '</span>';

    }

}
