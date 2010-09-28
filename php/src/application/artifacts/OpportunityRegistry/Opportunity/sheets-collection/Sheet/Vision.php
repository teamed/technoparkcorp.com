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
 * Vision
 *
 * @package Artifacts
 */
class Sheet_Vision extends Sheet_Abstract
{
    
    const UC_WIDTH = 2;
    const UC_HEIGHT = 1.2;
    
    /**
     * Defaults
     *
     * @var array
     * @see __get()
     */
    protected $_defaults = array(
        'product' => 'Custom Software System',
        'statement' => 'There is a strong marketing opportunity for a new business',
        'actors' => array(),
        'quality' => array(),
    );

    /**
     * Draw and return UC diagram
     *
     * @return string LaTeX
     */
    public function getUseCaseDiagram() 
    {
        $diagram = new Sheet_Vision_Diagram();
        foreach ($this->actors as $actor) {
            $diagram->addActor(strval($actor['name']));
        }
        
        foreach ($this->features as $feature) {
            $feature = strval($feature['value']);
            $matches = array();
            if (preg_match('/^"(.*?)"\s*(.*)$/', $feature, $matches)) {
                $feat = $matches[1];
                $feature = $matches[2];
            } else {
                $feat = $feature;
            }
            
            $actor = substr($feature, 0, strpos($feature, ' '));
            $diagram->addFeature($feat, $actor);
        }
        
        $width = min(4, sqrt(count($this->features)));
        $height = max(
            count($this->features) / $width, 
            sqrt(count($this->features))
        );
        
        $diagram->setOptions(
            array(
                'width' => false,
                'height' => false,

                'cellWidth' => self::UC_WIDTH * 1.2,
                'cellHeight' => self::UC_HEIGHT * 1.2,
                'cellsTotalX' => ceil($width),
                'cellsTotalY' => ceil($height),
                
                'startAngle' => 15,
            )
        );
        
        return $diagram->getLatex($this->sheets->getView());
    }
    
    /**
     * Get list of features
     *
     * @return SimpleXMLElement
     */
    protected function _getFeatures() 
    {
        return $this->actors->xpath('item/item');
    }
    
}
