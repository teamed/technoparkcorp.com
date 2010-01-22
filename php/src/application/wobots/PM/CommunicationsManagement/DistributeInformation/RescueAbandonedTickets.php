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

/**
 * Rescue tickets that are abandoned
 *
 * This decision will find tickets that don't have 
 * any attention by their owners and will try to remind the
 * owners about the tickets. If the reminder is not working
 * tickets will be assigned to project PM.
 *
 * @package wobots
 */
class RescueAbandonedTickets extends Model_Decision_PM
{

    /**
     * Find and rescue
     *
     * @return string|false
     */
    protected function _make()
    {
        $closed = array();
        foreach ($this->_project->issues as $issue) {
            $lastDate = $issue->changelog->get('comment')->getLastDate();

            // ignore closed tickets
            if ($issue->isClosed()) {
                $closed[] = $issue->id;
                logg(
                    "Ticket #%d ignored since closed (%s) on %s", 
                    $issue->id, 
                    $issue->changelog->get('status')->getValue(),
                    $lastDate
                );
                continue;
            }
                
            // there was some activity for the last 72 hours
            if ($lastDate->isEarlier(Zend_Date::now()->subHour(72))) {
                logg('Ticket #%d was changed shortly, on %s', $issue->id, $lastDate);
                continue;
            }
                
            $owner = $issue->changelog->get('owner')->getValue();
            if (!isset($this->_project->staffAssignments[$owner])) {
                FaZend_Log::err('Unknown email in ticket #%d: %s', $issue->id, $owner);
                continue;
            }
            
            $owner = $this->_project->staffAssignments[$owner];
            $pmRole = $this->_project->staffAssignments->createRole('PM');
            
            // this ticket is already with PM
            if ($owner->hasRole($pmRole)) {
                logg('Ticket #%d is in long delay, but with PM now', $issue->id);
                continue;
            }
             
            // remind right now   
            // if ($issue->askOnce(
            //     'remind after ' . $lastDate,
            //     sprintf(
            //         'This ticket has no attention of the owner for the last %d hours. ' .
            //         'Looks like an abandoned ticket. Could you please provide some news here ' . 
            //         'or assign the ticket to another person? Thanks!',
            //         $lastDate->sub(Zend_Date::now())->getHours()
            //     )
            // )) {
            //     logg('Reminder added to issue #%d (owner: %s)', $issue->id, $owner->email);
            //     continue;
            // }
                
            // reassign the ticket to PM
            $pm = $this->_project->staffAssignments->PM;
            // $issue->reassign($pm->email);
            logg(
                'Issue #%d reassigned from owner (%s) to PM (%s), since abandoned', 
                $issue->id, 
                $owner->email,
                $pm->email
            );
        }
        
        logg('Closed tickets were ignored: %s', implode(', ', $closed));
    }
    
}
