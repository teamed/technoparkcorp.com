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
 * One order
 *
 * @package Model
 */
class Model_Order {

    // priorities
    const BLOCKER = 1;
    const CRITICAL = 2;
    const MAJOR = 3;
    const MINOR = 4;

    /**
	 * Create new order of a certain type
     *
     * @param Model_Decision Decision, that initiates the order
     * @param string Unique id for this decision
     * @return Model_Order_Abstract
     */
	public static function factory(Model_Decision $decision, $id) {
        $className = 'Model_Order_' . $decision->wobot->name;
        return new $className($decision, $id);
    }

    /**
	 * Retrieve full list of orders created by this wobot
     *
     * @param Model_Wobot Author of orders
     * @return Model_Order[]
     */
	public static function retrieveByWobot(Model_Wobot $wobot) {
	    Model_Issue::retrieveBy
	    
	}
	
}
