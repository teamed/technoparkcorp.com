<?php
/**
 * @version $Id$
 */

class Mocks_Shared_Trac_Ticket_RequirementsAttributesTicket extends Mocks_Shared_Trac_Ticket
{

    public function getTracChangelog() 
    {
        return $this->_makeChangelog(
            array(
                array('status', 'open'),
                array('owner', 'john@example.com'),
                array('summary', 'to accept R1 and R1.2'), 
                array('comment', 'you should review and accept R1 and R1.2 requirements'), 
                array('description', 'if you agree, please accept, if not, please post your comments'),
                array(
                    'comment',
                    '
                    Dear requirements reviewers (incl. john@example.com and
                    willy@example.com), please confirm that (some other text here...):
                    
                    {{{
                    R1 is accepted, in rev.13:
                    When in public site page, ActorUser enters changes to UserAccount,
                    SUD writes changes to database
                    
                    R1.2 is accepted, in rev.6:
                    When viewing UserInvoice, ActorUser enters changes to UserInvoice,
                    SUD writes changes to system files.
                    }}}
                    
                    If you agree with that revisions, please reply in this 
                    ticket with a short message saying ```agree:887\'\'\'.
                    ',
                ),
                array(
                    'owner',
                    'peter@example.com',
                ),
                array(
                    'comment',
                    'I dont think that it\'s correct... Let\'s discuss by phone...',
                    'peter@example.com'
                ),
                array(
                    'owner',
                    'john@example.com',
                ),
                array(
                    'comment', 
                    'I think that I can accept it. Here is the token: agree:887',
                    'john@example.com'
                ),
                array(
                    'owner',
                    'willy@example.com'
                ),
                array(
                    'comment',
                    'I think that I can accept it. Here is the token: agree:887',
                    'willy@example.com'
                ),
            )
        );
    }
    
    protected function _makeChangelog(array $fields)
    {
        $changelog = array();
        foreach ($fields as $field) {
            $changelog[] = array(
                0 => Zend_Date::now()->get(self::TRAC_DATE),
                1 => isset($field[2]) ? $field[2] : Model_User::me()->email,
                2 => $field[0],
                3 => false,
                4 => $field[1],
            );
        }
        return $changelog;
    }
    
}
