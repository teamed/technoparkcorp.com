---
--- Ability of a Performer to take participation in a project
---

CREATE TABLE IF NOT EXISTS `ability` (
    
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of this ability',
    `performer` INT NOT NULL COMMENT 'Id of the performer',
    `role` VARCHAR(150) NOT NULL COMMENT 'Text name of role which could be filled by the performer',
    `price` VARCHAR(10) NOT NULL COMMENT 'Price per hour in this role',

    PRIMARY KEY (`id`),
    UNIQUE(`performer`, `role`),

    CONSTRAINT `fk_ability_performer` FOREIGN KEY (`performer`) REFERENCES `performer` (`id`) ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

