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
 * Total number of functional requiremnts
 * 
 * @package Artifacts
 */
class Metric_Requirements_Functional_Total extends Metric_Abstract {

    protected $_patterns = array(
        '/level(\d+)/' => 'level',
        '/level(\d+)\/(\w+)/' => 'level, status',
        );

    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload() {
        if ($this->_getOption('level')) {
            $this->_value = 999;
            $this->_default = $this->_project->metrics['requirements/functional/total']->target * 5;
            return;
        }
        
        $this->_value = 7;
        $this->_default = 12;
    }
        
}
