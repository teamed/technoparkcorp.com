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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * Color mapper
 *
 * @package Model
 */
class Model_Colors
{

    const DARK_GRAY =      '#5a5a5a';
    const LIGHT_GRAY =     '#7d8f9b';
    const BLUE =           '#0093d0';
    const GRAY =           '#e7e9ea';
    const BLACK =          '#122632';
    const RED =            '#e31b23';
    const GREEN =          '#54b948';
    const ORANGE =         'orange';
    const YELLOW =         '#fff454';
    const WHITE =          '#ffffff';

    const RUP_BORDER =     '#9a0033';
    const RUP_BACKGROUND = '#ffffcc';

    /**
     * Creates and returns the color for the image
     *
     * @param int Handler of the image(after imagecreate())
     * @param string Name of the color
     * @return int Id of the color
     */
    public static final function getForImage($img, $color)
    {
        return imagecolorallocate(
            $img, 
            hexdec('0x' . $color[1] . $color[2]), 
            hexdec('0x' . $color[3] . $color[4]), 
            hexdec('0x' . $color[5] . $color[6])
        );
    }

}
