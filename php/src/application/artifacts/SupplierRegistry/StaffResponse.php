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

/**
 * One response for staff
 *
 * @package Artifacts
 */
class theStaffResponse extends ArrayIterator
{

    /**
     * Show as a string
     *
     * @return string
     **/
    public function __toString() {
        if (!count($this))
            return 'nobody';
        
        $quality = 0;
        foreach ($this as $response) {
            $quality = max($quality, $response->quality);
        }
        return $quality . '%';
    }
    
    /**
     * Hook adding function in order to sort the array on-fly
     *
     * @param mixed Index in array
     * @param mixed Value to add
     * @return void
     **/
    public function offsetSet($index, $value) 
    {
        parent::offsetSet($index, $value);
        $this->uasort(create_function('$a, $b', 'return $a->quality < $b->quality;'));
    }

}
