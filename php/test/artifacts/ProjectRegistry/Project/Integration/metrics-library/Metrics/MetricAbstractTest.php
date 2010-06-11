<?php
/**
 * @version $Id$
 */

require_once 'AbstractProjectTest.php';

class MetricAbstractTest extends AbstractProjectTest
{
    
    const NAME = 'artifacts/requirements/functional/total';
    
    public function setUp()
    {
        parent::setUp();
        
        // explicitly reload them
        if (!$this->_project->metrics->isLoaded()) {
            $this->_project->metrics->reload();
        }
        
        $this->_metric = $this->_project->metrics[self::NAME];
    }
    
    public function testPropertiesOfMetricAreAccessible() 
    {
        $this->assertEquals(self::NAME, $this->_metric->name);
        $this->assertEquals('total', $this->_metric->suffix);
        $this->assertTrue(is_string($this->_metric->id));
        $this->assertTrue(is_numeric($this->_metric->value));
        $this->assertTrue(
            empty($this->_metric->objective) || is_numeric($this->_metric->objective),
            'Non-numeric objective: ' . $this->_metric->objective
        );
        $this->assertTrue(
            empty($this->_metric->default) || is_numeric($this->_metric->default),
            'Non-numeric default: ' . $this->_metric->objective
        );
        $this->assertTrue(
            empty($this->_metric->delta) || is_numeric($this->_metric->delta),
            'Non-numeric delta: ' . $this->_metric->delta
        );
        $this->assertTrue(is_bool($this->_metric->visible));
    }
    
    // public function testObjectiveIsChangeable()
    // {
    //     $this->_metric->objective = 150;
    //     $this->assertEquals(150, $this->_metric->objective);
    // }

}