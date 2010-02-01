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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

require_once 'artifacts/ProjectRegistry/Project/Time/ActivityList/slice-plugins/Abstract.php';

/**
 * Iterate activities
 * 
 * @package Slice_Plugin
 */
class Slice_Plugin_Iterate_DownCurve extends Slice_Plugin_Abstract
{

    /**
     * Minimim
     *
     * @var float
     */
    protected $_min;

    /**
     * Vertical delta
     *
     * @var float
     */
    protected $_d;

    /**
     * Iterate
     *
     * @param array List of options
     * @return mixed
     **/
    public function execute(array $options = array())
    {
        $this->_normalizeOptions(
            $options, 
            array(
                'minCost' => '10 USD', // minimal possible cost of one activity
                'codePrefix' => 'a', // prefix to set before each new code of activity
                'sow' => 'Perform work', //statement of work to set to each activity
            )
        );
        
        $this->rewind();
        $activity = $this->current();
        $total = FaZend_Bo_Money::factory($activity->cost);
        $this->delete($activity);
        
        $this->_min = FaZend_Bo_Money::factory($options['minCost'])->usd;
        
        /**
         * We will allocate numbers using $cos(x)$ function.
         * We assume that $x$ will be changing in $[0;\pi/2]$ interval.
         *
         * Vertical axis indicates the amount of money to be spend,
         * and horizontal axis indicates time.
         *
         * Function used: $f(x) = min + cos(x) * D$, where
         * $D$ is should be calculated using integral. We assume
         * that $\int f(x)$ in interval $[0;\pi/2]$ equals to total cost of work package.
         * Thus, $\int f(x) = x * min + sin(x) * D = T$, $T$ is total cost.
         * Thus, $D = (T - x * min) / sin(x)$
         *
         * On interval $[0;pi/2]$ $x$ equals to $\pi/2$ and $sin(x)$ equals to $1$
         */
        $this->_d = $total->usd - $this->_min * pi()/2;
        
        /** 
         * The smallest horizontal increment $p$ of $X$ we can afford, should
         * produce a rectangle on interval $[\pi/2-p;\pi/2]$ which square equals to $min$.
         *
         * Thus, $F(\pi/2) - F(x) = min$. Solution see below.
         */
        $p = pi()/2 - $this->_delta();
        
        /**
         * We start from the left horizontal point and move right until we reach $\pi/2$
         */
        $i = 0;
        for ($a = 0; $a < pi()/2; $a += $p) {
            
            // to avoid small pieces at the end
            if ($a+2*$p > pi()/2)
                $p = pi()/2 - $a;
                
            $activity = $this->add($options['codePrefix'] . $i++)
                ->setCost(FaZend_Bo_Money::factory($this->_square($a, $a+$p)))
                ->setSow($options['sow']);
        };
        
        return $i;
    }
    
    /**
     * Calculate square of a vertical bar on graph
     *
     * @param float X-coordinate, start
     * @param float X-coordinate, stop
     * @return float
     **/
    protected function _square($a, $b)
    {
        return $this->_int($b) - $this->_int($a);
    }
        
    /**
     * Function
     *
     * @param float X-coordinate
     * @return float
     **/
    protected function _f($x)
    {
        return $this->_min + cos($x) * $this->_d;
    }
        
    /**
     * Integral of f(x) = F(x)
     *
     * @param float X-coordinate
     * @return float
     **/
    protected function _int($x)
    {
        return $x * $this->_min + sin($x) * $this->_d;
    }
    
    /**
     * Calculate minimal horizontal delta
     *
     * The smallest horizontal increment $p$ of $X$ we can afford, should
     * produce a rectangle on interval $[\pi/2-p;\pi/2]$ which square equals to $min$.
     *
     * Thus, $F(\pi/2) - F(x) = min$, where $F(x) = min * x + d * sin(x) = $.
     *
     * $ = min * \pi/2 + d - min * x - d * sin(x) = min$
     *
     * $ min * \pi/2 - min + d - min * x - d * sin(x) = 0$
     *
     * Now we have function $\lambda(x)$ which should be equal to zero with
     * some value of $x$. We will use Newton-Raphson method, see link. 
     *
     * @link http://mathcentral.uregina.ca/QQ/database/QQ.09.00/roble1.html
     * @return float
     **/
    protected function _delta()
    {
        $x = 0; // it's important to start from zero!
        do {
            $x = $x - $this->_lambda($x) / $this->_lambdaDerivative($x);
        } while (abs($this->_lambda($x)) > 0.01);
        return $x;
    }

    /**
     * Lambda for _delta() calculation
     *
     * @param float X
     * @return float
     **/
    protected function _lambda($x)
    {
        return $this->_min * pi()/2 + $this->_d - $this->_min * $x - $this->_d * sin($x) - $this->_min;
    }
        
    /**
     * Derivative for lambda
     *
     * @param float X
     * @return float
     **/
    protected function _lambdaDerivative($x)
    {
        return - $this->_min - $this->_d * cos($x);
    }
        
}
