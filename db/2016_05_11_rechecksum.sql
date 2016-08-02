ALTER TABLE `ngs_fastq_files` 
ADD COLUMN `checksum_recheck` VARCHAR(255) NULL DEFAULT NULL AFTER `backup_checksum`;