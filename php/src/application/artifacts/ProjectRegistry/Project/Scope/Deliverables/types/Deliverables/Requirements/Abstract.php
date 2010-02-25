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
 * @version $Id: Actor.php 729 2010-02-22 12:06:48Z yegor256@yahoo.com $
 *
 */

require_once 'artifacts/ProjectRegistry/Project/Scope/Deliverables/types/Deliverables/Abstract.php';

/**
 * One abstract requirement
 *
 * @package Artifacts
 */
class Deliverables_Requirements_Abstract extends Deliverables_Abstract
{

        /**
         * This requirement is out of scope?
         *
         * @return boolean
         */
        protected function _getOutOfScope() 
        {
            return (bool)$this->attributes['out']->value;
        }

}
