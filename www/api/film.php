<?
require "_header.php";

if ($_SERVER["REQUEST_METHOD"] != "GET")
  cinema_error("wrong req method");

$ar = array();

switch ($Z->m(3))
{
  case "schedule":
    $film_r = $Z->m(2) ? $db->r("films", "WHERE clean_title='{$Z->m2}'") : false;

    if (!$film_r)
      cinema_error("no such film");

    $w_ar = array();
    $w_ar[] = "s.film_id='$film_r->id'";

    $ar["sessions"] = array();

    $session_rs = $db->rs("sessions s INNER JOIN halls h ON h.id=s.hall_id INNER JOIN cinemas c ON c.id=h.cinema_id", "WHERE ".join(" and ", $w_ar), "s.id,s.hall_id,s.date,c.title");
    while ($session_r = $db->fetch($session_rs))
    {
      $ar["sessions"][] = array(
        "session_id" => $session_r->id,
        "hall_id" => $session_r->hall_id,
        "cinema_title" => $session_r->title,
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