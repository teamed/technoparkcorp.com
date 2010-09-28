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
 * @version $Id: ScheduleEstimate.php 861 2010-03-26 08:40:23Z yegor256@yahoo.com $
 *
 */

require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/Sheet/Abstract.php';

/**
 * Preliminary staffing estimate
 *
 * @package Artifacts
 */
class Sheet_StaffingEstimate extends Sheet_Abstract
{
    
    /**
     * Defaults
     *
     * @var array
     * @see __get()
     */
    protected $_defaults = array(
        'staff' => array(),
    );

    /**
     * Render ordered list.
     *
     * @return string LaTeX
     */
    public function getSpreadsheet() 
    {
        $tex = "{\\linespread{1.0}\\selectfont
            \\begin{tabular}{>{\\raggedright}p{5em}
            >{\\raggedright}p{14em}>{\\raggedright}p{8em}r}
            Role & Responsibilities & Skills & Hours \\\\
            \\hline\n";
            
        foreach ($this->staff as $role) {
            $opts = array(
                'skills' => false,
                'budget' => false,
            );
            foreach ($role->item as $i) {
                $opt = strval($i['name']);
                if (array_key_exists($opt, $opts)) {
                    $opts[$opt] = $i['value'];
                } else {
                    FaZend_Exception::raise(
                        'Sheet_StaffingEstimate_InvalidOptionException', 
                        "Option '{$opt}' is not recognized"
                    );
                }
            }
            $tex .= $this->sheets->getView()->tex($role['name'])
                . ' & '
                . $this->sheets->getView()->tex($role['value'])
                . ' & '
                . $this->sheets->getView()->tex($opts['skills'])
                . ' & '
                . $this->sheets->getView()->tex(
                    floor(
                        $this->sheets['ROM']->hours * intval($opts['budget']) / 100
                    )
                )
                . "\\\\ \n";
        }
        $tex .= "\\hline\n \\end{tabular}}\n";
        return $tex;
    }

}
