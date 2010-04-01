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

/**
 * Rescue tickets that are abandoned
 *
 * This decision will find tickets that don't have 
 * any attention by their owners and will try to remind the
 * owners about the tickets. If the reminder is not working
 * tickets will be assigned to project PM.
 *
 * @package wobots
 * @see Model_Decision::factory()
 */
class RescueAbandonedTickets extends Model_Decision_PM
{

    /**
     * Find and rescue
     *
     * @return string|false
     * @see Model_Decision::make()
     */
    protected function _make()
    {
        return 'disabled temporarily';
        
        logg('Found %d tickets totally', count($this->_project->issues));
     
        $rescued = array();
        $closed = array();
        foreach ($this->_project->issues as $issue) {
            if (count($rescued) >= 3)
                break;
            
            $lastDate = $issue->changelog->get('comment')->getLastDate();

            // maybe the date is NULL, which means that there are no comments yet
            if (is_null($lastDate)) {
                $lastDate = $issue->changelog->get('summary')->getLastDate();
            }

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
            
            $delayedHours = Zend_Date::now()->sub($lastDate)->getTimestamp() /
                (60 * 60);
                
            // there was some activity for the last 72 hours
            if ($delayedHours < 72) {
                logg(
                    'Ticket #%d was updated recently, on %s (%d hours ago)', 
                    $issue->id, 
                    $lastDate,
                    $delayedHours
                );
                continue;
            }
                
            $owner = $issue->changelog->get('owner')->getValue();
            if (!isset($this->_project->staffAssignments[$owner])) {
                FaZend_Log::err(
                    sprintf(
                        'Unknown email in ticket #%d: %s', 
                        $issue->id, 
                        $owner
                    )
                );
                continue;
            }
            
            $owner = $this->_project->staffAssignments[$owner];
            $pmRole = $this->_project->staffAssignments->createRole('PM');
            
            // this ticket is already with PM
            if ($owner->hasRole($pmRole)) {
                logg(
                    'Ticket #%d is in long delay (%d hours since %s), but with PM now (%s)', 
                    $issue->id,
                    $delayedHours,
                    $lastDate->get(Zend_Date::DATE_MEDIUM),
                    $owner
                );
                continue;
            }
             
            // remind right now   
            if ($issue->askOnce(
                'remind after ' . $lastDate,
                'Could you please provide some update here ' . 
                'or re-assign this ticket to another person? Thanks!'
            )) {
                logg('Reminder added to issue #%d (owner: %s)', $issue->id, $owner->email);
                $rescued[] = $issue->id;
                continue;
            }
                
            // reassign the ticket to PM
            $pm = $this->_project->staffAssignments->PM->random();
            $issue
                ->reassign($pm->email)
                ->say('I\' re-assigning the ticket since it looks abandonded for a long time');
            logg(
                'Issue #%d reassigned from owner (%s) to PM (%s), since abandoned for %dhrs', 
                $issue->id, 
                $owner->email,
                $pm->email,
                $delayedHours
            );
            $rescued[] = $issue->id;
        }
        
        if ($closed) {
            logg(
                '%d closed tickets were ignored: %s',
                count($closed), 
                cutLongLine(implode(', ', $closed))
            );
        }

        if (empty($rescued))
            return 'Nothing to rescue';

        return 'Tickets rescued: ' . implode(', ', $rescued);
    }
    
}
