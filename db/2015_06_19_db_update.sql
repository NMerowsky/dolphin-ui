ALTER TABLE `biocore`.`ngs_experiment_series` 
ADD COLUMN `organization` VARCHAR(255) NULL DEFAULT NULL AFTER `design`,
ADD COLUMN `lab` VARCHAR(255) NULL DEFAULT NULL AFTER `organization`,
ADD COLUMN `grant` VARCHAR(255) NULL DEFAULT NULL AFTER `lab`;

ALTER TABLE `biocore`.`ngs_protocols`
ADD COLUMN `crosslinking_method` VARCHAR(255) NULL DEFAULT NULL AFTER `library_construction`,
ADD COLUMN `fragmentation_method` VARCHAR(255) NULL DEFAULT NULL AFTER `crosslinking_method`,
ADD COLUMN `strand_specific` VARCHAR(255) NULL DEFAULT NULL AFTER `fragmentation_method`;

ALTER TABLE `biocore`.`ngs_lanes`
ADD COLUMN `sequencing_id` VARCHAR(255) NULL DEFAULT NULL AFTER `name`;

ALTER TABLE `biocore`.`ngs_samples`
ADD COLUMN `batch_id` VARCHAR(255) NULL DEFAULT NULL AFTER `title`,
ADD COLUMN `source_symbol` VARCHAR(255) NULL DEFAULT NULL AFTER `batch_id`,
ADD COLUMN `biosample_type` VARCHAR(255) NULL DEFAULT NULL AFTER `organism`,
ADD COLUMN `condition_symbol` VARCHAR(255) NULL DEFAULT NULL AFTER `genotype`,
ADD COLUMN `concentration` VARCHAR(255) NULL DEFAULT NULL AFTER `condition`,
ADD COLUMN `treatment_manufacturer` VARCHAR(255) NULL DEFAULT NULL AFTER `concentration`,
ADD COLUMN `donor` VARCHAR(255) NULL DEFAULT NULL AFTER `treatment_manufacturer`,
ADD COLUMN `time` VARCHAR(255) NULL DEFAULT NULL AFTER `donor`,
ADD COLUMN `biological_replica` VARCHAR(255) NULL DEFAULT NULL AFTER `time`,
ADD COLUMN `technical_replica` VARCHAR(255) NULL DEFAULT NULL AFTER `biological_replica`,
ADD COLUMN `spike_ins` VARCHAR(255) NULL DEFAULT NULL AFTER `technical_replica`;
