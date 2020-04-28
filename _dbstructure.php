<?php

$_dbstru = array(


  'Projects' =>
      "id int(10)  NOT NULL,
  name text,
  self_link text,
  em_login varchar(80) default '',
  em_first_name varchar(80) default '',
  em_last_name varchar(80) default '',
  amount decimal(13,2) default 0,
  curr  varchar(7) default '',
  PRIMARY KEY PID (id)
  ",

  'Skills_prj' =>
  "skill_id int(10)  NOT NULL,
   project_id int(10) NOT NULL,
   KEY SPID (skill_id),
   KEY PSID (project_id)
  ",

  'Skills_tbl' =>
  "skill_id int(10)  NOT NULL,
   skill_name varchar(255) default '',
   PRIMARY KEY SID (skill_id)
  "


);
