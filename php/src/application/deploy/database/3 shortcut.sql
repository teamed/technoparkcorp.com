---
--- Shortcuts used in sharedDoc() helper 
---

CREATE TABLE IF NOT EXISTS `shortcut` (
    
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of the shortcut row',
    
    `user` VARCHAR(255) NOT NULL COMMENT 'User to get an access to the document',
    `document` VARCHAR(25) NOT NULL COMMENT 'Document to be shared',

    PRIMARY KEY (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

