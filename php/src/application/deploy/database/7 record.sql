---
--- Record for a performer
---

CREATE TABLE IF NOT EXISTS `record` (
    
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of this record',
    `performer` INT NOT NULL COMMENT 'Id of the performer',
    `text` LONGTEXT NOT NULL COMMENT 'Text description of the record',
    `author` VARCHAR(150) NOT NULL COMMENT 'Email of the author (or just a name)',
    `file` VARCHAR(255) COMMENT 'Relative file name',

    PRIMARY KEY (`id`),
    
    -- file may be linked to ONLY one record
    UNIQUE(`file`),

    CONSTRAINT `fk_record_performer` FOREIGN KEY (`performer`) REFERENCES `performer` (`id`) ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

