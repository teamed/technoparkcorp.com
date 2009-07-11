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
 * Static URL builder
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package FaZend 
 */
class Helper_StaticUrl extends FaZend_View_Helper {

    /**
     * Builds the static URL
     *
     * @return string
     */
    public function staticUrl($page) {
        return $this->getView()->url(array('page'=>$page), 'static', true, false);    
    }

}
