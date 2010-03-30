<?php
/**
 * @version $Id$
 */

require_once 'AbstractProjectTest.php';

class Deliverables_loaders_IssuesTest extends AbstractProjectTest
{

    public function setUp()
    {
        parent::setUp();
        $deliverables = $this->_project->deliverables;
        $deliverables->reload();
    }

    public function testAttributesAreLoadedFromTickets()
    {
        $this->assertTrue(
            $this->_project->deliverables['R1']->attributes['accepted-request']->isTrue(),
            'R1 was asked for acceptance, why false?'
        );

        $this->assertTrue(
            $this->_project->deliverables['R1']->attributes['accepted']->wasTrue(),
            'R1 is not accepted despite Mocks_Shared_Trac_Ticket_RequirementsAttributesTicket.php'
        );
    }

}