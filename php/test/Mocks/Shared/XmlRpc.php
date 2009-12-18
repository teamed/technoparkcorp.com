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
 * One mock for all client calls (to Trac, Wiki and Pan)
 *
 * @package Mocks
 */
class Mocks_Shared_XmlRpc 
{

    /**
     * Get proxy
     *
     * @return object
     **/
    public function getProxy($name) 
    {
        return $this;
    }

    /**
     * Get full list of wiki pages
     *
     * @return array
     **/
    public function getAllPages() 
    {
        return array();
    }

    /**
     * Get wiki page in HTML
     *
     * @param sting Name of the page
     * @return string
     **/
    public function getPageHTML($name) 
    {
        return '';
    }

    /**
     * Trac query
     *
     * @param sting Query to make
     * @return string
     **/
    public function query($query) 
    {
        return array();
    }

    /**
     * Trac create ticket
     *
     * @return integer Ticket ID
     **/
    public function create($summary, $description, $params, $smth) 
    {
        return 1;
    }

    /**
     * Trac update one ticket
     *
     * @return void
     **/
    public function update($id, $summary, $params, $smth) 
    {
        // ...
    }

    /**
     * Get ticket change log
     *
     * @param integer Ticket ID
     * @return array
     **/
    public function changeLog($id) 
    {
        return array();
    }

    /**
     * Get ticket info
     *
     * @param integer Ticket ID
     * @return array
     **/
    public function get($id) 
    {
        return array();
    }

}
