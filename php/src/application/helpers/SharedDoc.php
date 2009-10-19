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
 * Share this document
 *
 * @package helpers
 */
class Helper_SharedDoc extends FaZend_View_Helper {

    /**
     * Builds a link of a shared document
     *
     * @param string Link to use
     * @param theStakeholder Who will access it
     * @param boolean Clean all links to this document
     * @return string
     */
    public function sharedDoc($document, theStakeholder $stakeholder, $clean = true) {

        $shortcut = Model_Shortcut::create($stakeholder->getEmail(), $document, $clean);
        
        return $this->getView()->longUrl(array(
            'doc'=>$shortcut->getHash()), 'shared', true);

    }

}
