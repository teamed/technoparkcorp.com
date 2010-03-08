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
 * One response for staff
 *
 * @package Artifacts
 */
class theStaffResponse extends ArrayIterator
{

    /**
     * Show as a string
     *
     * @return string
     **/
    public function __toString()
    {
        if (!count($this))
            return 'nobody';
        
        $quality = 0;
        foreach ($this as $response) {
            $quality = max($quality, $response->quality);
        }
        return $quality . '%';
    }
    
    /**
     * Invite everybody in the list to the project
     *
     * @param theStaffRequest Request
     * @param string Message to add to the message
     * @return void
     */
    public function invite(theStaffRequest $request, $message) 
    {
        foreach ($this as $item) {
            // he is already here?
            if (isset($request->project->staffAssignments[$item->supplier->email])) {
                continue;
            }
            FaZend_Email::create('artifacts/SupplierRegistry/StaffResponse/invitation.tmpl')
                ->set('toEmail', $item->supplier->email)
                ->set('toName', $item->supplier->email)
                ->set('fromEmail', 'pmo@tpc2.com')
                ->set('fromName', 'TechnoPark Corp.')
                ->set('cc', array('yegor@tpc2.com'=>'Yegor Bugayenko'))
                ->set('message', $message)
                ->send();
        }
    }
    
    /**
     * Hook adding function in order to sort the array on-fly
     *
     * @param mixed Index in array
     * @param mixed Value to add
     * @return void
     **/
    public function offsetSet($index, $value) 
    {
        parent::offsetSet($index, $value);
        $this->uasort(create_function('$a, $b', 'return $a->quality < $b->quality;'));
    }

}
