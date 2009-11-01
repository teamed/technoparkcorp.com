---
--- Record for a performer
---

CREATE TABLE IF NOT EXISTS `record` (
    
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of this record',
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When it was created',    
    `supplier` INT NOT NULL COMMENT 'Id of the supplier',

    `approved` BOOL COMMENT 'Is he/she approved? (NULL=waiting, 0=reject, 1=approve)',
    `mark` INT COMMENT 'Mark given, <0 means negative, >0 means positive',

    `text` LONGTEXT NOT NULL COMMENT 'Text description of the record',
    `author` VARCHAR(150) NOT NULL COMMENT 'Email of the author (or just a name)',
    `file` VARCHAR(255) COMMENT 'Relative file name',

    PRIMARY KEY (`id`),
    
    -- file may be linked to ONLY one record
    UNIQUE(`file`),

    CONSTRAINT `fk_record_supplier` FOREIGN KEY (`supplier`) REFERENCES `supplier` (`id`) ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

