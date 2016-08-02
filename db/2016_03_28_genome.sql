CREATE TABLE `biocore`.`ngs_genome` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `genome` VARCHAR(100) NULL DEFAULT NULL,
  `build` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`));

INSERT INTO `biocore`.`ngs_genome` (`id`, `genome`, `build`) VALUES ('1', 'human', 'hg19');
INSERT INTO `biocore`.`ngs_genome` (`id`, `genome`, `build`) VALUES ('2', 'mouse', 'mm10');
INSERT INTO `biocore`.`ngs_genome` (`id`, `genome`, `build`) VALUES ('3', 'hamster', 'cho-k1');
INSERT INTO `biocore`.`ngs_genome` (`id`, `genome`, `build`) VALUES ('4', 'rat', 'rn5');
INSERT INTO `biocore`.`ngs_genome` (`id`, `genome`, `build`) VALUES ('5', 'zebrafish', 'danRer7');
INSERT INTO `biocore`.`ngs_genome` (`id`, `genome`, `build`) VALUES ('6', 'zebrafish', 'danRer10');
INSERT INTO `biocore`.`ngs_genome` (`id`, `genome`, `build`) VALUES ('7', 's_cerevisiae', 'sacCer3');
INSERT INTO `biocore`.`ngs_genome` (`id`, `genome`, `build`) VALUES ('8', 'c_elegans', 'ce10');
INSERT INTO `biocore`.`ngs_genome` (`id`, `genome`, `build`) VALUES ('9', 'cow', 'bosTau7');
INSERT INTO `biocore`.`ngs_genome` (`id`, `genome`, `build`) VALUES ('10', 'd_melanogaster', 'dm3');
INSERT INTO `biocore`.`ngs_genome` (`id`, `genome`, `build`) VALUES ('11', 'mousetest', 'mm10');
