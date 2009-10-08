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
 * Iterate activities
 * 
 * @package Slice_Plugin
 */
class Slice_Plugin_Iterate_DownCurve extends Slice_Plugin_Abstract {

    /**
     * Iterate
     *
     * @return mixed
     **/
    public function execute(array $options = array()) {
        $this->rewind();
        $activity = $this->current();
        $total = Model_Cost::factory($activity->cost);
        $this->delete($activity);
        
        // the smallest decrement we can afford
        $decrement = Model_Cost::factory($options['minCost'])->divide(2.4);
        $cost = Model_Cost::factory($options['maxCost']);
        $i = 0;
        
        do {
            $this->add($i++)
                ->setCost($cost)
                ->setSow($options['sow']);
                
            $total->deduct($cost);
            $cost->deduct($decrement);
            
            // if the cost we are going to deduct in the next
            // activity is too small
            // if ($total->lessThan(Model_Cost::factory($cost)->add(Model_Cost::factory($options['minCost']))))
                // $cost->set($total);
        } while ($total->greaterThan());
        
        return $i;
    }
        
}
