---
--- @see bootstrap.php
---

CREATE TABLE IF NOT EXISTS `session` (
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of the row',
    `modified` INT COMMENT 'Whether it is modified',
    `lifetime` INT COMMENT 'Lifetime in seconds',
    `data` LONGTEXT COMMENT 'Session content',

    PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
AUTO_INCREMENT=1
COMMENT="User sessions";

