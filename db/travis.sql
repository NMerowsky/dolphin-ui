INSERT INTO users
(`username`,`clusteruser`,`role`,`name`,`email`,`email_toggle`,`institute`,`lab`,`owner_id`,`group_id`,`perms`)
VALUES
('travis','travis','travis','travis user','travis',0,'travis','travis',1,1,15);

INSERT INTO user_group (`u_id`,`g_id`,`owner_id`,`group_id`,`perms`,`date_created`,`date_modified`,`last_modified_user`)
VALUES
(209,1,1,1,15,NOW(),NOW(),1);