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
 * Total cost of all activities
 * 
 * @package Slice_Plugin
 */
class Slice_Plugin_Sum extends Slice_Plugin_Abstract {

    /**
     * Summarize them
     *
     * @return FaZend_Bo_Money
     **/
    public function execute() {
        $sum = new FaZend_Bo_Money();
        foreach ($this as $activity) {
            if ($activity->cost instanceof FaZend_Bo_Money)
                $sum->add($activity->cost); 
        }
        return $sum;
    }
        
}
