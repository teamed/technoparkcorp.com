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

global $phpRackConfig;
$phpRackConfig = array(
    'dir' => dirname(__FILE__) . '/../rack-tests',
    'auth' => array(
        'username' => 'egor',
        'password' => 'tpc2',
    ),
    'notify' => array(
        'email' => array(
            'recipients' => 'bugs@tpc2.com',
        ),
    ),
);

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
include dirname(__FILE__) . '/../library/phpRack/bootstrap.php';
