<?
function cinema_error($message)
{
  $ar = array(
    "ok" => 0,
    "message" => $message,
  );

  print_json($ar);

  die();
}
?>