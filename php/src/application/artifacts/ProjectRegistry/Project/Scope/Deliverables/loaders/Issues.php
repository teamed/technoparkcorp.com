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

require_once 'artifacts/ProjectRegistry/Project/Scope/Deliverables/loaders/Abstract.php';
require_once 'artifacts/ProjectRegistry/Project/Scope/Deliverables/types/Deliverables/Abstract.php';

/**
 * Load all issues from defect tracking DB and find traceability in them
 *
 * @package Artifacts
 */
class DeliverablesLoaders_Issues extends DeliverablesLoaders_Abstract
{
    
    /**
     * Validate strongly against hash?
     *
     * @var boolean
     */
    protected static $_strictValidation = true;
    
    /**
     * Shall we strongly validate hash?
     *
     * @param boolean
     * @return void
     */
    public static function setStrictValidation($strictValidation) 
    {
        self::$_strictValidation = $strictValidation;
    }
    
    /**
     * Load all tickets and understand them
     *
     * @return void
     **/
    public function load() 
    {
        $this->_loadFirst('srs');
        
        logg('Issues loading started...');
        $project = $this->_deliverables->ps()->parent;
            
        foreach ($project->issues as $issue) {
            // add it to the list of deliverables
            $deliverable = theDeliverables::factory('Defects_Issue', '#' .  $issue->id);
            
            // add description to the deliverable
            $deliverable->attributes['description']
                ->add($issue->changelog->get('summary')->getValue());
            
            // add deliverable to the project's collection    
            $project->deliverables->add($deliverable);

            // find deliverables attributes
            $this->_findAttributes($project, $issue, $deliverable);
            
            // find all links from this issue to others
            $this->_findTraceabilityLinks($project, $issue, $deliverable);
        }
        logg(
            'Issues loading finished, %d tickets processed',
            count($project->issues)
        );
    }
    
    /**
     * Find attributes
     *
     * @param theProject Project to work with
     * @param Model_Asset_Defects_Issue_Abstract Issue just found
     * @param Deliverables_Abstract Deliverable (issue) just created
     * @return void
     */
    protected function _findAttributes(
        theProject $project, 
        Model_Asset_Defects_Issue_Abstract $issue,
        Deliverables_Abstract $deliverable)
    {
        // associative array, where keys are TAG-REGEXs used in tickets
        // and values are arrays of arrays. every array in that lists
        // includes three elements: 
        //   - "deliverable" - name of deliverable, like "R5.4"
        //   - "attribute" - attribute to set, like "accepted"
        //   - .. maybe something else later
        // we extend this array while going through changes, and 
        // deduct elements from this array when we find comments
        // with expected regexps.
        $tags = array();
        
        $changes = $issue->changelog->get('comment')->getChanges();
        foreach ($changes as $change) {
            $text = $change->value;
            
            // maybe some approval appeared?
            foreach ($tags as $regex=>$toApprove) {
                foreach ($toApprove as $tag) {
                    if (preg_match("/agree:{$regex}/", $text)) {
                        $project->deliverables[$tag['deliverable']]->attributes[$tag['attribute']]->add(
                            $deliverable->name,
                            $change->date,
                            $change->author,
                            sprintf(
                                "'%s' attribute set",
                                $tag['attribute']
                            )
                        );
                    }
                }
            }
            
            // is there any PRE-text?
            $matches = array();
            if (!preg_match('/\{{3}(.*)\}{3}/s', $text, $matches)) {
                continue;
            }
            $tagRegex = $this->_getHashRegex($text);
            if (!preg_match("/```(\\w+):{$tagRegex}'''/", $text)) {
                continue;
            }
            
            $request = $matches[1];
            
            // the request has valid information inside?
            $deliverableRegex = substr(Deliverables_Abstract::REGEX, 1, -1);
            if (!preg_match_all(
                "/({$deliverableRegex}) is (\\w+), in rev\\.\\d+:\\n(.*?)\\n\\s*\\n/s", 
                $request . "\n\n", 
                $matches
            )) {
                continue;
            }
            // we store everything which is WAITING for approval now
            $tags[$tagRegex] = array();
            
            // requests was sent for sure
            foreach ($matches[1] as $id=>$name) {
                $project->deliverables[$name]->attributes[$matches[2][$id] . '-request']->add(
                    $deliverable->name,
                    $change->date,
                    $change->author,
                    sprintf(
                        "'%s' attribute requested, together with: %s",
                        $matches[2][$id],
                        implode(', ', $matches[1])
                    )
                );
                $tags[$tagRegex][] = array(
                    'deliverable' => $name,
                    'attribute' => $matches[2][$id],
                );
            }
        }
    }
    
    /**
     * Calculate cash from the given text
     *
     * @param strin Text
     * @return string
     */
    protected function _getHashRegex($text) 
    {
        if (self::$_strictValidation) {
            return substr(md5($text), 0, 6); // first 6 symbols 
        } else {
            return '\d+';
        }
    }
    
    /**
     * Find traceability links in the issue and add them to deliverable
     *
     * @param theProject Project to work with
     * @param Model_Asset_Defects_Issue_Abstract Issue just found
     * @param Deliverables_Abstract Deliverable (issue) just created
     * @return void
     */
    protected function _findTraceabilityLinks(
        theProject $project, 
        Model_Asset_Defects_Issue_Abstract $issue, 
        Deliverables_Abstract $deliverable)
    {
        // we're building a list of deliverables mentioned in this ticket
        $mentioned = array();
        $changes = $issue->changelog->get('comment')->getChanges();
        foreach ($changes as $change) {
            $matches = array();
            if (!preg_match_all(Deliverables_Abstract::REGEX, $change->value, $matches)) {
                continue;
            }
            foreach ($matches[0] as $match) {
                if (isset($project->deliverables[$match])) {
                    $mentioned[$match] = true;
                }
            }
        }
        $mentioned = array_keys($mentioned);

        // make bi-directional links between them
        foreach ($mentioned as $name) {
            $project->traceability->add(
                new theTraceabilityLink(
                    $deliverable,
                    $project->deliverables[$name],
                    0.05,
                    1,
                    "mentioned in {$deliverable->name}: " . $issue->changelog->get('summary')->getValue()
                )
            );
        }
    } 
    
}
