<?
$db->q("CREATE TABLE IF NOT EXISTS halls(
  id bigint not null auto_increment,
  cinema_id bigint,
  title varchar(255) default '',
  index idx(cinema_id),
  primary key(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
?>