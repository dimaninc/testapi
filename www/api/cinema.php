<?
require "_header.php";

if ($_SERVER["REQUEST_METHOD"] != "GET")
  cinema_error("wrong req method");

$ar = array();

switch ($Z->m(3))
{
  case "schedule":
    $cinema_r = $Z->m(2) ? $db->r("cinemas", "WHERE clean_title='{$Z->m2}'") : false;
    $hall_r = isset($_GET["hall"]) && (int)$_GET["hall"] ? $db->r("halls", (int)$_GET["hall"]) : false;

    if (!$cinema_r)
      cinema_error("no such cinema");

    if (isset($_GET["hall"]) && !$hall_r)
      cinema_error("no such hall");

    if ($hall_r && $hall_r->cinema_id != $cinema_r->id)
      cinema_error("cinema/hall not match");

    $w_ar = array();
    $w_ar[] = $hall_r ? "s.hall_id='$hall_r->id'" : "s.hall_id in (SELECT id FROM halls WHERE cinema_id='$cinema_r->id')";

    $ar["sessions"] = array();

    $session_rs = $db->rs("sessions s INNER JOIN films f ON f.id=s.film_id", "WHERE ".join(" and ", $w_ar), "s.id,s.date,f.title");
    while ($session_r = $db->fetch($session_rs))
    {
      $ar["sessions"][] = array(
        "session_id" => $session_r->id,
        "film_title" => $session_r->title,
        "datetime" => $session_r->date,
      );
    }
    break;

  default:
    cinema_error("undefined method $Z->m3");
    break;
}

print_json($ar);
?>