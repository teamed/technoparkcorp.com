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
 * One abstract tracker
 *
 * @package Model
 */
abstract class Model_Issue_Tracker_Abstract extends FaZend_StdObject {

    /**
	 * Create a new tracker
     *
     * @param Model_Issue_Abstract|string Issue type of type of tracker in string
     * @param mixed Connection/configuration parameters
     * @return Model_Issue_Tracker_Abstract
     */
	public static function factory($type, $params) {
        $className = 'Model_Issue_Tracker_' . ucfirst($type);
        return Model_Flyweight::factory($className, $params);
    }

    /**
     * Find one issue by id
     *
     * @param string Unique ID of the issue
     * @return Model_Issue_Abstract
     **/
    public function find($id) {
        $className = 'Model_Issue_' . $this->getType();
        return Model_Flyweight::factory($className, $this, $id);
    }
    
    /**
     * Get type of tracker, e.g. 'trac'
     *
     * @return string
     **/
    public function getType() {
        return str_replace('Model_Issue_Tracker_', '', get_class($this));
    }
        
}
