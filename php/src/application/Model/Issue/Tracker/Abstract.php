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
     * Cache of trackers
     *
     * @var Model_Issue_Tracker_Abstract[]
     */
    protected static $_trackers = array();

    /**
	 * Create a new tracker
     *
     * @param Model_Issue_Abstract|string Issue type of type of tracker in string
     * @param mixed Connection/configuration parameters
     * @return Model_Issue_Tracker_Abstract
     */
	public static function factory($type, $params) {
	    if ($type instanceof Model_Issue_Abstract)
	        $type = str_replace('Model_Issue_', '', get_class($type));
	    
        $className = 'Model_Issue_Tracker_' . ucfirst($type);
        $id = $className . ':' . serialize($params);
        
        if (!isset(self::$_trackers[$id]))
            self::$_trackers[$id] = new $className($params);
            
        return self::$_trackers[$id];
    }

    /**
	 * Constructor
     *
     * @param mixed Connection parameters
     * @return void
     */
	abstract public function __construct($params);

}
