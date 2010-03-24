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
 * Universal builder of a Gantt Chart, rendering it in LaTeX. Can be used
 * either in sales documentation or anywhere else. It is initialized and
 * configured like this:
 *
 * <code>
 * $chart = new Sheet_ScheduleEstimate_Chart();
 * $chart->addBar(...);
 * $chart->addDependency(...);
 * echo $chart->getLatex($view);
 * </code>
 *
 * @package Artifacts
 * @see Sheet_ScheduleEstimate::getChart()
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
        'renderLegend' => true,
        
        'tikzGrid' => 'timescale',
        'tikzBar' => 'bar',
        'tikzBarText' => 'barText',
        'tikzWorstBar' => 'worstBar',
        'tikzMilestone' => 'milestone',
        'tikzWorstMilestone' => 'worstMilestone',
        'tikzConnector' => 'connector',
        'tikzComment' => 'comment',
        'tikzWorstComment' => 'worstComment',
        'tikzXScale' => 'xLabels',
        'tikzRunningDots' => 'runningDots',
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
     * Scale to use for x-axis
     *
     * Array of two elements. First one is delta to use, second one is a callback
     * to call in order to get the string to show. For example:
     *
     * <code>
     * array(30, 'sprintf("%d-th month", ${a1}/30)')
     * </code>
     *
     * @var array
     */
    protected $_xScale;
    
    /**
     * Set x-scale
     *
     * @return void
     * @see $this->_xScale
     */
    public function setXScale($delta, $callback) 
    {
        $this->_xScale = array(
            $delta,
            FaZend_Callback::factory($callback)
        );
    }
    
    /**
     * Add new bar
     *
     * @param string Name of the bar
     * @param float Size of the bar
     * @param string Comment
     * @param float Accuracy
     * @param string Comment to show for the worst scenario
     * @return void
     * @throws Sheet_ScheduleEstimate_Chart_AccuracyProhibitedException
     * @throws Sheet_ScheduleEstimate_Chart_DuplicateException
     * @throws Sheet_ScheduleEstimate_Chart_AccuracyInvalidException
     */
    public function addBar($name, $size, $comment = null, $accuracy  = 1, $worstComment = null) 
    {
        if (!$size && ($accuracy != 1)) {
            FaZend_Exception::raise(
                'Sheet_ScheduleEstimate_Chart_AccuracyProhibitedException',
                "Accuracy can't be set to milestone '{$name}'"
            );
        }
        if ($accuracy < 1) {
            FaZend_Exception::raise(
                'Sheet_ScheduleEstimate_Chart_AccuracyInvalidException',
                "Accuracy can't be less than 1 for '{$name}': {$accuracy}"
            );
        }
        $found = false;
        foreach ($this->_bars as $id=>$bar) {
            if ($bar['name'] == strval($name)) {
                $found = $id;
            }
        }
        if ($found !== false) {
            FaZend_Exception::raise(
                'Sheet_ScheduleEstimate_Chart_DuplicateException',
                "Bar '{$name}' (#{$found}) already exists in the chart"
            );
        }
        $this->_bars[] = array(
            'name' => strval($name),
            'size' => floatval($size),
            'comment' => strval($comment),
            'worstComment' => strval($worstComment),
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
     * @throws Sheet_ScheduleEstimate_Chart_SelfLinkException
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
        
        if ($from === $to) {
            FaZend_Exception::raise(
                'Sheet_ScheduleEstimate_Chart_SelfLinkException',
                "Bar '{$from}' can't link to itself"
            );
        }
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
        
        $tex = "\\begin{tikzpicture}\n";
        
        // grid
        $tex .= sprintf(
            "\\draw [%s, xstep=%f, ystep=%f] (0,0) grid (%f, %f);\n",
            $this->_options['tikzGrid'],
            $stepX,
            $stepY,
            $this->_options['width'],
            $this->_options['height']
        );
        
        $line = count($this->_bars) - 1;
        foreach ($this->_bars as $id=>$bar) {
            $bestX = $scaleX * $bar['bestStart'];
            $bestWidth = $scaleX * $bar['size'];
            
            $y = $scaleY * ($line--);
            
            // just a comment
            $tex .= sprintf(
                "\n%% %s: %s, %s\n",
                $id,
                $view->tex($bar['name']),
                $view->tex($bar['comment'])
            );
            
            if ($bar['accuracy'] && $this->_options['useAccuracy']) {
                $worstX = $scaleX * $bar['worstStart'];
                $worstWidth = $scaleX * $bar['size'] * $bar['accuracy'];
                if (!$this->_isMilestone($id)) {
                    $tex .= sprintf(
                        "\\node [%s, text width=%fcm] at (%f, %f) (barWorst%s) {};\n",
                        $this->_options['tikzWorstBar'],
                        $worstWidth,
                        $worstX,
                        $y,
                        $id
                    );
                } else {
                    $tex .= sprintf(
                        "\\node [%s] at (%f, %f) (barWorst%s) {};\n" .
                        "\t\\node [%s, right=0mm of barWorst%s] {%s};\n",
                        $this->_options['tikzWorstMilestone'],
                        $worstX,
                        $y,
                        $id,
                        $this->_options['tikzWorstComment'],
                        $id,
                        isset($bar['worstComment']) ? $view->tex($bar['worstComment']) : ''
                    );
                }
            }
            
            if (!$this->_isMilestone($id)) {
                $tex .= sprintf(
                    "\\node [%s, text width=%fcm] at (%f, %f) (bar%s) {};\n" .
                    "\\node [%s, right=0mm of bar%s.west, anchor=west] {%s};\n",
                    $this->_options['tikzBar'],
                    $bestWidth,
                    $bestX,
                    $y,
                    $id, 
                    $this->_options['tikzBarText'],
                    $id,
                    $view->tex($bar['name'])
                );
            } else {
                $tex .= sprintf(
                    "\\node [%s] at (%f, %f) (bar%s) {};\n" .
                    "\t\\node [%s, %s=0mm of bar%s] {%s}; %% %s\n",
                    $this->_options['tikzMilestone'],
                    $bestX,
                    $y,
                    $id,
                    $this->_options['tikzComment'],
                    $this->_options['useAccuracy'] ? 'left' : 'right',
                    $id,
                    $view->tex($bar['comment']),
                    $view->tex($bar['name'])
                );
            }
            
            // running dots
            if ($bar['accuracy'] && $this->_options['useAccuracy']) {
                $tex .= sprintf(
                    "\\draw [%s] (bar%s.east) -- (barWorst%s.%s);\n",
                    $this->_options['tikzRunningDots'],
                    $id,
                    $id,
                    $this->_isMilestone($id) ? 'west' : 'east'
                );
            }
            
        }
        
        // draw lines from one bar to another
        $tex .= $this->_drawLines();
        
        // draw X and Y scales
        if (isset($this->_xScale)) {
            $tex .= $this->_drawXScale($view);
        }
        
        if (!empty($this->_options['renderLegend'])) {
            $tex .= sprintf(
                "\\node [%s, anchor=east] at (%f, %f) (legend) {optimistic};\n" .
                "\\node [%s, below=2em of legend.east, anchor=east] {pessimistic};\n",
                $this->_options['tikzBar'],
                $this->_options['width'],
                $this->_options['height'],
                $this->_options['tikzWorstBar']
            );
        }
        
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
        $tex = "\n";
        $out = 0.3; //$this->_options['width'] / 30;
        
        // arrows!
        foreach ($this->_dependencies as $dep) {
            $path = '--';
            switch (true) {
                case $this->_isMilestone($dep['from']) && !$dep['lag']:
                    $left = 'south';
                    $right = 'west';
                    $path = "|- +(-{$out},-{$out}) |-";
                    break;
                case $this->_isMilestone($dep['from']) && $dep['lag']:
                    $left = 'south';
                    $right = 'west';
                    $path = '|-';
                    break;
                case !$this->_isMilestone($dep['from']) && !$this->_isMilestone($dep['to']):
                    $left = 'east';
                    $right = 'north west';
                    $path = "-| +({$out},-{$out}) -|";
                    break;
                case !$this->_isMilestone($dep['from']) && $this->_isMilestone($dep['to']) && !$dep['lag']:
                    $left = 'east';
                    $right = 'north';
                    $path = "-| +({$out},-{$out}) -|";
                    break;
                case !$this->_isMilestone($dep['from']) && $this->_isMilestone($dep['to']) && $dep['lag']:
                    $left = 'east';
                    $right = 'north';
                    $path = '-|';
                    break;
                default:
                    FaZend_Exception::raise(
                        'Sheet_ScheduleEstimate_InvalidPath', 
                        "Strange path"
                    );
            }
            $tex .= sprintf(
                "\\draw [%s] (bar%s.%s) %s (bar%s.%s);\n",
                $this->_options['tikzConnector'],
                $dep['from'],
                $left,
                $path,
                $dep['to'],
                $right
            );
        }
        return $tex;
    }

    /**
     * Draw X scale
     *
     * @return void
     */
    protected function _drawXScale(Zend_View $view) 
    {
        list($delta, $callback) = $this->_xScale;
        $num = 0;
        $width = $this->_calculateWidth();
        $tex = '';
        while ($num < $width) {
            $x = $this->_options['width'] * $num / $width;
            $tex .= sprintf(
                "\\node[%s] at (%f,0) {%s};\n",
                $this->_options['tikzXScale'],
                $x,
                $view->tex($callback->call($num))
            );
            $num += $delta;
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
                    * ($useAccuracy ? $this->_bars[$dep['from']]['accuracy'] : 1)
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