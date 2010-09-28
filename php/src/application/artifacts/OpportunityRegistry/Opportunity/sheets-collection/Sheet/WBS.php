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
 * @version $Id: Vision.php 837 2010-03-16 14:37:24Z yegor256@yahoo.com $
 *
 */

require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/Sheet/Abstract.php';

/**
 * WBS.
 *
 * @package Artifacts
 */
class Sheet_WBS extends Sheet_Abstract
{
    
    /**
     * Defaults
     *
     * @var array
     * @see __get()
     */
    protected $_defaults = array(
        'percentage' => 85,
    );
    
    /**
     * Render ordered list.
     *
     * @return string LaTeX
     */
    public function getSpreadsheet() 
    {
        if (!isset($this->sheets['ROM'])) {
            FaZend_Exception::raise(
                'Sheet_WBS_InsufficientDataException',
                "ROM is mandatory for WBS"
            );
        }
        $budget = $this->sheets['ROM']->lowBoundary;
        $toShow = $this->percentage * $budget / 100;
        
        $tex = "{\\linespread{1.0}\\selectfont\n"
            . "\\begin{tabular}{l>{\\raggedright}p{30em}r}\n"
            . "\\hline Code & Description & Hours \\\\ \\hline \n";
        $packages = $this->_loadPackages();
        $shown = 0;
        foreach ($packages as $p) {
            $share = intval($p['share'] * $budget);
            if ($share <= 0) {
                continue;
            }
            $shown += $share;
            if ($shown > $toShow) {
                break;
            }
            $tex .= sprintf(
                "%s & %s & %d \\\\ \n",
                $p['code'],
                $p['description'],
                $share
            );
        }
        $tex .= "\\hline\n\\end{tabular}}\n";
        return $tex;
    }
    
    /**
     * Load list of packages from CSV file. Packages will be
     * balances and their total weight will be equal to "percentage".
     *
     * @return array
     */
    protected function _loadPackages() 
    {
        $packages = array();
        $lines = file(dirname(__FILE__) . '/WBS/packages.csv');
        foreach ($lines as $id=>$line) {
            $exp = array_map('trim', explode(';', trim($line, "\n\t\r ")));
            if (count($exp) == 1) {
                continue;
            }
            if (count($exp) < 2) {
                FaZend_Exception::raise(
                    'Sheet_WBS_InsufficientDataException',
                    "Error in line #{$id}"
                );
            }
            $packages[] = array(
                'code' => $exp[0],
                'header' => $exp[1],
                'share' => isset($exp[2]) ? $exp[2] : 0,
                'description' => isset($exp[3]) ? $exp[3] : '',
            );
        }
        $sum = 0;
        foreach ($packages as $p) {
            $sum += $p['share'];
        }
        foreach ($packages as &$p) {
            $p['share'] = $p['share'] / $sum;
        }
        usort($packages, create_function('$a, $b', 'return $a["share"] < $b["share"];'));
        return $packages;
    }
    
}
