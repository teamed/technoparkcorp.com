---
--- Performer
---

CREATE TABLE IF NOT EXISTS `performer` (
    
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of the performer',
    `email` VARCHAR(120) NOT NULL COMMENT 'Email of the performer',
    `name` VARCHAR(150) NOT NULL COMMENT 'Full name of the person',

    PRIMARY KEY (`id`),
    UNIQUE(`email`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

