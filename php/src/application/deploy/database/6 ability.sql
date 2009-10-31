---
--- Ability of a Supplier to take participation in a project
---

CREATE TABLE IF NOT EXISTS `ability` (
    
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of this ability',
    `supplier` INT NOT NULL COMMENT 'Id of the supplier',
    `role` VARCHAR(150) NOT NULL COMMENT 'Text name of role which could be filled by the supplier',
    `price` VARCHAR(10) NOT NULL COMMENT 'Price per hour in this role',

    PRIMARY KEY (`id`),
    UNIQUE(`supplier`, `role`),

    CONSTRAINT `fk_ability_supplier` FOREIGN KEY (`supplier`) REFERENCES `supplier` (`id`) ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

