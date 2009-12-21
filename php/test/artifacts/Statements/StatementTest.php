<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

require_once 'FaZend/Test/TestCase.php';

/**
 * Test theStatement class
 *
 * @package test
 */
class theStatementTest extends FaZend_Test_TestCase
{

    public function testVolumeAndBalanceWork() 
    {
        require_once 'Mocks/artifacts/Statements/Statement.php';
        $statement = Mocks_theStatement::get();
        
        $volume = $statement->volume;
        $this->assertTrue($volume instanceof Model_Cost, 'Volume is not as Model_Cost, why?');
        
        $balance = $statement->balance;
        $this->assertTrue($balance instanceof Model_Cost, 'Balance is not as Model_Cost, why?');
    }

    public function testPaymentCollectionWorks() 
    {
        require_once 'Mocks/artifacts/Statements/Statement.php';
        $statement = Mocks_theStatement::get();
        $this->assertTrue(count($statement) > 0, 'Statement is empty, why?');
    }

    public function testAsTextWorks() 
    {
        require_once 'Mocks/artifacts/Statements/Statement.php';
        $statement = Mocks_theStatement::get();
        
        $this->assertTrue(count($statement) > 0, 'Statement is empty, why?');
        logg('There are ' . count($statement) . ' payments in the statement');
        
        $this->assertTrue(strlen($statement->asText) > 0, 'Statement asText() is empty, why?');
    }

    public function testSendByEmailWorks() 
    {
        require_once 'Mocks/artifacts/Statements/Statement.php';
        $statement = Mocks_theStatement::get();
        $balance = $statement->balance->usd;
        
        // make it positive
        if ($balance <= 0) {
            require_once 'Mocks/artifacts/Statements/Statement/Payment.php';
            Mocks_thePayment::make($statement->supplier, -$balance + 50);
        }
            
        $statement->sendByEmail();
    }

}