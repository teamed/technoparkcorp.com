--
-- One payment made to someone or received from someone. Every
-- statement is attached to an email and could have an optional
-- param: context. It could be a project name.
--

CREATE TABLE IF NOT EXISTS `payment` (
    
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of the payment',
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When it was created',    
    `supplier` VARCHAR(135) COMMENT 'Email of the related supplier, if any',
    `context` VARCHAR(50) COMMENT 'Project name, for example',
    
    `rate` VARCHAR(15) COMMENT 'Active user rate at the moment of payment',
    `original` VARCHAR(20) COMMENT 'Payment amount with currency',
    `amount` INT NOT NULL COMMENT 'Payment amount in USD cents',

    `reason` VARCHAR(250) NOT NULL COMMENT 'Reason of payment, unique',

    `details` MEDIUMTEXT NOT NULL COMMENT 'Details of the payment made',
    
    PRIMARY KEY (`id`),
    UNIQUE(`supplier`, `context`, `reason`)

) 
    ENGINE=InnoDB 
    DEFAULT CHARSET=utf8 
    AUTO_INCREMENT=1
    COMMENT="One single payment to/form somebody";

