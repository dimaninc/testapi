<?
$db->q("CREATE TABLE IF NOT EXISTS places(
  id bigint not null auto_increment,
  hall_id bigint,
  title varchar(255) default '',
  index idx(hall_id),
  primary key(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
?>