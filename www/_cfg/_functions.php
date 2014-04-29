<?
/*
    // dimaninc
*/

class diEmptyClass {}

date_default_timezone_set("Etc/GMT-4");

// for dierror
define("DIE_NOTICE", 0);
define("DIE_WARNING", 1);
define("DIE_FATAL", 2);
//

$image_type_to_ext_ar = array(
  1 => "gif",
  2 => "jpeg",
  3 => "png",
);

$days_in_mon_ar = array(
  false => array(31,28,31,30,31,30,31,31,30,31,30,31),
  true  => array(31,29,31,30,31,30,31,31,30,31,30,31),
);

$month_titles = array("января", "февраля", "марта", "апреля", "мая", "июня",
  "июля", "августа", "сентября", "октября", "ноября", "декабря");

$eng_month_titles = array("january", "febrary", "march", "april", "may", "june",
  "july", "august", "september", "october", "november", "december");

// в базу
function str_in($str)
{
  global $db;

  $str = trim($str);
  $str = isset($db) ? $db->escape_string($str) : addslashes($str);
  //$str = mysql_real_escape_string($str);

  return $str;
}

// из базы
function str_out($str, $replace_amp = false)
{
  //$str = stripslashes($str);
  if ($replace_amp) $str = str_replace('&', '&amp;', $str);
  $str = str_replace('"', '&quot;', $str);
  //$str = str_replace("'", '&apos;', $str);
  $str = str_replace('<', '&lt;', $str);
  $str = str_replace('>', '&gt;', $str);

  return $str;
}

function lead0($num)
{
  if (strlen($num) == 1) $num = "0".$num;

  return $num;
}

function add_ending_slash($path)
{
  if (substr($path, strlen($path) - 1, 1) != "/") $path .= "/";

  return $path;
}

function remove_ending_slash($path)
{
  if (substr($path, strlen($path) - 1, 1) == "/") $path = substr($path, 0, strlen($path) - 1);

  return $path;
}

function create_folders_chain($start_path, $path_to_create, $mod = 0775)
{
  $_folders = explode("/", $path_to_create);
  $_path = $start_path;

  $oldumask = umask(0) ;
  foreach ($_folders as $f)
  {
    if ($f)
    {
      $_path = add_ending_slash($_path);
      $_path .= $f;

      if (!is_dir($_path)) mkdir($_path, $mod);
    }
  }
  umask($oldumask);
}

function time_to_str($hours, $mins)
{
  if (strlen($hours) == 1) $hours = "0".$hours;
  if (strlen($mins) == 1) $mins = "0".$mins;

  return $hours.":".$mins;
}

function date_to_str($day, $month, $year)
{
  if (strlen($day) == 1) $day = "0".$day;
  if (strlen($month) == 1) $month = "0".$month;

  return "$day.$month.$year";
}

function date_to_str2($day, $month, $year)
{
  if (strlen($day) == 1) $day = "0".$day;
  if (strlen($month) == 1) $month = "0".$month;

  return "$year.$month.$day";
}

function date_to_str_wordmonth($day, $month, $year)
{
  global $month_titles;

  if (strlen($day) == 1) $day = "0".$day;

  $smonth = $month_titles[$month - 1];

  return "$day $smonth $year";
}

function remake_paramline($all_params, $except_these_keys)
{
  $all_keys = array();
  $all_vals = array();

  $i = 0;

  while(list($key, $val) = each($all_params))
  {
    $key = stripslashes($key);
    $val = stripslashes($val);
    $key = urlencode($key);
    $val = urlencode($val);

    $all_keys[] = $key;
    $all_vals[] = $val;

    $i++;
  }

  $s = "";

  for ($i = 0; $i < count($all_keys); $i++)
  {
    $is_this_bad = 0;

    for ($j = 0; $j < count($except_these_keys); $j++)
    {
      if (strtolower($all_keys[$i]) == strtolower($except_these_keys[$j]))
      {
        $is_this_bad = 1;

        break;
      }
    }

    if ($is_this_bad == 0) //такого параметра в списке запрещенных нет
    {
      if ($s != "") $s .= "&";

      $s .= strtolower($all_keys[$i])."=".$all_vals[$i];
    }
  }

  return $s;
}

function make_paramline($cur_params = array(), $new_params = array(), $kill_params = array())
{
  $cur_params = array_merge($cur_params, $new_params);

  $s = "";

  foreach ($cur_params as $k => $v)
  {
    if (!in_array($k, $kill_params))
    {
      if ($s) $s .= "&";

      $s .= "$k=$v";
    }
  }

  return $s;
}

function isleapyear($year)
{
  $r = ($year % 4 == 0 && $year % 100 != 0 || $year % 400 == 0)
    ? true
    : false;

  return $r;
}

function date_compare($d1, $m1, $y1, $d2, $m2, $y2)
{
  $r = 0;

  if ($y1 < $y2) { $r = -1; } else
  if ($y1 > $y2) { $r = 1;  } else
  if ($m1 < $m2) { $r = -1; } else
  if ($m1 > $m2) { $r = 1;  } else
  if ($d1 < $d2) { $r = -1; } else
  if ($d1 > $d2) { $r = 1;  }

  return $r;
}

function time_compare($h1, $m1, $h2, $m2)
{
  $r = 0;

  if ($h1 < $h2) { $r = -1; } else
  if ($h1 > $h2) { $r = 1;  } else
  if ($m1 < $m2) { $r = -1; } else
  if ($m1 > $m2) { $r = 1;  }

  return $r;
}

function getdayafter($dd, $mm, $yy, $shift)
{
  $stamp = mktime(0,0,0,$mm,$dd,$yy);

  $stamp += $shift * 86400; // 60 * 60 * 24  == one day length in seconds

  $dt = getdate($stamp);

  return array($dt["mday"], $dt["mon"], $dt["year"],
               "d" => $dt["mday"], "m" => $dt["mon"], "y" => $dt["year"]);
}

function getdaybefore($dd, $mm, $yy, $shift)
{
  return getdayafter($dd, $mm, $yy, -$shift);
}

function gettomorrow($dd, $mm, $yy)
{
  return getdayafter($dd, $mm, $yy, 1);
}

function getyesterday($dd, $mm, $yy)
{
  return getdaybefore($dd, $mm, $yy, 1);
}

function timediff($h_now, $m_now, $h_that, $m_that)
{
  $mm = ($h_that * 60) + $m_that - ($h_now * 60) - $m_now;

  if (abs($mm) > 12 * 60)
  {
    $mm += 24 * 60 * (int) ($mm / $mm);
  }

  $hh = (int) ($mm / 60);
  $mm = $mm % 60;

  return array($hh, $mm, "h" => $hh, "m" => $mm);
}

function day_of_week($dd, $mm, $yy) // 0 - monday ... 6 - sunday
{
  $dt = getdate( mktime(0, 0, 0, $mm, $dd, $yy) );

  $dow = $dt['wday'];
  if ($dow == 0) $dow = 7;

  $dow--;

  return $dow;
}

// $cut_len - words with length greater than $cut_len will get cut up to the len
//            if 0 - no cutting
// $cut_all_words - if set 'true', all words in $text get cut. if 'false' - only urls
function highlight_urls($text, $cut_len = 0, $cut_all_words = false, $paramz = array("target" => "_blank"))
{
  $lines_ar = explode("\n", $text);

  for ($i = 0; $i < sizeof($lines_ar); $i++)
  {
    $words_ar = explode(" ", $lines_ar[$i]);

    for ($j = 0; $j < sizeof($words_ar); $j++)
    {
      $words_ar[$j] = trim($words_ar[$j]);

      $prefix = strtolower(substr($words_ar[$j], 0, 8));

      if (
          substr($prefix, 0, 7) == "http://" ||
          substr($prefix, 0, 8) == "https://" ||
          substr($prefix, 0, 6) == "ftp://" ||
          substr($prefix, 0, 4) == "www."
         )
      {
        if (substr($prefix, 0, 4) == "www.") $words_ar[$j] = "http://".$words_ar[$j];

        $s_paramz = "";
        foreach ($paramz as $n => $v)
        {
          $s_paramz .= " ".$n."=\"".$v."\"";
        }

        $inner_text = (strlen($words_ar[$j]) > $cut_len && $cut_len > 0)
          ? substr($words_ar[$j], 0, $cut_len - 3)."..."
          : $words_ar[$j];

        $words_ar[$j] = "<a href=\"".$words_ar[$j]."\"".$s_paramz.">".$inner_text."</a>";
      }
      elseif ($cut_all_words && $cut_len > 0 && strlen($words_ar[$j]) > $cut_len)
      {
        $words_ar[$j] = substr($words_ar[$j], 0, $cut_len);
      }
    }

    $lines_ar[$i] = implode(" ", $words_ar);
  }

  $text = implode("\n", $lines_ar);

  return $text;
}

function divide3dig($s, $divider = ",")
{
  $x = strpos($s, ".");

  if ($x === false)
    $x = strlen($s);

  $s2 = substr($s, $x);
  $s = substr($s, 0, $x);

  $ss = "";
  $start = strlen($s) - 3;

  for ($i = 0; $i < ceil(strlen($s) / 3); $i++)
  {
    $len = 3;

    if ($start < 0)
    {
      $len += $start;
      $start = 0;
    }

    $ss = substr($s, $start, $len).$divider.$ss;

    $start -= 3;
  }

  $ss = substr($ss, 0, strlen($ss) - strlen($divider));

  return $ss.$s2;
}

function is_email_valid($email)
{
  return preg_match("/^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-._]?[0-9a-z])*\.[a-z]{2,4}$/i", $email);
}

function is_back_valid($back)
{
  return !preg_match("/^(https?:\/\/|ftp:\/\/|mailto:)/i", ltrim($back));
}

function send_email($from, $to, $subject, $message, $attachment_ar = false)
{
  if (is_email_valid($from))
    $from_email_brackets = "<$from>";
  else
  {
    preg_match("/^(.+) \<(.+)\>$/", $from, $from_regs);

    if ($from_regs)
      $from_email_brackets = "<".$from_regs[2].">";
    else
      $from_email_brackets = $from;
  }

  $headers  = "From: ".$from."\n";
  $headers .= "Reply-To: ".$from_email_brackets."\n";
  //$headers .= "X-Sender: ".$from_email_brackets."\n";
  $headers .= "X-Mailer: diPHP\n";
  $headers .= "Return-Path: ".$from_email_brackets."\n";

  if ($attachment_ar && count($attachment_ar))
  {
    $mime_boundary = "==Multipart_Boundary_x".md5(mt_rand())."x";

    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-Transfer-Encoding: binary\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$mime_boundary}\"\n";

    $message = "--{$mime_boundary}\n".
      "Content-Type: text/html; charset=windows-1251\n".
      "Content-Transfer-Encoding: 8bit\n".
      "\n$message\n";

    foreach ($attachment_ar as $attachment)
    {
      $attachment["data"] = chunk_split(base64_encode($attachment["data"]));

      $message .= "\n--{$mime_boundary}\n";
      $message .= "Content-Disposition: inline; filename=\"{$attachment["filename"]}\"\n";
      $message .= "Content-Type: {$attachment["content_type"]}; name=\"{$attachment["filename"]}\"\n";
      $message .= "Content-Transfer-Encoding: base64\n";
      $message .= "\n{$attachment["data"]}\n";
    }

    $message .= "\n--{$mime_boundary}--\n";
  }
  else
  {
    $headers .= "Content-Transfer-Encoding: 8bit\n";
    $headers .= "Content-Type: text/html; charset=windows-1251\n";
  }

  return mail($to, $subject, $message, $headers);
}

// ** header encoding added
// each element in attachment_ar should look like this:
// [0] => array(
//          "filename" => "filename.jpg",
//          "content_type" => "image/jpeg",
//          "data" => "[binary_data]"),
function send_email2($from, $to, $subject, $message, $body_html, $attachment_ar = false)
{
  $_utf8 = DIMAILENCODING == "UTF8" ? true : false;
  $_quoted_printable = false;

  if (is_email_valid($from))
  {
    $from_email_brackets = "<$from>";

    $from_name = "";
    $from_email = $from;
  }
  else
  {
    preg_match("/^(.+) \<(.+)\>$/", $from, $from_regs);

    if ($from_regs)
    {
      $from_email_brackets = "<".$from_regs[2].">";

      $from_name = $from_regs[1];
      $from_email = $from_regs[2];
    }
    else
    {
      $from_email_brackets = $from;

      $from_name = "";
      $from_email = $from;
    }
  }

  preg_match("/^(.+) \<(.+)\>$/", $to, $to_regs);

  if (!empty($to_regs))
  {
    $to_name = $to_regs[1];
    $to_email = $to_regs[2];
  }
  else
  {
    $to_name = "";
    $to_email = $to;
  }

  if ($_utf8)
  {
    $from_name = iconv("cp1251", "utf-8", $from_name);
    $to_name = iconv("cp1251", "utf-8", $to_name);
    $subject = iconv("cp1251", "utf-8", $subject);
    $message = iconv("cp1251", "utf-8", $message);
    $body_html = iconv("cp1251", "utf-8", $body_html);

    $from = $from_name ? "=?UTF-8?B?".base64_encode($from_name)."?= <$from_email>" : $from_email;
    $to = $to_name ? "=?UTF-8?B?".base64_encode($to_name)."?= <$to_email>" : $to_email;
    $subject = "=?UTF-8?B?".base64_encode($subject)."?=";
  }
  else
  {
    $from = $from_name ? "=?CP1251?B?".base64_encode($from_name)."?= <$from_email>" : $from_email;
    $to = $to_name ? "=?CP1251?B?".base64_encode($to_name)."?= <$to_email>" : $to_email;
    $subject = "=?CP1251?B?".base64_encode($subject)."?=";
  }

  $headers  = "From: $from\n";
  $headers .= "Reply-To: $from_email_brackets\n";
  $headers .= "X-Sender: $from_email_brackets\n";
  $headers .= "X-Mailer: dimail\n";
  $headers .= "Return-Path: $from_email_brackets\n";

  if ($body_html || ($attachment_ar && count($attachment_ar)))
  {
    $mime_boundary = "==Multipart_Boundary_x".md5(mt_rand())."x";

    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-Transfer-Encoding: binary\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$mime_boundary}\"\n";

    if ($message)
    {
      if ($_quoted_printable)
        $message = quoted_printable_encode($message);

      $message = "\n\n--{$mime_boundary}\n".
        "Content-Type: text/plain; charset=\"".($_utf8 ? "UTF-8" : "windows-1251")."\"\n".
        "Content-Transfer-Encoding: ".($_quoted_printable ? "quoted-printable" : "8bit")."\n".
        "\n$message\n";
    }

    if ($body_html)
    {
      if ($_quoted_printable)
        $body_html = quoted_printable_encode($body_html);

      $message .= "\n\n--{$mime_boundary}\n".
        "Content-Type: text/html; charset=\"".($_utf8 ? "UTF-8" : "windows-1251")."\"\n".
        "Content-Transfer-Encoding: ".($_quoted_printable ? "quoted-printable" : "8bit")."\n".
        "\n$body_html\n";
    }

    //$message .= "\n--{$mime_boundary}--\n";

    if ($attachment_ar)
    foreach ($attachment_ar as $attachment)
    {
      $attachment["data"] = chunk_split(base64_encode($attachment["data"]));

      $cid_line = !empty($attachment["content_id"]) ? "Content-ID: <{$attachment["content_id"]}>\n" : "";

      $message .= "\n--{$mime_boundary}\n";
      $message .= "Content-Disposition: inline; filename=\"{$attachment["filename"]}\"\n";
      $message .= $cid_line;
      $message .= "Content-Type: {$attachment["content_type"]}; name=\"{$attachment["filename"]}\"\n";
      $message .= "Content-Transfer-Encoding: base64\n";
      $message .= "\n{$attachment["data"]}\n";
    }

    $message .= "\n--{$mime_boundary}--\n";
  }
  else
  {
    $headers .= "Content-Type: text/plain; charset=\"".($_utf8 ? "UTF-8" : "windows-1251")."\"\n".
    $headers .= "Content-Transfer-Encoding: ".($_quoted_printable ? "quoted-printable" : "8bit")."\n".

    $message = quoted_printable_encode($message);
  }

  $result = mail($to, $subject, $message, $headers, "-f{$from_email}");

  return $result;
}

function str_check_empty($s, $str_if_empty = "&nbsp;")
{
  if ($s == "") $s = $str_if_empty;

  return $s;
}

function str_cut_end($s, $max_len, $trailer = "...")
{
  if (strlen($s) > $max_len)
    $s = rtrim(substr(ltrim($s), 0, $max_len - strlen($trailer))).$trailer;

  return $s;
}

function smart_str_cut_end($s, $max_len, $trailer = "...", $is_utf8 = false)
{
  $printedLength = 0;
  $position = 0;
  $tags = array();

  $res = "";
  if (strlen($s) > $max_len)
    $max_len -= strlen($trailer);
  else
    $trailer = "";

  // For UTF-8, we need to count multibyte sequences as one character.
  $re = $is_utf8
    ? '{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;|[\x80-\xFF][\x80-\xBF]*}'
    : '{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}';

  while ($printedLength < $max_len && preg_match($re, $s, $match, PREG_OFFSET_CAPTURE, $position))
  {
    list($tag, $tagPosition) = $match[0];

    // Print text leading up to the tag.
    $str = substr($s, $position, $tagPosition - $position);
    if ($printedLength + strlen($str) > $max_len)
    {
      $res .= substr($str, 0, $max_len - $printedLength);
      $printedLength = $max_len;

      break;
    }

    $res .= $str;
    $printedLength += strlen($str);
    if ($printedLength >= $max_len) break;

    if ($tag[0] == '&' || ord($tag) >= 0x80)
    {
      // Pass the entity or UTF-8 multibyte sequence through unchanged.
      $res .= $tag;
      $printedLength++;
    }
    else
    {
      // Handle the tag.
      $tagName = $match[1][0];
      if ($tag[1] == '/')
      {
        // This is a closing tag.

        $openingTag = array_pop($tags);
        assert($openingTag == $tagName); // check that tags are properly nested.

        $res .= $tag;
      }
      else if ($tag[strlen($tag) - 2] == '/')
      {
        // Self-closing tag.
        $res .= $tag;
      }
      else
      {
        // Opening tag.
        $res .= $tag;
        $tags[] = $tagName;
      }
    }

    // Continue after the tag.
    $position = $tagPosition + strlen($tag);
  }

  // Print any remaining text.
  if ($printedLength < $max_len && $position < strlen($s))
      $res .= substr($s, $position, $max_len - $printedLength);

  // Close any open tags.
  while (!empty($tags))
    $res .= sprintf('</%s>', array_pop($tags));

  $res .= $trailer;

  return $res;
}

/*
function smart_str_cut_end($s, $max_len, $trailer = "...")
{
  if (strlen($s) > $max_len)
  {
    $cut_name = substr($s, 0, $max_len - strlen($trailer));
    $len_diff = 0;

    if (preg_match("/&#?[0-9a-z]{3,5};/i", $cut_name))
    {
      preg_match_all("/&#?[0-9a-z]{3,5};/i", $s, $regs);
      $x0 = 0;

      if (isset($regs[0]))
      {
        foreach ($regs[0] as $a)
        {
          $x1 = strpos($s, $a, $x0);

          if ($x1 < $max_len)
          {
            if ($x1 !== false)
              $len_diff += strlen($a) - 1;
          }
          else
            break;

          if (isset($_COOKIE["test"]))
            echo "$a - $x1 - $len_diff\n";

          $x0 = $x1 + strlen($a);
        }
      }
    }

    $s = str_cut_end($s, $max_len + $len_diff - strlen($trailer));
    $s .= $trailer;
  }

  return $s;
}
*/

// idx starts from 0
function array_remove_element($ar, $idx, $len = 1)
{
  $left = array_slice($ar, 0, $idx - 1);
  $right = array_slice($ar, $idx + $len - 1, count($ar));

  $ar = array_merge($left, $right);

  unset($left);
  unset($right);

  return $ar;
}

/*
url functions
*/

function myurlencode($s)
{
  $s = rawurlencode($s);
  $s = str_replace("http%3A%2F%2F", "http://", $s);
  $s = str_replace("%2F", "/", $s);

  return $s;
}

// returns array("f" => array(files...), "d" => array(directories...))
function get_dir_array($sPath)
{
  $handle = opendir($sPath);

  $rezz = array("f" => array(), "d" => array());

  while ($f = readdir($handle))
  {
    if (is_file(add_ending_slash($sPath).$f)) $rezz["f"][] = $f;
    elseif (is_dir(add_ending_slash($sPath).$f) && $f != "." && $f != "..") $rezz["d"][] = $f;
  }

  closedir($handle);

  sort($rezz["f"]);
  sort($rezz["d"]);

  return $rezz;
}

function get_file_ext($fn)
{
  preg_match("/\.([^.]*)$/", $fn, $regs);

  return $regs ? $regs[1] : "";
}

// returns an array:
//  "lines" => array of "script" lines
//  "script" => javascript with <script> tags for sending email
//  "email" => e-mail address escaped, so fuken spammerz couldn't know it
//  "a" => array of <a> tag elements "onclick" and "href"
function publish_email($email, $doggy_replacer = "&#64", $unique_function_name_ending = "")
{
  $lines = array();
  $a["onclick"] = "onclick=\"sendMail".$unique_function_name_ending."(this);\"";
  $a["href"] = "href=\"#\"";
  $_email = "";

  $email = trim($email);

  $i = 0;
  $j = 0;
  $parts = array();

  while ($i < strlen($email))
  {
    if (in_array($email[$i], array("@",".","_","-")) || $i == strlen($email) - 1)
    {
      if ($i < strlen($email) - 1)
      {
        $parts[] = substr($email, $j, $i - $j);
        $parts[] = $email[$i];
      }
      else
      {
        $parts[] = substr($email, $j, $i - $j + 1);
      }

      $j = $i + 1;
    }

    $i++;
  }

  if (isset($parts[0]))
  {
    $lines[] = "<script type=\"text/javascript\">function sendMail".$unique_function_name_ending."(link) {";
    $lines[] = "mailto = \"".$parts[0]."\";";
    $_email .= "mailto = \"".$parts[0]."\";";

    for ($k = 1; $k < count($parts); $k++)
    {
      if ($parts[$k] == "@") $parts[$k] = $doggy_replacer;

      $lines[] = "mailto+=\"".$parts[$k]."\";";
      $_email .= "mailto+=\"".$parts[$k]."\";";
    }

    $_email = "<script type=\"text/javascript\">".$_email."document.write(mailto);</script>";

    $lines[] = "link.href=\"mailto:\"+mailto;";
    $lines[] = "return true;";
    $lines[] = "}</script>";
  }

  return array("lines" => $lines, "script" => join("\n", $lines), "email" => $_email, "a" => $a);
}

function transliterate_rus_to_eng($text)
{
  $trans_table = array(
   "клу" => "clu",
   "кат" => "cat",
   "ком" => "com",
   "цц" => "zz",
   "ый" => "y",
   "а" => "a",
   "б" => "b",
   "в" => "v",
   "г" => "g",
   "д" => "d",
   "е" => "e",
   "ё" => "e",
   "ж" => "zh",
   "з" => "z",
   "и" => "i",
   "й" => "y",
   "к" => "k",
   "л" => "l",
   "м" => "m",
   "н" => "n",
   "о" => "o",
   "п" => "p",
   "р" => "r",
   "с" => "s",
   "т" => "t",
   "у" => "u",
   "ф" => "f",
   "х" => "h",
   "ц" => "ts",
   "ч" => "ch",
   "ш" => "sh",
   "щ" => "sch",
   "ъ" => "",
   "ы" => "y",
   "ь" => "",
   "э" => "e",
   "ю" => "yu",
   "я" => "ya",
   " " => "_",

   "КЛУ" => "clu",
   "КАТ" => "cat",
   "КОМ" => "com",
   "ЦЦ" => "zz",
   "ЫЙ" => "y",
   "А" => "a",
   "Б" => "b",
   "В" => "v",
   "Г" => "g",
   "Д" => "d",
   "Е" => "e",
   "Ё" => "e",
   "Ж" => "zh",
   "З" => "z",
   "И" => "i",
   "Й" => "y",
   "К" => "k",
   "Л" => "l",
   "М" => "m",
   "Н" => "n",
   "О" => "o",
   "П" => "p",
   "Р" => "r",
   "С" => "s",
   "Т" => "t",
   "У" => "u",
   "Ф" => "f",
   "Х" => "h",
   "Ц" => "ts",
   "Ч" => "ch",
   "Ш" => "sh",
   "Щ" => "sch",
   "Ъ" => "",
   "Ы" => "y",
   "Ь" => "",
   "Э" => "e",
   "Ю" => "yu",
   "Я" => "ya",
   " " => "_",
  );

  $text = strtolower($text);
  return str_replace(array_keys($trans_table), array_values($trans_table), $text);
}

function my_nl2br($s)
{
  $a1 = array(
    "\r",
    "\n",
    "\\r",
    "\\n",
  );

  $a2 = array(
    "",
    "<br>",
    "",
    "<br>",
  );

  return str_replace($a1, $a2, $s);
}

function my_nl_out($s)
{
  $a1 = array(
    "\\r",
    "\\n",
  );

  $a2 = array(
    "",
    "\n",
  );

  return str_replace($a1, $a2, $s);
}

function has_str_within($what, $subject)
{
  return strlen(strpos($subject, $what)) ? true : false;
}

// $_SERVER["HTTP_USER_AGENT"] is expected
function get_os_version($http_user_agent)
{
  $agt = strtolower($http_user_agent);

  $is_win   = (has_str_within("win", $agt) || has_str_within("16bit", $agt));
  $is_win95 = (has_str_within("win95", $agt) || has_str_within("windows 95", $agt));

  $is_win16 = (
               has_str_within("win16", $agt) ||
               has_str_within("16bit", $agt) ||
               has_str_within("windows 3.1", $agt) ||
               has_str_within("windows 16-bit", $agt)
              );

  $is_win31 = (
               has_str_within("windows 3.1", $agt) ||
               has_str_within("win16", $agt) ||
               has_str_within("windows 16-bit", $agt)
              );

  $is_winme = has_str_within("win 9x 4.90", $agt);
  $is_win2k = has_str_within("windows nt 5.0", $agt);
  $is_winxp = has_str_within("windows nt 5.1", $agt);

  $is_win98 = (has_str_within("win98", $agt) || has_str_within("windows 98", $agt));
  $is_winnt = (has_str_within("winnt", $agt) || has_str_within("windows nt", $agt));
  $is_win32 = ($is_win95 || $is_winnt || $is_win98 || has_str_within("win32", $agt) || has_str_within("32bit", $agt));

  $is_os2   = (has_str_within("os/2", $agt) || has_str_within("ibm-webexplorer", $agt));

  $is_mac   = has_str_within("mac", $agt);

  $is_mac68k = ($is_mac && has_str_within("68k", $agt) || has_str_within("68000", $agt));
  $is_macppc = ($is_mac && has_str_within("ppc", $agt) || has_str_within("powerpc", $agt));

  $is_sun   = has_str_within("sunos", $agt);
  $is_sun4  = has_str_within("sunos 4", $agt);
  $is_sun5  = has_str_within("sunos 5", $agt);
  $is_suni86= ($is_sun && has_str_within("i86", $agt));
  $is_irix  = has_str_within("irix", $agt);
  $is_irix5 = has_str_within("irix 5", $agt);
  $is_irix6 = (has_str_within("irix 6", $agt) || has_str_within("irix6", $agt));
  $is_hpux  = has_str_within("hp-ux", $agt);
  $is_hpux9 = ($is_hpux && has_str_within("09.", $agt));
  $is_hpux10= ($is_hpux && has_str_within("10.", $agt));
  $is_aix   = has_str_within("aix", $agt);
  $is_aix1  = has_str_within("aix 1", $agt);
  $is_aix2  = has_str_within("aix 2", $agt);
  $is_aix3  = has_str_within("aix 3", $agt);
  $is_aix4  = has_str_within("aix 4", $agt);
  $is_linux = has_str_within("inux", $agt);
  $is_sco   = (has_str_within("sco", $agt) || has_str_within("unix_sv", $agt));
  $is_unixware = has_str_within("unix_system_v", $agt);
  $is_mpras    = has_str_within("ncr", $agt);
  $is_reliant  = has_str_within("reliantunix", $agt);
  $is_dec   = (
               has_str_within("dec", $agt) ||
               has_str_within("osf1", $agt) ||
               has_str_within("dec_alpha", $agt) ||
               has_str_within("alphaserver", $agt) ||
               has_str_within("ultrix", $agt) ||
               has_str_within("alphastation", $agt)
              );

  $is_sinix = has_str_within("sinix", $agt);
  $is_freebsd = has_str_within("freebsd", $agt);
  $is_bsd = has_str_within("bsd", $agt);
  $is_unix  = (
               has_str_within("x11", $agt) ||
               $is_sun || $is_irix || $is_hpux ||
               $is_sco || $is_unixware || $is_mpras || $is_reliant ||
               $is_dec || $is_sinix || $is_aix || $is_linux || $is_bsd || $is_freebsd
              );

  preg_match("/\((.*)\)/", $http_user_agent, $regs);
  $regs = explode(";", $regs[1]);
  $reserved_os = trim($regs[2]);

  $rezz = $reserved_os;
  if ($is_win) $rezz = "Windows ";

  if ($is_win95) $rezz .= "95";
  elseif ($is_win31) $rezz .= "3.1";
  elseif ($is_winme) $rezz .= "ME";
  elseif ($is_win2k) $rezz .= "2000";
  elseif ($is_winxp) $rezz .= "XP";
  elseif ($is_winnt) $rezz .= "NT";
  elseif ($is_win98) $rezz .= "98";
  elseif ($is_os2) $rezz = "OS/2";
  elseif ($is_mac) $rezz = "MacOS";
  elseif ($is_sun5) $rezz = "SunOS 5";
  elseif ($is_sun4) $rezz = "SunOS 4";
  elseif ($is_suni86) $rezz = "SunOS i86";
  elseif ($is_sun) $rezz = "SunOS";
  elseif ($is_irix6) $rezz = "Irix 6";
  elseif ($is_irix5) $rezz = "Irix 5";
  elseif ($is_irix) $rezz = "Irix";
  elseif ($is_hpux10) $rezz = "HPUX 10";
  elseif ($is_hpux9) $rezz = "HPUX 9";
  elseif ($is_hpux) $rezz = "HPUX";
  elseif ($is_aix4) $rezz = "AIX 4";
  elseif ($is_aix3) $rezz = "AIX 3";
  elseif ($is_aix2) $rezz = "AIX 2";
  elseif ($is_aix1) $rezz = "AIX 1";
  elseif ($is_aix) $rezz = "AIX";
  elseif ($is_linux) $rezz = "Linux";
  elseif ($is_sco) $rezz = "SCO";
  elseif ($is_unixware) $rezz = "UnixWare";
  elseif ($is_mpras) $rezz = "MPRAS Unix";
  elseif ($is_reliant) $rezz = "Reliant Unix";
  elseif ($is_dec) $rezz = "DEC Unix";
  elseif ($is_sinix) $rezz = "Sinix";
  elseif ($is_freebsd) $rezz = "FreeBSD";
  elseif ($is_bsd) $rezz = "BSD";
  elseif ($is_unix) $rezz = "Unix";

  return $rezz;
}

function get_user_ip()
{
  if (!empty($_SERVER["HTTP_CLIENT_IP"]))
  {
    $ip = $_SERVER["HTTP_CLIENT_IP"];
  }
  elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
  {
    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
  }
  else
  {
    $ip = $_SERVER["REMOTE_ADDR"];
  }

  return $ip;
}

//
function dierror($text, $status = DIE_FATAL)
{
  $types = array(
    DIE_NOTICE => "Notice: ",
    DIE_WARNING => "Warning: ",
    DIE_FATAL => "Fatal error: ",
  );

  // file stuff
  $ip = get_user_ip();
  $host = gethostbyaddr($ip);
  $r = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";

  $f = fopen("{$_SERVER["DOCUMENT_ROOT"]}/log/".date("Y_m_d")."-errors.txt", "a");
  fputs($f, date("d.m.Y H:i:s").", $ip ($host), uri: {$_SERVER["REQUEST_URI"]}, ref: $r, agent: {$_SERVER["HTTP_USER_AGENT"]}\n$text\n\n");
  fclose($f);
  //

  if ($status == DIE_FATAL)
    die("<br /><br /><b>{$types[$status]}</b> $text");
  else
    echo("<br /><br /><b>{$types[$status]}</b> $text");
}

if (!function_exists("htmlspecialchars_decode"))
{
  function htmlspecialchars_decode($text)
  {
    return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
  }
}

function imagefliphorizontal($image)
{
  $w = imagesx($image);
  $h = imagesy($image);

  $flipped = imagecreatetruecolor($w, $h);

  for ($x = 0; $x < $w; $x++)
  {
    imagecopy($flipped, $image, $x, 0, $w - $x - 1, 0, 1, $h);
  }

  return $flipped;
}

function imageflipvertical($image)
{
  $w = imagesx($image);
  $h = imagesy($image);

  $flipped = imagecreatetruecolor($w, $h);

  for ($y = 0; $y < $h; $y++)
  {
    imagecopy($flipped, $image, 0, $y, 0, $h - $y - 1, $w, 1);
  }

  return $flipped;
}

function get_unique_id()
{
  srand((double)microtime() * 1000000);
  return md5(rand(0, 9999999));
}

function rgb_color($color)
{
  if (is_string($color))
  {
    if (substr($color, 0, 1) == "#") $color = substr($color, 1);

    return array(
      hexdec(substr($color, 0, 2)),
      hexdec(substr($color, 2, 2)),
      hexdec(substr($color, 4, 2))
    );
  }
  else
  {
    return $color;
  }
}

function rgb_allocate($image, $color)
{
  list($r, $g, $b) = rgb_color($color);

  $index = imagecolorexact($image, $r, $g, $b);

  return ($index == -1 ? imagecolorallocate($image, $r, $g, $b) : $index);
}


function digit_case($x, $s1, $s2, $s3 = false, $only_string = false)
{
  if ($s3 === false) $s3 = $s2;

  $x0 = $x;
  $x = $x % 100;

  if ($x % 10 == 1 && $x != 11)
    return $only_string ? $s1 : "$x0 $s1";
  elseif ($x % 10 >= 2 && $x % 10 <= 4 && $x != 12 && $x != 13 && $x != 14)
    return $only_string ? $s2 : "$x0 $s2";
  else
    return $only_string ? $s3 : "$x0 $s3";
}

function pad_left($s, $len, $char)
{
  while (strlen($s) < $len)
    $s = $char.$s;

  return $s;
}

function escape_tpl_brackets($s)
{
  return str_replace(array("{","}"),array("&#123;","&#125;"),$s);
}

function fix_anchors($s)
{
  return preg_replace('/\<a([^\>]+)href[\x20\t]*\=[\x20\t]*[\'\"]?\#([^\'\"]+)[\'\"]?([^\>]*)\>/i', '<a\\1href="'.$_SERVER["REQUEST_URI"].'#\\2"\\3>', $s);
}

function imagecreate_func_name($img_type)
{
  global $image_type_to_ext_ar;

  $func_suffix = $img_type >= 1 && $img_type <= 3 ? $image_type_to_ext_ar[$img_type] : "";
  return $func_suffix ? "imagecreatefrom{$func_suffix}" : "";
}

function imagestore_func_name($img_type)
{
  global $image_type_to_ext_ar;

  $func_suffix = $img_type >= 1 && $img_type <= 3 ? $image_type_to_ext_ar[$img_type] : "";
  return $func_suffix ? "image{$func_suffix}" : "";
}

function day_of_week_eng($dd, $mm, $yy) // 0 - sunday ... 6 - saturday
{
  $dt = getdate(mktime(0, 0, 0, $mm, $dd, $yy));

  return $dt['wday'];
}

function str_to_upper($str){
  return strtr($str,
  "abcdefghijklmnopqrstuvwxyz".
  "\xE0\xE1\xE2\xE3\xE4\xE5".
  "\xb8\xe6\xe7\xe8\xe9\xea".
  "\xeb\xeC\xeD\xeE\xeF\xf0".
  "\xf1\xf2\xf3\xf4\xf5\xf6".
  "\xf7\xf8\xf9\xfA\xfB\xfC".
  "\xfD\xfE\xfF",
  "ABCDEFGHIJKLMNOPQRSTUVWXYZ".
  "\xC0\xC1\xC2\xC3\xC4\xC5".
  "\xA8\xC6\xC7\xC8\xC9\xCA".
  "\xCB\xCC\xCD\xCE\xCF\xD0".
  "\xD1\xD2\xD3\xD4\xD5\xD6".
  "\xD7\xD8\xD9\xDA\xDB\xDC".
  "\xDD\xDE\xDF"
  );
}

function str_to_lower($str){
  return strtr($str,
  "ABCDEFGHIJKLMNOPQRSTUVWXYZ".
  "\xC0\xC1\xC2\xC3\xC4\xC5".
  "\xA8\xC6\xC7\xC8\xC9\xCA".
  "\xCB\xCC\xCD\xCE\xCF\xD0".
  "\xD1\xD2\xD3\xD4\xD5\xD6".
  "\xD7\xD8\xD9\xDA\xDB\xDC".
  "\xDD\xDE\xDF",
  "abcdefghijklmnopqrstuvwxyz".
  "\xE0\xE1\xE2\xE3\xE4\xE5".
  "\xb8\xe6\xe7\xe8\xe9\xea".
  "\xeb\xeC\xeD\xeE\xeF\xf0".
  "\xf1\xf2\xf3\xf4\xf5\xf6".
  "\xf7\xf8\xf9\xfA\xfB\xfC".
  "\xfD\xfE\xfF"
  );
}

function di_ucwords($s)
{
  $break = 1;
  $s2 = "";

  for ($i = 0; $i < strlen($s); $i++)
  {
    $ch = $s{$i};

    if ((ord($ch) > 64 && ord($ch) < 123) || (ord($ch) > 48 && ord($ch) < 58) || (ord($ch) >= 192 && ord($ch) <= 255) || ord($ch) == 184 || ord($ch) == 168)
    {
      if ($break) $s2 .= strtoupper($ch);
      else $s2 .= strtolower($ch);

      $break = 0;
    }
    else
    {
      $s2 .= $ch;
      $break = 1;
    }
  }

  return $s2;
}

function json_encode2($a = false)
{
  if (is_null($a)) return 'null';
  if ($a === false) return 'false';
  if ($a === true) return 'true';
  if (is_scalar($a))
  {
    if (is_float($a))
    {
      // Always use "." for floats.
      return floatval(str_replace(",", ".", strval($a)));
    }

    if (is_string($a))
    {
      static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
      return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
    }
    else
      return $a;
  }
  $isList = true;
  for ($i = 0, reset($a); $i < count($a); $i++, next($a))
  {
    if (key($a) !== $i)
    {
      $isList = false;
      break;
    }
  }
  $result = array();
  if ($isList)
  {
    foreach ($a as $v) $result[] = json_encode2($v);
    return '[' . join(',', $result) . ']';
  }
  else
  {
    foreach ($a as $k => $v) $result[] = json_encode2($k).':'.json_encode2($v);
    return '{' . join(',', $result) . '}';
  }
}

if (!function_exists('json_encode'))
{
  function json_encode($a = false)
  {
    return json_encode2($a);
  }
}

function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0)
{
  $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#", '', $json);

  if (version_compare(phpversion(), '5.4.0', '>='))
    $json = json_decode($json, $assoc, $depth, $options);
  elseif (version_compare(phpversion(), '5.3.0', '>='))
    $json = json_decode($json, $assoc, $depth);
  else
    $json = json_decode($json, $assoc);

  return $json;
}

function get_1000_path($id)
{
  $id = strval($id);
  $id = str_repeat("0", 9 - strlen($id)).$id;

  $path = substr($id, 0, 3)."/".substr($id, 3, 3)."/".substr($id, 6, 3)."/";

  return $path;
}

function word_wrap($s, $len, $divider = " ")
{
  $r_char = ">";

  $lines = array("");
  $lines2 = array("");

  $ar0 = preg_split("/[ \r\n]/", $s);
  $ar1 = array();
  $ar2 = array();
  $ar3 = array();

  for ($i = 0; $i < count($ar0); $i++)
  {
    if ($ar0[$i] == "") continue;

    $ar1[] = $ar0[$i];
    $ar2[] = preg_replace("/\&(\#[0-9]{2,5}|[a-zA-Z]{2,8})\;/", $r_char, $ar0[$i]);

    preg_match_all("/\&(\#[0-9]{2,5}|[a-zA-Z]{2,8})\;/", $ar0[$i], $r);
    $ar3[] = $r[0];
  }

  for ($i = 0; $i < count($ar1); $i++)
  {
    if (strlen($lines2[count($lines2) - 1]) + 1 + strlen($ar2[$i]) <= $len)
    {
      $lines[count($lines) - 1] .= " ".$ar1[$i];
      $lines2[count($lines2) - 1] .= " ".$ar2[$i];
    }
    else
    {
      if (strlen($ar2[$i]) <= $len)
      {
        $lines[count($lines)] = $ar1[$i];
        $lines2[count($lines2)] = $ar2[$i];
      }
      else
      {
        $cc = 0;

        while (strlen($ar2[$i]) > 0)
        {
          $tmp1 = substr($ar2[$i], 0, $len);
          $tmp2 = $tmp1;

          for ($j = 0; $j < strlen($tmp1); $j++)
          {
            if (substr($tmp1, $j, 1) == $r_char)
            {
              $tmp1 = substr($tmp1, 0, $j).$ar3[$i][$cc].substr($tmp1, $j + 1);
              $j += strlen($ar3[$i][$cc]) - 1;
              $cc++;
            }
          }

          $ar1[$i] = substr($ar1[$i], strlen($tmp1));
          $ar2[$i] = substr($ar2[$i], strlen($tmp2));

          $lines[count($lines)] = $tmp1;
          $lines2[count($lines2)] = $tmp2;
        }
      }
    }
  }

  return join($divider, $lines);
}

function utime()
{
  $time = explode(" ", microtime());
  $usec = (double)$time[0];
  $sec = (double)$time[1];
  return $sec + $usec;
}

function replace_file_ext($fn, $new_ext)
{
  if ($new_ext && $new_ext{0} != ".")
    $new_ext = ".".$new_ext;

  $x = strrpos($fn, ".");

  if ($x !== false)
    $fn = substr($fn, 0, $x);

  return $fn.$new_ext;
}

function get_yday($dt)
{
  return $dt ? floor(($dt - mktime(0, 0, 0, 1, 1, date("Y", $dt))) / 86400) : 0;
}

function get_big_yday($dt)
{
  $yd = get_yday($dt);
  $yd = str_repeat("0", 3 - strlen($yd)).$yd;
  return $dt ? date("Y", $dt).$yd : 0;
}

function ip2bin($ip = false)
{
  if ($ip === false)
    $ip = get_user_ip();

  return ip2long($ip);
}

function bin2ip($bin)
{
  return long2ip($bin);
}

function time_passed_by($timestamp, $now = false)
{
  if ($now === false) $now = time();
  $t_diff = $now - $timestamp;

  if ((int)$timestamp != $timestamp)
    $timestamp = strtotime($timestamp);

  $s = "";

  if (!$t_diff)
  {
    return "только что";
  }
  elseif (strtotime("+1 month", $timestamp) < $now) // more than a month ago
  {
    return date("d.m.Y H:i", $timestamp);
  }
  else
  {
    if ($t_diff < 60) // secs
    {
      $s = digit_case(abs($t_diff), "секунду", "секунды", "секунд");
    }
    else
    {
      $t_diff = round($t_diff / 60);

      if ($t_diff < 60) //mins
      {
        $s = digit_case(abs($t_diff), "минуту", "минуты", "минут");
      }
      else
      {
        $t_diff = round($t_diff / 60);

        if ($t_diff < 24) // hours
        {
          $s = digit_case($t_diff, "час", "часа", "часов");
        }
        else
        {
          $t_diff = round($t_diff / 24);

          if ($t_diff < 30) // days
          {
            $s = digit_case($t_diff, "день", "дня", "дней");
          }
          else
          {
            $t_diff = round($t_diff / 30);

            if ($t_diff < 12) // months
            {
              $s = digit_case($t_diff, "месяц", "месяца", "месяцев");
            }
            else
            {
              $t_diff = round($t_diff / 12);

              $s = digit_case($t_diff, "год", "года", "лет");
            }
          }
        }
      }
    }

    return $s." назад";
  }
}

function size_in_bytes($size, $mb = "Mb", $kb = "kb", $b = "bytes")
{
  if ($size > 1048576) return (round($size * 10 / 1048576) / 10).$mb;
  elseif ($size > 1024) return (round($size * 10 / 1024) / 10).$kb;
  else return $size.$b;
}

function str_filesize($size)
{
  return size_in_bytes($size, "Мб", $kb = "кб", $b = " байт");
}

function get_age($d, $m, $y)
{
  return $y ? date("Y") - $y - (date("md") < lead0($m).lead0($d) ? 1 : 0) : 0;
}

function clean_filename($fn)
{
  $fn = transliterate_rus_to_eng($fn);
  $fn = preg_replace("/[^a-zA-Z0-9-\._\(\)\[\]]/", "", $fn);

  return $fn ? $fn : "New_folder";
}

function get_uri_glue($uri)
{
  return strpos($uri, "?") === false ? "?" : "&";
}

/* classes stuff */

function get_path_to_classes($path_prefix)
{
  $path = $_SERVER["DOCUMENT_ROOT"]."/_cfg/classes/";

  if ($path_prefix) $path .= add_ending_slash($path_prefix);

  return $path;
}

function require_class($class_name, $path_prefix = "")
{
  if (class_exists($class_name)) return false;

  require_once get_path_to_classes($path_prefix)."_class_".strtolower($class_name).".php";

  $ar = get_defined_vars();

  foreach ($ar as $k => $v)
  {
    if (in_array($k, array("class_name", "path_prefix"))) continue;

    $GLOBALS[$k] = $v;
  }
}

function require_interface($interface_name, $path_prefix = "")
{
  require_once get_path_to_classes($path_prefix)."_interface_".strtolower($interface_name).".php";
}

function check_uploaded_file($full_fn, $orig_fn = "", $types_ar = array())
{
  $typed_allowed_ext_ar = array(
    "pic" => array("jpeg","jpg","png","gif","swf"),
    "audio" => array("mp3","ogg","ac3"),
    "video" => array("avi","flv","mp4"),
    "arc" => array("rar","zip","gz"),
    "office" => array("doc","docx","xls","xlsx","pdf"),
  );

  if ($types_ar && !is_array($types_ar))
    $types_ar = array($types_ar);

  $ar = array();

  if ($types_ar)
  {
    $ar = array();

    foreach ($types_ar as $t)
    {
      if (isset($typed_allowed_ext_ar[$t]))
        $ar = array_merge($ar, $typed_allowed_ext_ar[$t]);
    }
  }
  else
  {
    foreach ($typed_allowed_ext_ar as $k => $v)
    {
      $ar = array_merge($ar, $v);
    }
  }

  $ext = strtolower(get_file_ext($orig_fn ? $orig_fn : $full_fn));

  return in_array($ext, $ar);
}

function shuffle_assoc(&$array)
{
  $keys = array_keys($array);

  shuffle($keys);

  foreach($keys as $key)
  {
    $new[$key] = $array[$key];
  }

  $array = $new;

  return true;
}

function escape_bad_html($s, $allowed = "p|br|b|i|u|a|img|object|embed|param|iframe")
{
  $s = preg_replace("/<((?!\/?($allowed)\b)[^>]*)>/xis", '&lt;\1&gt;', $s);

  preg_match_all("/<iframe[^>]*>/", $s, $regs);

  foreach ($regs[0] as $tag)
  {
    if (strpos($tag, " src=\"http://www.youtube.com/") === false)
      $s = str_replace($tag, str_out($tag), $s);
  }

  return $s;
}

function wysiwyg_empty($s)
{
  $s = preg_replace("/<\/?(p|br)[^>]*>/xis", "", $s);
  $s = str_replace("&nbsp;", "", $s);
  $s = trim($s);

  return !$s;
}

function print_json($ar)
{
  global $html_encodings_ar;

  //text/plain
  //header("Content-type: application/json; charset={$html_encodings_ar[DIENCODING]}");
  header("Content-type: text/plain; charset={$html_encodings_ar[DIENCODING]}");
  header("Expires: Mon, 11 Jul 1999 00:00:00 GMT");
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");

  echo json_encode2($ar);
}

function dierror2($text, $module = "")
{
  $ip = get_user_ip();
  $host = gethostbyaddr($ip);
  $r = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";

  if ($module)
    $module = "[$module]";

  $f = fopen("{$_SERVER["DOCUMENT_ROOT"]}/log/".date("Y_m_d")."-errors.txt", "a");
  fputs($f, date("d.m.Y H:i:s")." $module $ip ($host), uri: {$_SERVER["REQUEST_URI"]}, agent: {$_SERVER["HTTP_USER_AGENT"]}\n$text\n\n");
  fclose($f);

  die("$text");
}

function simple_debug($message, $module = "")
{
  $fn = "{$_SERVER["DOCUMENT_ROOT"]}/log/debug_".date("Y_m_d").".txt";

  $f = fopen($fn, "a");
  fputs($f, date("[d.m.Y H:i:s]")." [{$module}] $message\n");
  fclose($f);

  @chmod($fn, 0777);
}

function var_debug($var, $module = "")
{
  $fn = "{$_SERVER["DOCUMENT_ROOT"]}/log/debug_".date("Y_m_d").".txt";

  $f = fopen($fn, "a");
  fputs($f, date("[d.m.Y H:i:s]")." [{$module}] ".var_export($var, true)."\n");
  fclose($f);

  @chmod($fn, 0777);
}

function cron_debug($script)
{
  $fn = "{$_SERVER["DOCUMENT_ROOT"]}/log/cron_".date("Y_m_d").".txt";

  $f = fopen($fn, "a");
  fputs($f, date("[d.m.Y H:i:s]")." {$script}\n");
  fclose($f);

  @chmod($fn, 0777);
}

function extend()
{
  $args = func_get_args();
  $extended = array();

  if (is_array($args) && count($args))
  {
    foreach ($args as $array)
    {
      if (is_array($array) || is_object($array))
      {
        $extended = array_merge($extended, (array)$array);
      }
    }
  }

  return $extended;
}

function utf($s)
{
  return iconv("cp1251", "utf-8", $s);
}

function _utf($s)
{
  return iconv("utf-8", "cp1251", $s);
}
?>