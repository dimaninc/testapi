<?
error_reporting(E_ALL);

require "_cfg/_functions.php";
require "_cfg/common.php";

require "_create_tables/cinemas.php";
require "_create_tables/halls.php";
require "_create_tables/places.php";
require "_create_tables/films.php";
require "_create_tables/sessions.php";
require "_create_tables/tickets.php";
require "_create_tables/orders.php";

echo "<pre>";
var_dump($db->log);
echo "</pre>";

/*
for ($hall_id = 1; $hall_id <= 6; $hall_id++)
for ($i = 1; $i < 30; $i++)
{
  $db->insert("places", array(
    "hall_id" => $hall_id,
    "title" => "Место №$i",
  ));
}
*/
?>