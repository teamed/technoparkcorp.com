---
--- Skill of a Supplier
---

CREATE TABLE IF NOT EXISTS `skill` (
    
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of this skill',
    `supplier` INT NOT NULL COMMENT 'Id of the supplier',
    `name` VARCHAR(150) NOT NULL COMMENT 'Name of the skill',
    `level` INT UNSIGNED NOT NULL COMMENT 'Level of the skill in [0..100] interval',

    PRIMARY KEY (`id`),
    UNIQUE(`supplier`, `name`),

    CONSTRAINT `fk_skill_supplier` FOREIGN KEY (`supplier`) REFERENCES `supplier` (`id`) ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

