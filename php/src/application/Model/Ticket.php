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
 * One multi-lingual ticket
 *
 * @package Model
 */
class Model_Ticket {

    /**
     * Name of it
     *
     * @var string
     */
    protected $_name;

    /**
     * List of params
     *
     * @var array
     */
    protected $_params;

    /**
     * Construct it
     *
     * @param string Name of the ticket (name of the file)
     * @param array Associative array of params
     * @return void
     **/
    protected function __construct($name, array $params) {
        $this->_name = $name;
        $this->_params = $params;
    }
    
    /**
     * Get simple ticket
     *
     * @param string Name of the ticket (name of the file)
     * @param array Associative array of params
     * @return Model_Ticket
     **/
    public static function factory($name, array $params) {
        return new Model_Ticket($name, $params);
    }
    
    /**
     * Make it as string
     *
     * @return string
     **/
    public function __toString() {
        return file_get_contents(APPLICATION_PATH . '/tickets/' . $this->_name . '.phtml');
    }

}
