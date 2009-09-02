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
 * History of decisions
 *
 * @package Model
 */
class Model_Decision_History extends FaZend_Db_Table_ActiveRow_history {

    /**
     * Create new row
     *
     * @param Model_Wobot Wobot that initiated this decision
     * @param Model_Decision Decision that has to be protocoled
     * @return Model_Decision_History
     */
    public static function create(Model_Wobot $wobot, Model_Decision $decision) {
        $row = new Model_Decision_History();

        $row->wobot = $wobot->name;
        $row->context = $wobot->context;
        $row->result = $decision->decision;
        $row->protocol = $decision->log;
        $row->hash = $decision->hash;
        $row->save();

        return $row;
    }

    /**
     * Find next waiting decision
     *
     * @param Model_Wobot Wobot that is looking for new decision
     * @param array List of files that exist in the wobot
     * @return string
     */
    public static function findNextDecision(Model_Wobot $wobot, array $files) {

        sort($files);

        // the list is NOT empty
        assert(count($files));

        // first element of the list - we return it if nothing better found
        $first = $files[0];

        $latest = self::retrieve()
            ->setSilenceIfEmpty()
            ->where('wobot = ?', $wobot->name)
            ->where('context = ?', $wobot->context)
            ->order('created DESC')
            ->fetchRow();

        if (!$latest)
            return $first;

        while (count($files)) {
            $file = array_shift($files);

            // we found the file that ALREADY has been executed
            if ($latest->hash == Model_Decision::hash($file)) {
                if (count($files))
                    return $files[0];
                else
                    return $first;
            }
        }

        // nothing found in the list - return first element
        return $first;

    }

    /**
     * Retrieve all decisions for this particular wobot and context
     * 
     * @param Model_Wobot Wobot to work with
     * @return Model_Decision_History[]
     */
    public static function retrieveByWobot(Model_Wobot $wobot) {
        return self::retrieve()
            ->where('wobot = ?', $wobot->name)
            ->where('context = ?', $wobot->context)
            ->order('created DESC')
            ->setRowClass('Model_Decision_History')
            ->fetchAll();
    }

}
