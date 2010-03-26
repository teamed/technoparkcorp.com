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

require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/Sheet/Abstract.php';

/**
 * System architecture overview
 *
 * @package Artifacts
 */
class Sheet_SAO extends Sheet_Abstract
{
    
    /**
     * Defaults
     *
     * @var array
     * @see __get()
     */
    protected $_defaults = array(
        'components' => array(),
        'concepts' => array(),
    );
    
    /**
     * Get diagram
     *
     * @return float
     */
    public function getDiagram() 
    {
        $diagram = new Sheet_SAO_Diagram();
        foreach ($this->components as $component) {
            $diagram->addComponent(strval($component['name']));
        }
        
        foreach ($this->components as $component) {
            foreach ($component as $to)
            $diagram->addLink(
                strval($component['name']), 
                strval($to['name']), 
                strval($to['value'])
            );
        }
        
        $width = max(4, min(10, 4 * count($this->components)));
        $height = max($width * 0.6, 1.5 * count($this->components));
        $diagram->setOptions(
            array(
                'width' => $width,
                'height' => $height,
            )
        );
        
        return $diagram->getLatex($this->sheets->getView());
    }
    
}
