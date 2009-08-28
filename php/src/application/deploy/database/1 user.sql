---
--- 
---

CREATE TABLE IF NOT EXISTS `user` (
    
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of the user',
    `email` VARCHAR(80) NOT NULL COMMENT 'Unique user email',
    `password` VARCHAR(32) NOT NULL COMMENT 'User password hash',
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

