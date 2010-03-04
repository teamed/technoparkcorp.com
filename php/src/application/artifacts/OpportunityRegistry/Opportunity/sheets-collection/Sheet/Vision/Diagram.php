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
 * @version $Id: Vision.php 661 2010-02-11 13:07:10Z yegor256@yahoo.com $
 *
 */

/**
 * Use case diagram
 *
 * @package Artifacts
 */
class Sheet_Vision_Diagram
{
    
    /**
     * Options
     *
     * @var array
     */
    protected $_options = array(
        'width' => 100,
        'height' => 100,

        'texActor' => '\visionActor',
        'tikzUC' => 'uc',
        'tikzUsageLine' => 'ln',
        'tikzBoundary' => 'boundary',
        'tikzBoundaryNote' => 'boundaryNote',

        'actorWidth' => 10,
        'actorHeight' => 10,
        
        'cellWidth' => 10,
        'cellHeight' => 10,
        'cellsTotalX' => 4,
        'cellsTotalY' => 4,
    );
    
    /**
     * List of actors and features
     *
     * <code>
     * array(
     *   'User' => array('Login', 'Register Account'),
     *   'Admin' => array('Print Reports', 'Configure System'),
     * )
     * </code>
     *
     * @var array
     */
    protected $_features = array();
    
    /**
     * Add new feature
     *
     * @param string Feature text
     * @param string Actor name
     * @return void
     */
    public function addFeature($feature, $actor) 
    {
        $this->addActor($actor);
        $this->_features[$actor][$feature] = $feature;
    }
    
    /**
     * Add new actor
     *
     * @param string Actor name
     * @return void
     */
    public function addActor($actor) 
    {
        if (!array_key_exists($actor, $this->_features)) {
            $this->_features[$actor] = array();
        }
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
                    'Sheet_Vision_InvalidOption', 
                    "Option '{$option}' is unknown in the diagram builder"
                );
            }
            $this->_options[$option] = $value;
        }
        return $this;
    }
    
    /**
     * Convert diagram to LaTeX
     *
     * @param Zend_View View to render
     * @return string
     */
    public function getLatex(Zend_View $view) 
    {
        // normalize options before rendering
        $this->_normalizeOptions();
        
        // get location of actors, returns an associative
        // array, where keys are actors and values are
        // ANGLEs in radians
        $actors = $this->_getActorsLocation();
        
        // allocate them on the surface
        // and return an associative array, where
        // keys are features and values are two-items array, 
        // with XY coordinate of the cell to put this feature to
        $features = $this->_getFeaturesLocation($actors);
        
        // center of the diagram
        $centerX = $this->_options['width'] / 2;
        $centerY = $this->_options['height'] / 2;

        // radius for actors allocation
        $radiusX = ($centerX + ($this->_options['cellsTotalX'] * $this->_options['cellWidth'])/2)/2;
        $radiusY = ($centerY + ($this->_options['cellsTotalY'] * $this->_options['cellHeight'])/2)/2;

        $actorNum = $featureNum = 1;
        $tex ="\\begin{tikzpicture}\n";
        foreach ($actors as $actor=>$angle) {
            $x = $centerX + $radiusX * cos($angle);
            $y = $centerY + $radiusY * sin($angle);
            $tex .= "{$this->_options['texActor']}{actor{$actorNum}}{{$x}}{{$y}}{{$view->tex($actor)}}\n";
            
            foreach ($features as $feature=>$coordinates) {
                if (!in_array($feature, $this->_features[$actor])) {
                    continue;
                }
                
                list($cellX, $cellY) = $coordinates;
                
                $ucX = $centerX + ($cellX - ($this->_options['cellsTotalX']-1)/2) * $this->_options['cellWidth'];
                $ucY = $centerY + ($cellY - ($this->_options['cellsTotalY']-1)/2) * $this->_options['cellHeight'];
                
                $tex .= "\\node [{$this->_options['tikzUC']}] (feature{$featureNum}) " .
                "at($ucX, $ucY) {``{$view->tex($feature)}''};\n" .
                "\\path [{$this->_options['tikzUsageLine']}] " .
                "(actor{$actorNum}) -- (feature{$featureNum});\n";
                $featureNum++;
            }
            $actorNum++;
        }
        
        $fit = '';
        for ($i=1; $i<$featureNum; $i++) {
            $fit .= '(feature' . $i . ') ';
        }
        
        $tex .=
        "\\node [{$this->_options['tikzBoundary']}, fit={$fit}] (scope) {};\n".
        "\\node [{$this->_options['tikzBoundaryNote']}] {System Boundary};\n";

        return $tex . "\\end{tikzpicture}\n";
    }
    
    /**
     * Normalize them
     *
     * @return void
     */
    protected function _normalizeOptions() 
    {
        if (empty($this->_options['width'])) {
            $this->_options['width'] = 
            $this->_options['cellsTotalX'] * ($this->_options['cellWidth'] + 4);
        }
        if (empty($this->_options['height'])) {
            $this->_options['height'] = 
            $this->_options['cellsTotalY'] * ($this->_options['cellHeight'] + 4);
        }
    }
    
    /**
     * Allocate actors on the surface
     *
     * @return array
     * @see getLatex()
     */
    protected function _getActorsLocation() 
    {
        $actors = array();
        $angle = 0;
        foreach (array_keys($this->_features) as $actor) {
            $actors[$actor] = $angle;
            $angle += pi() * 2 / count($this->_features);
        }
        return $actors;
    }
    
    /**
     * Allocate them on the surface
     *
     * @param array Result from _getActorsLocation()
     * @return array
     * @see getLatex()
     * @see _getActorsLocation()
     * @throws Sheet_Vision_Diagram_NotEnoughCellsException
     */
    protected function _getFeaturesLocation(array $actors) 
    {
        $cells = array();
        for ($i=0; $i<$this->_options['cellsTotalX']; $i++) {
            $cells[$i] = array();
            for ($j=0; $j<$this->_options['cellsTotalY']; $j++) {
                $cells[$i][$j] = false;
            }
        }
        
        $features = array();
        foreach ($actors as $actor=>$angle) {
            foreach ($this->_features[$actor] as $feature) {
                $busy = 0;
                foreach ($this->_getBestCells($angle) as $coordinate) {
                    list($x, $y) = $coordinate;
                    if (empty($cells[$x][$y])) {
                        break;
                    }
                    $busy++;
                }
                if (!empty($cells[$x][$y])) {
                    FaZend_Exception::raise(
                        'Sheet_Vision_Diagram_NotEnoughCellsException',
                        "Not enough cells, {$busy} are busy"
                    );
                }
                $cells[$x][$y] = $feature;
                $features[$feature] = $coordinate;
            }
        }
        return $features;
    }
    
    /**
     * Get list of best cells
     *
     * @param float Angle in radians
     * @return array
     */
    protected function _getBestCells($angle) 
    {
        $actorX = 2 * cos($angle);
        $actorY = 2 * sin($angle);
        
        $coordinates = array();
        
        $width = $this->_options['cellsTotalX']-1;
        $height = $this->_options['cellsTotalY']-1;
        
        for ($i=0; $i<=$width; $i++) {
            $cellX = ($i - $width/2) / $width;
            for ($j=0; $j<=$height; $j++) {
                $cellY = ($j - $height/2) / $height;
                $distance = sqrt(pow($actorX - $cellX, 2) + pow($actorY - $cellY, 2));
                
                $coordinates[] = array($i, $j, $distance);
            }
        }
        usort(
            $coordinates, 
            create_function(
                '$a, $b',
                'return $a[2] > $b[2];'
            )
        );
        return $coordinates;
    }
    
}
