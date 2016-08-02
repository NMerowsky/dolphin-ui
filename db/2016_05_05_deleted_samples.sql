CREATE TABLE `ngs_deleted_samples` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `sample_id` INT NULL DEFAULT NULL,
  `samplename` VARCHAR(100) NULL DEFAULT NULL,
  `lane_id` INT NULL DEFAULT NULL,
  `experiment_series_id` INT NULL DEFAULT NULL,
  `user_delete` INT NULL DEFAULT NULL,
  `date` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`));
