---
--- Payments 
---

CREATE TABLE IF NOT EXISTS `payment` (
    
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of the payment',
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When it was created',    
    `user` VARCHAR(120) COMMENT 'Email of the user related to this payment',
    `context` VARCHAR(50) COMMENT 'Project name, for example',
    
    `rate` VARCHAR(15) COMMENT 'Active user rate at the moment of payment',
    `original` VARCHAR(20) COMMENT 'Payment amount with currency',
    `amount` INT NOT NULL COMMENT 'Payment amount in USD cents',

    `reason` VARCHAR(250) NOT NULL COMMENT 'Reason of payment, unique',

    `details` MEDIUMTEXT NOT NULL COMMENT 'Details of the payment made',
    
    PRIMARY KEY (`id`),
    UNIQUE(`reason`),

) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

