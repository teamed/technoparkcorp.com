---
--- Supplier
---

CREATE TABLE IF NOT EXISTS `supplier` (
    
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of the performer',
    `email` VARCHAR(120) NOT NULL COMMENT 'Email of the supplier',
    `name` VARCHAR(80) NOT NULL COMMENT 'Full name of the person',

    `country` VARCHAR(2) NOT NULL COMMENT 'ISO-3166 two-letters country code',

    PRIMARY KEY (`id`),
    UNIQUE(`email`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

