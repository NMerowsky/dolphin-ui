INSERT INTO user_group (`u_id`,`g_id`,`owner_id`,`group_id`,`perms`,`date_created`,`date_modified`,`last_modified_user`) VALUES (209,1,1,1,15,NOW(),NOW(),1);
UPDATE ngs_runparams SET owner_id = 209 where id = 4;
UPDATE ngs_runlist SET owner_id = 209 where id IN (19,20);