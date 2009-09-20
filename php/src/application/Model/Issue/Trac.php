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
 * One issue in Trac
 *
 * @package Model
 */
class Model_Issue_Trac extends Model_Issue_Abstract {

    /**
     * Make sure the issue exists. If not - create it right now
     *
     * @return $this
     **/
    public function makeAlive() {
        return $this;
    }
    
    /**
     * Make sure the issue exists AND is open right now. If not, do the necessary actions.
     *
     * @return $this
     **/
    public function makeOpen() {
        return $this;
    }
    
    /**
     * Save all changes made
     *
     * @return $this
     **/
    protected function _save() {
        return $this;
    }
    
}
