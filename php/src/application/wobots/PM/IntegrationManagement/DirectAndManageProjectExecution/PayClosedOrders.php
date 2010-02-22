<?php
/**
 * thePanel v2.0, Project Management Software Toolkit
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are PROHIBITED without prior written permission from 
 * the author. This product may NOT be used anywhere and on any computer 
 * except the server platform of TechnoPark Corp. located at 
 * www.technoparkcorp.com. If you received this code occasionally and 
 * without intent to use it, please report this incident to the author 
 * by email: privacy@technoparkcorp.com or by mail: 
 * 568 Ninth Street South 202, Naples, Florida 34102, USA
 * tel. +1 (239) 935 5429
 *
 * @author Yegor Bugayenko <egor@tpc2.com>
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
class PayClosedOrders extends Model_Decision_PM
{

    /**
     * Make decision, close the first non-yet-closed order
     *
     * @return string|false
     * @throws Exception If something happens 
     */
    protected function _make() 
    {
        // go through the list of all orders
        // foreach ($this->_project->workOrders as $order) {
        //     
        //     // skip the paid orders
        //     if ($order->isPaid()) {
        //         logg("Order {$order} was paid already");
        //         continue;
        //     }
        //     
        //     // validate that it's already finished and confirmed
        //     if (!$order->isDelivered()) {
        //         logg("Order {$order} is not delivered ye, won't pay");
        //         continue;
        //     }
        //         
        //     // pay it
        //     $order->pay();
        //     return "Order {$order} was paid";
        //         
        // }
    }
    
}
