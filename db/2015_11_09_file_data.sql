CREATE TABLE `biocore`.`ngs_filedata` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `sample_id` INT NULL DEFAULT NULL,
  `filename` VARCHAR(100) NULL DEFAULT NULL,
  `file_acc` VARCHAR(45) NULL DEFAULT NULL,
  `file_uuid` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `biocore`.`ngs_runparams` 
ADD COLUMN `run_flag` INT NULL DEFAULT NULL AFTER `run_group_id`;
