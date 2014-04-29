<?
$db->q("CREATE TABLE IF NOT EXISTS orders(
  id bigint not null auto_increment,
  uid varchar(32),
  index idx(uid),
  primary key(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
?>