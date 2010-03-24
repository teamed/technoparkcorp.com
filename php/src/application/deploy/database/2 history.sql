---
--- @see Model_Decision, Model_Decision_History
---

CREATE TABLE IF NOT EXISTS `history` (
    
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of the history row',
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When it was created',
    `hash` VARBINARY(255) COMMENT 'FileName+MD5 of decision file name',

    `wobot` VARCHAR(25) NOT NULL COMMENT 'Name of wobot',
    `context` VARCHAR(255) COMMENT 'Colon-separated list of context variables',

    `result` VARCHAR(255) COMMENT 'Decision made, NULL = failed decision',
    `protocol` LONGTEXT NOT NULL COMMENT 'Protocol of decision',
    `cost` INT COMMENT 'CPU resources spent on this decision (in seconds)',

    PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
AUTO_INCREMENT=1
COMMENT="List of decisions made by wobots";

