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
 * Criteria of activity closure
 *
 * @package Artifacts
 */
class theActivityCriteria extends ArrayIterator {

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString() {
        return (string)implode('; ', $this->getArrayCopy());
    }
    
    /**
     * Condition to attach
     *
     * @param string Condition
     * @return $this
     */
    public function when($condition) {
        $args = func_get_args();
        $this[] = new theCriteriaCondition(call_user_func_array('sprintf', $args));
        return $this;
    }

    /**
     * Is it true now?
     *
     * @param theProject What is the source of metrics
     * @return boolean
     **/
    public function isTrue(theProject $project) {
        foreach ($this as $when) {
            if (!$when->isTrue($project))
                return false;
        }
        return true;
    }
    
    /**
     * Return criteria in HTML form
     *
     * @param theProject What is the source of metrics
     * @return string
     **/
    public function asHtml(theProject $project) {
        $html = '<p>All of the below shall be true:</p><ul>';

        $metrics = array();
        foreach ($this as $when) {
            $true = $when->isTrue($project);
            $html .= '<li><span class="formula">' . $when->asHtml($project, $metrics) . '</span>' . 
                ' (<span style="color: ' . ($true ? Model_Colors::GREEN : Model_Colors::RED) . '">' . 
                ($true ? 'true' : 'false') . '</span>)</li>';
        }
        $html .= '</ul>';
        
        foreach ($metrics as $var=>$metric)
            $html .= "<h2>Variable <span class='formula'><b>$var</b> = {$metric->value}</span></h2>" . 
                '<p>' . $metric->name . '</p>';
        
        return $html;
    }

    /**
     * Return a list of all metrics involved here, that affect this activity
     *
     * @return string[]
     **/
    public function getAffectors() {
        $metrics = array();
        foreach ($this as $when) {
            $metrics += $when->getAffectors();
        }
        return array_unique($metrics);
    }
}
