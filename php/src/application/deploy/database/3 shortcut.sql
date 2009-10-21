---
--- Shortcuts used in sharedDoc() helper 
---

CREATE TABLE IF NOT EXISTS `shortcut` (
    
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of the shortcut row',
    
    `emails` LONGTEXT NOT NULL COMMENT 'Serialized list of emails who will access the document',
    `document` VARCHAR(255) NOT NULL COMMENT 'Document to be shared',

    `author` VARCHAR(255) NOT NULL COMMENT 'Who shared the document?',
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When it was created',

    `params` LONGTEXT COMMENT 'Serialized array of params, if necessary',
    
    PRIMARY KEY (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

