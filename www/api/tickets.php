<?
require "_header.php";

if ($_SERVER["REQUEST_METHOD"] != "POST")
  cinema_error("wrong req method");

$ar = array();

switch ($Z->m(2))
{
  case "buy":
    $session_r = isset($_GET["session"]) && (int)$_GET["session"] ? $db->r("sessions", (int)$_GET["session"]) : false;
    $place_rs = $session_r && isset($_GET["places"])
      ? $db->rs("places p LEFT JOIN tickets t ON t.place_id=p.id AND t.session_id='$session_r->id'",
          "WHERE p.id IN (".addslashes($_GET["places"]).")",
          "p.id,t.id AS ticket_id"
        )
      : false;

    if (!$place_rs || !$db->count($place_rs))
      cinema_error("no such places");

    // checking if there already booked places
    $booked_ar = array();

    while ($place_r = $db->fetch($place_rs))
    {
      if ($place_r->ticket_id)
        $booked_ar[] = $place_r->id;
    }

    if ($booked_ar)
      cinema_error("places are already booked: ".join(", ", $booked_ar));
    //

    // making an order
    do {
      $ar["code"] = get_unique_id();
    } while ($db->r("orders", "WHERE uid='{$ar["code"]}'"));

    $order_id = $db->insert("orders", array(
      "uid" => $ar["code"],
    ));

    $db->reset($place_rs);
    while ($place_r = $db->fetch($place_rs))
    {
      $db->insert("tickets", array(
        "order_id" => $order_id,
        "session_id" => $session_r->id,
        "place_id" => $place_r->id,
      ));
    }
    //

    break;

  case "reject":
    $order_r = $Z->m(3) ? $db->r("orders", "WHERE uid='{$Z->m3}'") : false;

    if (!$order_r)
      cinema_error("no such order");

    $session_r = $db->r("sessions", "WHERE id in (SELECT session_id FROM tickets WHERE order_id='$order_r->id')");

    if (strtotime($session_r->date) - time() < 3600)
      cinema_error("unable to reject - session will start in less than an hour");

    $db->delete("tickets", "WHERE order_id='$order_r->id'");
    $db->delete("orders", "WHERE id='$order_r->id' LIMIT 1");

    $ar["ok"] = 1;

    break;

  default:
    cinema_error("undefined method $Z->m2");
    break;
}

print_json($ar);
?>