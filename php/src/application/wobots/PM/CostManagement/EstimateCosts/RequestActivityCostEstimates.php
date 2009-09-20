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
 * Request estimates of activities
 *
 * @package wobots
 */
class RequestActivityCostEstimates extends Model_Decision {

    /**
     * Make decision
     *
     * @return string|false
     */
    protected function _make() {

        $requested = 0;
        foreach ($this->project->activities as $activity) {

            // if this particular activity requires cost estimate request
            // to be sent - do it!
            if ($activity->isAssigned() &&
                !$activity->isCostEstimated() &&
                !$activity->isCostEstimateRequested()) {

                // request cost esimate
                $activity->requestCostEstimate();

                // protocol this operation
                logg("Activity {$activity->name} is assigned to " .
                    "{$activity->performer}, but cost is not yet estimated/requested. Estimate requested.");

                // calculate made requests
                $requested++;
            }
            
        }

        if (!$requested)
            return false;

        return plural("Requested cost estimate[s] for {$requested} activit[ies]", $requested);

    }

}
