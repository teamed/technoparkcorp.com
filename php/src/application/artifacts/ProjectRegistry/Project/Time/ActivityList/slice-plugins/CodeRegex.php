<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

require_once 'artifacts/ProjectRegistry/Project/Time/ActivityList/slice-plugins/Abstract.php';

/**
 * Sector of the array, by regular expression
 * 
 * @package Slice_Plugin
 */
class Slice_Plugin_CodeRegex extends Slice_Plugin_Abstract {

    /**
     * Regular expression
     *
     * @var string
     */
    protected $_regex;

    /**
     * Show only activities which codes match regex
     *
     * @param theActivity Activity to check
     * @return boolean
     **/
    protected function _isInside(theActivity $activity) {
        return preg_match($this->_regex, $activity->code);
    }
        
    /**
     * Get a sector of this slice
     *
     * @param string Mask
     * @return Slice_Plugin_CodeRegex
     **/
    public function execute($regex) {
        $this->_regex = $regex;
        return $this;
    }
        
}
