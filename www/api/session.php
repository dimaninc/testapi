<?
require "_header.php";

if ($_SERVER["REQUEST_METHOD"] != "GET")
  cinema_error("wrong req method");

$ar = array();

switch ($Z->m(3))
{
  case "places":
    $session_r = (int)$Z->m(2) ? $db->r("sessions", (int)$Z->m(2)) : false;

    if (!$session_r)
      cinema_error("no such session");

    $w_ar = array();
    $w_ar[] = "(SELECT COUNT(id) FROM tickets t WHERE t.place_id=p.id)='0'";
    $w_ar[] = "hall_id='$session_r->hall_id'";

    $ar["places"] = array();

    $place_rs = $db->rs("places p", "WHERE ".join(" and ", $w_ar), "p.id,p.title");
    while ($place_r = $db->fetch($place_rs))
    {
      $ar["places"][] = array(
        "place_id" => $place_r->id,
        "place_title" => $place_r->title,
      );
    }
    break;

  default:
    cinema_error("undefined method $Z->m3");
    break;
}

print_json($ar);
?>