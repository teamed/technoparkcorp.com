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
 * Pathfinder
 *
 * @package Model
 */
class Model_Algo_PathFinder extends Model_Algo
{

    /**
     * List of options
     *
     * @var string[]
     */
    protected $_options = array(
        'pairs' => array(),
    );
    
    /**
     * List of already passed pairs
     *
     * @var string[]
     * @see find()
     */
    protected $_passed;

    /**
     * Find one path from $left to $right, or NULL if it's absent
     *
     * Returns an array of strings. Every item is
     * a tag from {@link $this->_options['pairs']}, which is in a possible way
     * from $left to $right.
     *
     * @param string Tag from left components of {@link $this->_options['pairs']}
     * @param string Tag from right components of {@link $this->_options['pairs']}
     * @return array|null NULL if nothing found
     */
    public function find($left, $right) 
    {
        assert(isset($this->_options['pairs']));
        $this->_passed = array();
        return $this->_findTail($left, $right);
    }

    /**
     * Find one path from $left to $right, or NULL if it's absent
     *
     * @param string Tag from left components of {@link $this->_options['pairs']}
     * @param string Tag from right components of {@link $this->_options['pairs']}
     * @return array|null NULL if nothing found
     */
    protected function _findTail($left, $right) 
    {
        $path = array($left);
        foreach ($this->_options['pairs'] as $id=>$pair) {
            if (isset($this->_passed[$id])) {
                continue;
            }
            assert($pair instanceof Model_Algo_PathFinder_Pair);
            $l = $pair->getLeftInPair();
            $r = $pair->getRightInPair();
            // ignore "a->a" pairs
            if ($l == $r) {
                continue;
            }
            if ($l !== $left) {
                continue;
            }
            if ($r === $right) {
                $path[] = $r;
                return $path;
            }
            $this->_passed[$id] = true;
            $more = $this->_findTail($r, $right);
            if ($more) {
                $path = array_merge($path, $more);
                return $path;
            }
        }
        return null;
    }
    
}
