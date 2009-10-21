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
 * One single condition inside criteria
 *
 * @package Artifacts
 */
class theCriteriaCondition extends ArrayIterator {

    /**
     * Raw text condition
     *
     * @var string
     */
    protected $_text;

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString() {
        return $this->_text;
    }
    
    /**
     * Construct it
     *
     * @param string Condition
     * @return $this
     */
    public function __construct($text) {
        $this->_text = $text;
    }
    
    /**
     * Is it true now?
     *
     * @param theProject What is the source of metrics
     * @return boolean
     **/
    public function isTrue(theProject $project) {
        $formula = $this->_text;
        if (preg_match_all('/\[([\w\d\/]+)\]/', $formula, $matches)) {
            foreach ($matches[0] as $id=>$match) {
                try {
                    $metric = $project->metrics[$matches[1][$id]];
                } catch (MetricNotFound $e) {
                    return false;
                }
                $formula = str_replace($match, $metric->value, $formula);
            }
        }
        eval("\$result = {$formula};");
        return $result;
    }

    /**
     * Return criteria in HTML form
     *
     * @param theProject What is the source of metrics
     * @param array List of metrics mentioned, it will be updated
     * @return string
     **/
    public function asHtml(theProject $project, array &$variables) {

        $text = $this->_text;
        if (!preg_match_all('/\[([\w\d\/]+)\]/', $text, $matches))
            return $text;
            
        foreach ($matches[0] as $id=>$match) {
            try {
                $metric = $project->metrics[$matches[1][$id]];
            } catch (MetricNotFound $e) {
                $text = str_replace($match, '{' . $matches[1][$id] . '}', $text);
                continue;
            }
            
            $text = str_replace($match, '<b>' . $this->_newVariable($variables, $metric) . '</b>', $text);
        }
        
        return $text;
    }

    /**
     * Get new variable name for this metric
     *
     * @param array List of variables/metrics already there
     * @param Metric_Abstract The metric to add to the array
     * @return string
     **/
    protected function _newVariable(&$variables, Metric_Abstract $metric) {
        $name = 'A';
        foreach ($variables as $id=>$variable) {
            if ($variable == $metric)
                return $id;
                
            if ($id >= $name)
                $name = $id++;
        }
              
        $variables[$name] = $metric;      
                
        return $name;
    }

}
