<?php

include '../library/FaZend/Application/index.php';

// save all changes made to POS, if they were made
FaZend_Pos_Properties::cleanPosMemory(true, true);