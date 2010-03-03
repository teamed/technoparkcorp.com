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
 * @copyright Copyright (c) FaZend.com
 * @version $Id: YesNo.php 611 2010-02-07 07:43:45Z yegor256@yahoo.com $
 * @category FaZend
 */

/**
 * Show diff between two strings
 *
 * @package helpers
 */
class Helper_Diff
{

    /**
     * Show diff
     *
     * @return string
     */
    public function diff($source, $changes)
    {
        $from = tempnam(TEMP_PATH, 'panel2diff');
        $to = tempnam(TEMP_PATH, 'panel2diff');
        file_put_contents($from, $source);
        file_put_contents($to, $changes);
        $diff = shell_exec(
            'diff ' . escapeshellarg($from) . ' ' . escapeshellarg($to) . ' 2>&1'
        );
        unlink($from);
        unlink($to);
        return '<pre>' . $diff . '</pre>';
    }

}
