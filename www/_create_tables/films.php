<?
$db->q("CREATE TABLE IF NOT EXISTS films(
  id bigint not null auto_increment,
  title varchar(255) default '',
  primary key(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
?>