<?php
/**
 * FaZend Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available 
 * through the world-wide-web at this URL: http://www.fazend.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@fazend.com so we can send you a copy immediately.
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id: LongLine.php 1747 2010-03-17 19:17:38Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * Cut the line to a required length
 *
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_LongLine
{

    /**
     * Cut the line to a required length
     *
     * @return string
     */
    public function longLine($line, $length = 50)
    {
        return cutLongLine($line, $length);
    }

}
