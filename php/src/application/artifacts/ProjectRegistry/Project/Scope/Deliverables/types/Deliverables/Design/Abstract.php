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
 * @version $Id: Package.php 729 2010-02-22 12:06:48Z yegor256@yahoo.com $
 *
 */

require_once 'artifacts/ProjectRegistry/Project/Scope/Deliverables/types/Deliverables/Abstract.php';

/**
 * One abstract design element
 *
 * @package Artifacts
 */
abstract class Deliverables_Design_Abstract extends Deliverables_Abstract
{
    
    /**
     * List of tickets (ticket names) that are waiting
     *
     * @var string[]
     */
    protected $_todoTickets;

    /**
     * Set list of tickets waiting
     *
     * @param array List of ticket names
     * @return void
     * @see DeliverablesLoaders_Design::load()
     */
    public function setTodoTickets(array $tickets) 
    {
        $this->_todoTickets = $tickets;
    }
    
}
