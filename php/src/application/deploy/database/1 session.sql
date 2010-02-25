---
--- @see bootstrap.php
---

CREATE TABLE IF NOT EXISTS `session` (
    `id` VARCHAR(32) NOT NULL COMMENT 'Unique ID of the row',
    `modified` INT COMMENT 'Whether it is modified',
    `lifetime` INT COMMENT 'Lifetime in seconds',
    `data` LONGTEXT COMMENT 'Session content',

    PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COMMENT="User sessions";

