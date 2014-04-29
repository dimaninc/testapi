<?
$db->q("CREATE TABLE IF NOT EXISTS tickets(
  id bigint not null auto_increment,
  order_id bigint,
  session_id bigint,
  place_id bigint,
  index idx(session_id,place_id),
  index idx2(order_id),
  primary key(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
?>