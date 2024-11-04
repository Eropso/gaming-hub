CREATE TABLE `businessdb`.`users` (`id` INT NOT NULL 
AUTO_INCREMENT , `user` VARCHAR(254) NOT NULL , `username` 
VARCHAR(50) NOT NULL , `password` CHAR(255) NOT NULL , 
`reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
PRIMARY KEY (`id`), UNIQUE (`user`), UNIQUE (`username`)) 
ENGINE = InnoDB;