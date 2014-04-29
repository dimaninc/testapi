<?
// cfg.common
// (q) by diman inc.

require_class("diDB");

define("SECS_PER_DAY", 86400);
define("DIENCODING", "UTF8");
define("DIMAILENCODING", "UTF8");

$html_encodings_ar = array(
  "CP1251" => "windows-1251",
  "UTF8" => "utf-8",
);

// -[ mysql stuff ]-----------------------------------------------------------------
if (empty($db))
{
  if ($_SERVER["HTTP_HOST"] == "testapi")
  {
    $db = new diMYSQLi("localhost", "root", "", "testapi");
  }
  else
  {
    $db = new diMYSQLi("host", "user", "password", "dbname");
  }

  $db->q("SET NAMES ".DIENCODING);
  $db->set_charset(DIENCODING);
}
?>