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
 * Gantt Chart
 *
 * @package Artifacts
 */
class Sheet_ScheduleEstimate_Chart
{
    
    const DEP_FS = 'fs';
    const DEP_SF = 'sf';
    const DEP_SS = 'ss';
    const DEP_FF = 'ff';
    
    /**
     * Options
     *
     * @var array
     */
    protected $_options = array(
        'width' => 10,
        'height' => 5,
        
        'useAccuracy' => true,
        
        'tikzGrid' => 'timescale',
        'tikzBar' => 'bar',
        'tikzWorstBar' => 'worstBar',
        'tikzMilestone' => 'milestone',
        'tikzWorstMilestone' => 'worstMilestone',
        'tikzConnector' => 'connector',
    );
    
    /**
     * List of bars to show
     *
     * Every element of this array is an array of elements:
     *
     * <code>
     * array(
     *   '1' => array(
     *     'size' => 25, // 0 means that this is a milestone
     *     'name' => 'work to do',
     *     'comment' => 'this task is about creating new code...',
     *     'accuracy' => 1.35, // 35% more, in pessimistic case
     *   ),
     * )
     * </code>
     *
     * @var array[]
     */
    protected $_bars = array();

    /**
     * Dependency from bar to bar
     *
     * Every element of this array is an array of elements:
     *
     * <code>
     * array(
     *   array(
     *     'from' => '1',
     *     'to' => '2',
     *     'type' => 'FS', // finish-to-start
     *     'lag' => 4, // lag, if necessary (ZERO by default)
     *     'comment' => 'this link is important',
     *   ),
     * )
     * </code>
     *
     * @var array[]
     */
    protected $_dependencies = array();
    
    /**
     * Add new bar
     *
     * @param string Name of the bar
     * @param float Size of the bar
     * @param string Comment
     * @param float Accuracy
     * @return void
     */
    public function addBar($name, $size, $comment = null, $accuracy  = 1) 
    {
        $this->_bars[] = array(
            'name' => strval($name),
            'size' => floatval($size),
            'comment' => strval($comment),
            'accuracy' => floatval($accuracy),
        );
    }
    
    /**
     * Add new actor
     *
     * @param string FROM bar name
     * @param string TO bar name
     * @param string Type of relation
     * @param float Lag, if necessary
     * @param string Comment
     * @return void
     * @throws Sheet_ScheduleEstimate_Chart_NotFoundException
     */
    public function addDependency($from, $to, $type = self::DEP_FS, $lag = 0, $comment = null) 
    {
        $found = false;
        foreach ($this->_bars as $id=>$bar) {
            if ($bar['name'] == strval($from)) {
                $found = $id;
            }
        }
        if ($found === false) {
            FaZend_Exception::raise(
                'Sheet_ScheduleEstimate_Chart_NotFoundException',
                "Bar '{$from}' not found as FROM"
            );
        }
        $from = $found;

        $found = false;
        foreach ($this->_bars as $id=>$bar) {
            if ($bar['name'] == strval($to)) {
                $found = $id;
            }
        }
        if ($found === false) {
            FaZend_Exception::raise(
                'Sheet_ScheduleEstimate_Chart_NotFoundException',
                "Bar '{$to}' not found as TO"
            );
        }
        $to = $found;
        
        $this->_dependencies[] = array(
            'from' => $from,
            'to' => $to,
            'type' => $type,
            'lag' => floatval($lag),
            'comment' => strval($comment),
        );
    }
    
    /**
     * Set array of config options
     *
     * @param array Options
     * @return $this
     * @throws Sheet_Vision_InvalidOption
     */
    public function setOptions(array $options) 
    {
        foreach ($options as $option=>$value) {
            if (!array_key_exists($option, $this->_options)) {
                FaZend_Exception::raise(
                    'Sheet_ScheduleEstimate_InvalidOption', 
                    "Option '{$option}' is unknown in " . get_class($this)
                );
            }
            $this->_options[$option] = $value;
        }
        return $this;
    }
    
    /**
     * Convert chart to LaTeX
     *
     * @param Zend_View View to render
     * @return string
     * @throws Sheet_ScheduleEstimate_InvalidPath
     */
    public function getLatex(Zend_View $view) 
    {
        // normalize options before rendering
        $this->_normalizeOptions();
        
        // for every bar set its start point ("bestStart" and "worstStart")
        $this->_setStarts();
        // bug($this->_bars);
        
        // calculate maximum width of the diagram
        $width = $this->_calculateWidth();
        $scaleX = $this->_options['width'] / $width;

        // calculate maximum height of the diagram
        $height = count($this->_bars);
        $scaleY = $this->_options['height'] / ($height - 1);
        
        $stepX = max($scaleX, $this->_options['width'] / 10);
        $stepY = $scaleY;
        
        $tex ="\\begin{tikzpicture}\n" .
        "\\draw [{$this->_options['tikzGrid']}, xstep={$stepX}, ystep={$stepY}] " .
        "(0,0) grid ({$this->_options['width']}, {$this->_options['height']});\n";
        
        $line = count($this->_bars) - 1;
        foreach ($this->_bars as $id=>$bar) {
            $bestX = $scaleX * $bar['bestStart'];
            $bestWidth = $scaleX * $bar['size'];
            
            $y = $scaleY * ($line--);
            
            if ($bar['accuracy'] && $this->_options['useAccuracy']) {
                $worstX = $scaleX * $bar['worstStart'];
                $worstWidth = $scaleX * $bar['size'] * $bar['accuracy'];
                if (!$this->_isMilestone($id)) {
                    $tex .= "\\node [{$this->_options['tikzWorstBar']}, text width={$worstWidth}cm] " .
                    "at ({$worstX}, {$y}) {};\n";
                } else {
                    $tex .= "\\node [{$this->_options['tikzWorstMilestone']}] " .
                    "at ({$worstX}, {$y}) {};\n";
                }
            }
            
            if (!$this->_isMilestone($id)) {
                $tex .= "\\node [{$this->_options['tikzBar']}, text width={$bestWidth}cm] " .
                "at ({$bestX}, {$y}) (bar{$id}) {{$view->tex($bar['name'])}};\n";
            } else {
                $tex .= "\\node [{$this->_options['tikzMilestone']}] " .
                "at ({$bestX}, {$y}) (bar{$id}) {};" .
                "\\node [anchor=west, right=0mm of bar{$id}] {{$view->tex($bar['name'])}};\n";
            }
        }
        
        $tex .= $this->_drawLines();
        
        return $tex . "\\end{tikzpicture}\n";
    }
    
    /**
     * Normalize them
     *
     * @return void
     */
    protected function _normalizeOptions() 
    {
    }
    
    /**
     * Draw lines from bar to bar
     *
     * @return string
     */
    protected function _drawLines() 
    {
        $tex = '';
        // arrows!
        foreach ($this->_dependencies as $dep) {
            $path = '--';
            switch (true) {
                case $this->_isMilestone($dep['from']) && !$dep['lag']:
                    $left = 'south';
                    $right = 'west';
                    $path = '|- +(-0.5,-0.5) |-';
                    break;
                case $this->_isMilestone($dep['from']) && $dep['lag']:
                    $left = 'south';
                    $right = 'west';
                    $path = '|-';
                    break;
                case !$this->_isMilestone($dep['from']) && !$this->_isMilestone($dep['to']):
                    $left = 'east';
                    $right = 'north west';
                    $path = '-| +(0.5,-0.5) -|';
                    break;
                case !$this->_isMilestone($dep['from']) && $this->_isMilestone($dep['to']):
                    $left = 'east';
                    $right = 'north';
                    $path = '-| +(0.5,-0.5) -|';
                    break;
                default:
                    FaZend_Exception::raise(
                        'Sheet_ScheduleEstimate_InvalidPath', 
                        "Strange path"
                    );
            }
            $tex .= "\\draw [{$this->_options['tikzConnector']}] " .
            "(bar{$dep['from']}.{$left}) {$path} (bar{$dep['to']}.{$right});\n";
        }
        return $tex;
    }

    /**
     * Is it a milesone?
     *
     * @param string Bar id
     * @return boolean
     */
    protected function _isMilestone($id) 
    {
        return $this->_bars[$id]['size'] == 0;
    }
    
    /**
     * Set "bestStart" and "worstStart" for each bar
     *
     * @return void
     */
    protected function _setStarts() 
    {
        foreach ($this->_bars as $id=>&$bar) {
            $bar['bestStart'] = $this->_getStart($id);
        }
        foreach ($this->_bars as $id=>&$bar) {
            $bar['worstStart'] = $this->_getStart($id, true);
        }
    }
    
    /**
     * Get start of the given bar (ID provided)
     *
     * @param string Name of the bar
     * @param boolean Shall we take accuracy into account?
     * @return float
     */
    protected function _getStart($id, $useAccuracy = false) 
    {
        $start = 0;
        foreach ($this->_dependencies as $dep) {
            if ($dep['to'] == $id) {
                $start = max(
                    $start, 
                    $this->_getStart($dep['from'], $useAccuracy)
                    + $dep['lag']
                    + $this->_bars[$dep['from']]['size']
                    * ($useAccuracy ? 1 : $this->_bars[$dep['from']]['accuracy'])
                );
            }
        }
        return $start;
    }
    
    /**
     * Calculate maximum width of the chart
     *
     * @return void
     */
    protected function _calculateWidth() 
    {
        $width = 0;
        foreach ($this->_bars as $bar) {
            $width = max(
                $width,
                $bar['worstStart'] + $bar['size'] * $bar['accuracy']
            );
        }
        return $width;
    }
    
}
