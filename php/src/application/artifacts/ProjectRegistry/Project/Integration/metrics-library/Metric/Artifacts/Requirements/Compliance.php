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
 * Requirements compliance to our writing convention
 * 
 * @see http://fazend.com/a/2009-12-WikiNotation.html
 * @package Artifacts
 */
class Metric_Artifacts_Requirements_Compliance extends Metric_Abstract 
{

    /**
     * Forwarders
     *
     * @var array
     */
    protected $_patterns = array(
        '/(\w+)/' => 'element',
        );

    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload() 
    {
        if ($this->_getOption('element'))
            return $this->_value = rand(0, 1);
        
        // types of elements in SRS and their value in total compliance
        $types = array(
            'glossary' => 5,
            'actors' => 1,
            'interfaces' => 1,
            'functional' => 4,
            'qos' => 3,
            );
            
        $compliance = array();
        $this->_value = 0;
        foreach (array_keys($types) as $type) {
            if (!isset($compliance[$type]))
                $compliance[$type] = array();
            foreach ($this->_project->deliverables->$type as $requirement) {
                $compliance[$type][$requirement->name] = 
                    $this->_project->metrics['artifacts/requirements/compliance/' . $requirement->name]->value;
            }
        }
        
        foreach ($types as $type=>$weight) {
            if (count($compliance[$type]) > 0)
                $compliance[$type] = $weight * (array_sum($compliance[$type]) / count($compliance[$type]));
            else
                $compliance[$type] = 0;
        }
        
        $this->_value = round(array_sum($compliance) / array_sum($types), 2);
    }
                
}
