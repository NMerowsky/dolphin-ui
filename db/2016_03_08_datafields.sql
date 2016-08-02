INSERT INTO datafields (table_id, fieldname, title, type, len, owner_id, group_id, date_created, date_modified, last_modified_user) 
VALUES
(7, 'donor', 'Donor', 'text', 128, 1, 1, NOW(), NOW(), 1),
(7, 'target', 'Antibody Target', 'text', 128, 1, 1, NOW(), NOW(), 1),
(7, 'time', 'Time', 'text', 128, 1, 1, NOW(), NOW(), 1),
(7, 'biological_replica', 'Biological Rep', 'text', 128, 1, 1, NOW(), NOW(), 1),
(7, 'technical_replica', 'Technical Rep', 'text', 128, 1, 1, NOW(), NOW(), 1);

UPDATE `biocore`.`datafields` SET `fieldname`='samplename' WHERE `id`='44';
