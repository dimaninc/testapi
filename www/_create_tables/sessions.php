<?
$db->q("CREATE TABLE IF NOT EXISTS sessions(
  id bigint not null auto_increment,
  hall_id bigint,
  film_id bigint,
  date datetime,
  index idx(hall_id,film_id,date),
  primary key(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
?>