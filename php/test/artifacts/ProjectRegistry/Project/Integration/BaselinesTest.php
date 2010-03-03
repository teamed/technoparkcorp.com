<?php
/**
 * @version $Id: MetricsTest.php 718 2010-02-21 15:55:39Z yegor256@yahoo.com $
 */

require_once 'AbstractProjectTest.php';

class BaselinesTest extends AbstractProjectTest
{
    
    public function setUp()
    {
        parent::setUp();
        if (!$this->_project->deliverables->isLoaded()) {
            $this->_project->deliverables->reload();
        }
        $this->_project->baselines->reload();
    }
    
    public function testSnapshotIsRenderableAndParseable() 
    {
        $baselines = $this->_project->baselines;
        $snapshot = $baselines['trunk']->text;
        $snapshot = $this->_touchIt($snapshot);
        assert(!empty($snapshot));
        $baseline = $baselines->addSnapshot('test', 'this is just a test snapshot', $snapshot);
        $this->assertTrue($baseline instanceof theBaseline);
    }
    
    public function testProjectCanBeSwitchedToBaseline() 
    {
        $baselines = $this->_project->baselines;
        $snapshot = $baselines['trunk']->text;
        $snapshot = $this->_touchIt($snapshot);
        $baseline = $baselines->addSnapshot('test2', 'another test snapshot', $snapshot);
        $baselines->switchTo('test2');
    }
    
    protected function _touchIt($text)
    {
        $lines = explode("\n", $text);
        $inject = 'oops';
        foreach ($lines as &$line) {
            if ((strpos($line, theBaseline::CHAPTER_MARKER) === false) && (rand(0, 9) > 6)) {
                $pos = rand(0, strlen($line) - strlen($inject));
                $line = substr($line, 0, $pos) . $inject . substr($line, $pos + strlen($inject));
            }
        }
        return implode("\n", $lines);
    }
    
}