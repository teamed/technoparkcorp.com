<?php
/**
 * @version $Id$
 */

require_once 'AbstractTest.php';

/**
 * @see Model_Algo_PathFinder_Pair
 */
require_once 'Model/Algo/PathFinder/Pair.php';

class pair implements Model_Algo_PathFinder_Pair
{
    protected $_left;
    protected $_right;
    function __construct($left, $right) { $this->_left = $left; $this->_right = $right; }
    function getLeftInPair() { return $this->_left; }
    function getRightInPair() { return $this->_right; }
}

class Model_Algo_PathFinderTest extends AbstractTest 
{

    public static function providerCombinations()
    {
        return array(
            array(null, 'c', 'f'),
            array(array('a', 'e'), 'a', 'e'),
            array(array('a', 'e', 'c'), 'a', 'c'),
            array(array('f', 'b', 'c', 'a'), 'f', 'a'),
            array(array('f', 'b', 'c', 'a', 'e'), 'f', 'e'),
        );
    }

    /**
     * @dataProvider providerCombinations
     */
    public function testSimplePathFindingWorks($path, $left, $right) 
    {
        $pairs = array(
            new pair('b', 'c'),
            new pair('a', 'e'),
            new pair('e', 'c'),
            new pair('b', 'f'), new pair('f', 'b'), // endless cycle!
            new pair('c', 'a'),
        );
        
        $algo = Model_Algo::factory(
            'pathFinder', 
            array('pairs' => new ArrayIterator($pairs))
        );

        $this->assertEquals($path, $algo->find($left, $right));
    }

}