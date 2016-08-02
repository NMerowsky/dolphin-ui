ALTER TABLE `ngs_fastq_files` 
ADD COLUMN `aws_status` INT NULL DEFAULT NULL AFTER `backup_checksum`;
