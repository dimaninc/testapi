<?
$db->q("CREATE TABLE IF NOT EXISTS cinemas(
  id bigint not null auto_increment,
  title varchar(255) default '',
  clean_title varchar(255) default '',
  unique index ct(clean_title),
  primary key(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
?>