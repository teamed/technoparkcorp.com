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
 * Panel URL builder
 *
 * @package helpers
 */
class Helper_PanelUrl extends FaZend_View_Helper {

    /**
     * Builds the static URL
     *
     * @param string string|false New url to use or current (if FALSE)
     * @return string HTML URL
     */
    public function panelUrl($doc = false) {
        return $this->getView()->url(array('doc' => ($doc ? $doc : $this->getView()->doc)), 'panel', true, false);
    }

}
