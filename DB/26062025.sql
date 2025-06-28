CREATE TABLE `users` (`id` INT NOT NULL AUTO_INCREMENT , `email` VARCHAR(100) NOT NULL , `password` VARCHAR(255) NOT NULL , `role` INT NOT NULL COMMENT '1=Admin,2=Customer' , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `users` CHANGE `role` `role` INT(11) NOT NULL COMMENT '1=Seller,2=Customer';

ALTER TABLE `users` ADD `first_name` VARCHAR(255) NOT NULL AFTER `id`, ADD `last_name` VARCHAR(255) NOT NULL AFTER `first_name`;

ALTER TABLE `users` ADD `phone` BIGINT NOT NULL AFTER `role`, ADD `dob` DATE NULL AFTER `phone`, ADD `gender` ENUM('Male','Female','Other','') NOT NULL AFTER `dob`;