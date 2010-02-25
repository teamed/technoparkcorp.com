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
 * @version $Id$
 *
 */

/**
 * Total number of functional requiremnts, accepted!
 * 
 * @package Artifacts
 */
class Metric_Artifacts_Requirements_Functional_Accepted extends Metric_Abstract
{
    
    const DEFAULT_TOTAL = 250;

    /**
     * Forwarders
     *
     * @var array
     * @see Metric_Abstract::$_patterns
     */
    protected $_patterns = array(
        '/level\/(\w+)/' => 'level',
    );

    /**
     * How many levels should exist for this amount of reqs?
     *
     * @param integer Total number of reqs
     * @return integer
     */
    public function getTotalLevels($requirements) 
    {
        return intval(floor(log($requirements, 8))) + 1;
    }

    /**
     * How many requirements should be on first level?
     *
     * @param integer Total number of reqs
     * @return integer
     */
    public function getTotalOnFirstLevel($requirements) 
    {
        return intval(round(min(8, max(4, $requirements / 8))));
    }
    
    /**
     * Calculate multiplier between levels
     *
     * @param integer Total number of reqs
     * @link http://mathcentral.uregina.ca/QQ/database/QQ.09.00/roble1.html
     * @return float
     */
    public function getMultiplier($requirements)
    {
        // to protect against division by zero
        if ($requirements < 8) {
            return 1;
        }
        $m = 0; // it's important to start from zero!
        do {
            $m = $m - $this->_lambda($m, $requirements) / $this->_lambdaDerivative($m, $requirements);
        } while (abs($this->_lambda($m, $requirements)) > 0.01);
        return $m;
    }

    /**
     * Lambda for getMultiplier() calculation
     *
     * The formula is: 
     * f(x) = FirstLevel \times (1 + M + M^2 + ... M^(Levels-1)) - Total
     *
     * @param float M
     * @param integer Total number of reqs
     * @return float
     */
    protected function _lambda($m, $requirements)
    {
        $mul = 0;
        for ($i=0; $i<$this->getTotalLevels($requirements); $i++) {
            $mul += pow($m, $i);
        }
        return $this->getTotalOnFirstLevel($requirements) * $mul - $requirements;
    }
        
    /**
     * Derivative for lambda
     *
     * The formula is: 
     * f(x) = FirstLevel \times (1 + 2M + 3M^2...)
     *
     * @param float X
     * @param integer Total number of reqs
     * @return float
     */
    protected function _lambdaDerivative($m, $requirements)
    {
        $mul = 0;
        for ($i=0; $i<$this->getTotalLevels($requirements)-1; $i++) {
            $mul += ($i + 1) * pow($m, $i);
        }
        return $this->getTotalOnFirstLevel($requirements) * $mul;
    }

    /**
     * How many requirements should be on the given level?
     *
     * @param integer Total number of reqs
     * @param integer Level (0..)
     * @return integer
     */
    public function getTotalOnLevel($requirements, $level) 
    {
        if ($level+1 > $this->getTotalLevels($requirements)) {
            return 0;
        }
        return intval(
            round(
                $this->getTotalOnFirstLevel($requirements) *
                pow($this->getMultiplier($requirements), $level)
            )
        );
    }
    
    /**
     * Load this metric
     *
     * @return void
     */
    public function reload()
    {
        // we can't calculate metrics here if deliverables are not loaded
        if (!$this->_project->deliverables->isLoaded()) {
            $this->_project->deliverables->reload();
        }
        
        $total = $this->_project->metrics['artifacts/requirements/functional/total'];
        $reqs = $this->_project->deliverables->functional;
        $level = $this->_getOption('level');
        if ($level) {
            // filter the array out leaving only reqs on the requested level
            foreach ($reqs as $id=>$req) {
                if ($total->getLevelCode($level) !== $req->getLevel()) {
                    unset($reqs[$id]);
                }
            }
        }

        // total amount of reqs accepted
        $this->value = 0;
        foreach ($reqs as $req) {
            if ($req->isAccepted()) {
                $this->value += 1;
            }
        }
            
        // objective for total reqs is to be set in Objectives, only
        if ($level) {
            $this->default = $this->getTotalOnLevel(
                $this->_project->metrics['artifacts/requirements/functional/accepted']->objective,
                $total->getLevelCode($level)
            );
        } else {
            $this->default = self::DEFAULT_TOTAL;
        }
    }
        
    /**
     * Get work package
     *
     * @param string[] Names of metrics, to consider after this one
     * @return theWorkPackage
     */
    protected function _derive(array &$metrics = array())
    {
        $level = $this->_getOption('level');
        // we specify requirements only on some particular level
        if (!$level) {
            // instruct loader to ping these metrics/WPs
            $codes = $this->_project->metrics['artifacts/requirements/functional/total']->getLevelCodes();
            foreach ($codes as $code) {
                $metrics[] = './level/' . $code;
            }
            return null;
        }
            
        // if we already have too many requirements - skip this WP
        if ($this->delta <= 0) {
            return null;
        }
            
        // price per all requirements at this level
        $price = FaZend_Bo_Money::factory(
            $this->_project
            ->metrics['history/cost/requirements/functional/level/' . $level]
            ->value
        )
        ->mul($this->objective);
        
        // we have more reqs accepted than needed
        if ($this->value >= $this->objective) {
            return null;
        }
            
        return $this->_makeWp(
            $price->mul(1 - $this->value / $this->objective), 
            sprintf(
                'to accept +%d %s level functional requirements',
                $this->objective - $this->value, 
                $level
            )
        );
    }
    
}
