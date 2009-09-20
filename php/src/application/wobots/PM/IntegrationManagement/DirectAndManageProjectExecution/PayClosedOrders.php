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
 * Close and pay orders which are waiting for approval
 *
 * We are searching the entire list of project orders and trying to
 * find that orders which are completed, marked as closed
 * and are ready for the payment. We find the first one, close it
 * and initiate a payment in the booking system.
 *
 * @package wobots
 */
class PayClosedOrders extends Model_Decision_PM {

    /**
     * Make decision, close the first non-yet-closed order
     *
     * @return string|false
     * @throws Exception If something happens 
     */
    protected function _make() {
        
        $orders = Model_Order::retrieveByWobot($this->wobot);

    }
    
}
