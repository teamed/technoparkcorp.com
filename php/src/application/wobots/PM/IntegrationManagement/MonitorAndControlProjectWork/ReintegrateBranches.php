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
 * @version $Id: PayClosedOrders.php 888 2010-04-01 06:22:53Z yegor256@yahoo.com $
 *
 */

/**
 * Reintegrate branches with trunk
 *
 * We're searching all tickets for queries from architects. Queries
 * that contain requests for branch integration.
 *
 * @package wobots
 * @see Model_Decision::factory()
 */
class ReintegrateBranches extends Model_Decision_PM
{

    /**
     * Reintegrate branches, if architects request this operation
     *
     * @return string|false
     * @throws Exception If something happens 
     * @see Model_Decision::make()
     */
    protected function _make() 
    {
        // maybe there are no architects now in the project?
        if ($this->_project->staffAssignments->hasRole('architect')) {
            return 'No architect in the project';
        }

        // full list of tickets used in reintegration process
        $reintegrated = array();
        logg('Found %d tickets totally', count($this->_project->issues));
        foreach ($this->_project->issues as $issue) {
            // this ticket is not mine
            if ($issue->changelog->get('owner')->getValue() != Model_User::me()->email) {
                continue;
            }

            // author of the last message - architect?
            $architect = $issue->changelog->get('comment')->getLastAuthor();
            if (!isset($this->_project->staffAssignments[$architect])) {
                logg('%s email is not in staffAssignment, we ignore the message', $architect);
                continue;
            }

            $architect = $this->_project->staffAssignments[$architect];
            if (!$architect->hasRole($this->_project->staffAssignments->createRole('architect'))) {
                logg('%s is not an architect, we ignore the message', $architect->email);
                // we don't return a message back to this person
                // since other wobots might be interested in his
                // message
                continue;
            }

            $comment = $issue->changelog->get('comment')->getValue();
            $matches = array();
            if (!preg_match('/reintegrate\s+(\/branches\/[\w_-]+)\s+into\s+(\/trunk)/', $comment, $matches)) {
                logg("message is not clear: '%s'", $comment);
                continue;
            }
            
            $branch = $matches[1];
            $trunk = $matches[2];
            $username = escapeshellarg('');
            $password = escapeshellarg('');
            $key = $this->_project->name . '-reintegrate-' . $branch;
            $signature = 'success:' . md5($key);
            $script = "#!/bin/bash 
            svn co --username {$username} --password {$password} \\
                svn://svn.fazend.com/{$this->_project->name}{$trunk} project
            cd project/php
            phing -Dto.lint=true -Dto.phpcs=true -Dto.phpmd=true
            svn merge --reintegrate ^{$branch} ..
            phing
            svn ci --username {$username} --password {$password} \\
                -m 'refs #{$issue->id} - branch {$branch} merged into {$trunk}' ..
            svn del --username {$username} --password {$password}
                -m 'refs #{$issue->id} - branch {$branch} closed after integration with {$trunk}' ..
            cd ../..
            rm -rf project
            echo '{$signature}'
            ";
            
            logg(
                "branch: '%s', trunk: '%s', username: '%s', password: '%s'\n"
                . "key: '%s', signature: '%s'\n%s",
                $branch,
                $trunk,
                $username,
                $password,
                $key,
                $signature,
                $script
            );
            
            $asset = $this->_project->getAsset(Model_Project::ASSET_CODE);
            // $result = $asset->reintegrate($key, $script);
            $result = 123;
            
            if (is_numeric($result)) {
                logg('Asset returned PID of the process (%d), we shall wait', $result);
                continue;
            }

            // finished?
            if (strpos($result, $signature)) {
                $issue->say('done');
            } else {
                $issue->say("failed, see log:\n{{{{$result}}}}");
            }
            $issue->changelog->get('owner')->setValue($architect->email);
            
            $reintegrated[] = $issue->id;
        }
        return 'Reintegrated: ' . implode(', ', $reintegrated);
    }
    
}
