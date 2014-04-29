<?
/*
    // dimaninc

    // 2014/02/20
        * ::insert() improved: multi-insert added

    // 2014/02/11
        * ::rs_go() added

    // 2013/10/31
        * some precache improvements

    // 2013/06/22
        * reorganized
        * dimysqli class added
        * precache added

    // 2012/11/07
        * ::reset() added

    // 2012/04/06
        * ::insert() updated: *fields (w/o '') added

    // 2010/05/31
        * ::delete() updated: direct int ID and array IDs support added

    // 2010/05/18
        * ::update() and ::ar() updated: direct int ID and array IDs support added

    // 2009/11/02
        * init method params order changed
        * silent mode added

    // 2009/05/05
        * some improvements

    // 2008/12/05
        * random methods added
        * debug added

    // 2008/10/07
        * ::ar() methods added

    // 2008/06/05
        * ::drop(), ::delete() methods added

    // 2008/04/01
        * birthday
*/

function is_rs($rs)
{
  return is_resource($rs) || (is_object($rs) && method_exists($rs, "fetch_object"));
}

abstract class diDB
{
  // basic db info
  public $host;
  public $dbname;
  public $username;
  public $password;
  //

  public $link;
  public $log;
  public $execution_time = 0;
  public $execution_time_log = array();

  protected $fp;
  protected $tables_ar;
  protected $debug = false;
  protected $silent = false;

  public $affected_rows = 0;
  public $cached_db_data = array();

  function __construct($host, $username, $password, $dbname)
  {
    $this->host = $host;
    $this->dbname = $dbname;
    $this->username = $username;
    $this->password = $password;

    $this->log = array();

    if ($this->debug)
    {
      create_folders_chain($_SERVER["DOCUMENT_ROOT"]."/", "log/db", 0777);
      $this->fp = fopen("{$_SERVER["DOCUMENT_ROOT"]}/log/db/db-".date("Y-m-d-H-i-s-").get_unique_id().".csv", "a");

      fputs($this->fp, "\"uri:\";\"{$_SERVER["REQUEST_URI"]}\";\n");
    }

    if (!empty($GLOBALS["engine"]["tables_ar"])) $this->set_tables_ar($GLOBALS["engine"]["tables_ar"]);

    if (!$this->connect())
      $this->_fatal("unable to connect to database");
  }

  function dierror($message = "")
  {
    if ($this->silent)
      exit(0);

    if (count($this->log))
    {
      $this->_fatal($message);
    }
  }

  function _log($message, $add_native_error_message = true)
  {
    $this->log[] = $message;
    if ($add_native_error_message)
      $this->log[] = $this->error();

    return false;
  }

  function _fatal($message)
  {
    dierror(join("\n", $this->log), DIE_WARNING);
    dierror($message, $this->silent ? DIE_WARNING : DIE_FATAL);

    return false;
  }

  function time_log($method, $duration, $query = "", $message = "")
  {
    if (!$this->debug)
      return false;

    $duration = sprintf("%.10f", $duration);

    //$this->log[] = "$message: $duration sec";

    $er = $query ? $this->__fetch($this->__q("EXPLAIN $query")) : false;

    $s = "\"$method\";\"$query\";\"$duration\";\"$message\"";

    if ($er) foreach ($er as $k => $v) $s .= "$k = $v;";

    fputs($this->fp, "$s\r\n");
    fflush($this->fp);

    return false;
  }

  function set_tables_ar($ar)
  {
    $this->tables_ar = $ar;
  }

  function get_table_name($table)
  {
    return $this->tables_ar && isset($this->tables_ar[$table]) ? $this->tables_ar[$table] : $table;
  }

  function precache_rs($table, $query_or_ids_ar = "", $fields = "*")
  {
    if (empty($this->cached_db_data[$table]))
      $this->cached_db_data[$table] = array();

    $rs = $this->rs($table, $query_or_ids_ar, $fields);
    while ($r = $this->fetch($rs))
    {
      $this->cached_db_data[$table][$r->id] = $r;
    }

    $this->reset($rs);

    return $rs;
  }

  function precache_r($table, $id, $fields = "*", $force = true)
  {
    return $this->get_precached_r($table, $id, $fields, $force);
  }

  function get_precached_r($table, $id, $fields = "*", $force = true)
  {
    if (empty($this->cached_db_data[$table]))
      $this->cached_db_data[$table] = array();

    if (empty($this->cached_db_data[$table][$id]) && $id)
    {
      $r = $force ? $this->r($table, $id) : false;

      if ($r && !empty($r->id)) $id = $r->id;
      $this->cached_db_data[$table][$id] = $r;
    }

    return $id ? $this->cached_db_data[$table][$id] : false;
  }

  function clear_precached($table = false)
  {
    $this->flush_precached($table);
  }

  function flush_precached($table = false)
  {
    if ($table)
      $this->cached_db_data[$table] = array();
    else
      $this->cached_db_data = array();
  }

  function escape_string($s)
  {
    return mysql_real_escape_string($s);
  }

  static function in($ar = array(), $digits_only = false, $positive = true)
  {
    if (is_array($ar))
    {
      if (count($ar) == 1)
      {
        $c = $positive ? "" : "!";
        return "$c='{$ar[0]}'";
      }
      else
      {
        $c = $positive ? "" : " not";
        return $digits_only
          ? "$c in (".join(",", $ar).")"
          : "$c in ('".join("','", $ar)."')";
      }
    }
    else
    {
      $c = $positive ? "" : "!";
      return "$c='$ar'";
    }
  }

  static function not_in($ar = array(), $digits_only = false)
  {
    if (is_array($ar))
    {
      if (count($ar) == 1)
        return "!='{$ar[0]}'";
      else
        return $digits_only
          ? " not in (".join(",", $ar).")"
          : " not in ('".join("','", $ar)."')";
    }
    else
    {
      return "!='$ar'";
    }
  }

  /* main methods */

  function connect()
  {
    $time1 = utime();

    $result = $this->__connect();

    $time2 = utime();
    $this->execution_time += $time2 - $time1;

    return $result;
  }

  function close()
  {
    if ($this->debug)
    {
      $this->time_log("total", $this->execution_time);
      fclose($this->fp);
    }

    return $this->__close();
  }

  function error()
  {
    return $this->__error();
  }

  function q($q)
  {
    $time1 = utime();

    $result = $this->__q($q);

    $time2 = utime();
    $this->execution_time += $time2 - $time1;

    $err = $this->error();

    if (!$result && $err)
    {
      $this->_log("unable to exec query \"$q\"", false);
      $this->_log($err, false);
    }

    $this->time_log("q", $time2 - $time1, $q);

    return $result;
  }

  function rq($q)
  {
    $time1 = utime();

    $result = $this->__rq($q);

    $time2 = utime();
    $this->execution_time += $time2 - $time1;

    if (!$result)
      $this->_log("unable to exec query \"$q\"");

    $this->time_log("rq", $time2 - $time1, $q);

    return $result;
  }

  function rs($table, $q_ending = "", $q_fields = "*")
  {
    $time1 = utime();

    // fast construction to get record by id
    if (is_numeric($q_ending))
      $q_ending = "WHERE id='$q_ending' LIMIT 1";
    elseif (is_array($q_ending))
      $q_ending = "WHERE id".$this->in($q_ending);
    //

    $t = $this->get_table_name($table);
    $q = "SELECT $q_fields FROM $t $q_ending";

    $rs = $this->__q($q);

    $time2 = utime();
    $this->execution_time += $time2 - $time1;

    $this->time_log("rs", $time2 - $time1, $q);

    if (!$rs)
      return $this->_log("unable to exec query \"$q\"");

    return $rs;
  }

  function r($table, $q_ending = "", $q_fields = "*")
  {
    // alias to ::fetch()
    if ((is_rs($table) || $table === false) && $q_ending === "")
      return $this->fetch($table);
    //

    // fast construction to get record by id
    if (is_numeric($q_ending))
      $q_ending = "WHERE id='$q_ending'";
    //

    $t = $this->get_table_name($table);
    $q = "SELECT $q_fields FROM $t $q_ending LIMIT 1";

    $time1 = utime();

    $rs = $this->__q($q);
    $r = $rs ? $this->__fetch($rs) : false;

    $time2 = utime();
    $this->execution_time += $time2 - $time1;

    $this->time_log("r", $time2 - $time1, $q);

    if (!$r)
    {
      $err = $this->error();

      if ($err)
      {
        $this->_log("unable to exec query \"$q\"", false);
        $this->_log($err, false);
      }

      return false;
    }

    return $r;
  }

  function random_rs($table, $limit, $q_ending = "", $q_fields = "*")
  {
    $time1 = utime();

    $t = $this->get_table_name($table);

    $r = $this->r($t, $q_ending, "COUNT(*) AS cc");
    $count = $r ? $r->cc : 0;

    if ($count <= $limit)
      return $this->rs($table, $q_ending, $q_fields);

    srand((double)microtime() * 1000000);
    $start = rand(0, $count - $limit);

    $q = "SELECT $q_fields FROM $t $q_ending LIMIT $start,$limit";

    $rs = $this->__q($q);

    $time2 = utime();
    $this->execution_time += $time2 - $time1;

    $this->time_log("random_rs", $time2 - $time1, $q);

    if (!$rs)
      return $this->_log("unable to exec query $q");

    return $rs;
  }

  function random_r($table, $q_ending = "", $q_fields = "*")
  {
    $rs = $this->random_rs($table, 1, $q_ending, $q_fields);
    return $rs ? $this->__fetch($rs) : false;
  }

  function ar($table, $q_ending = "", $q_fields = "*")
  {
    // alias to ::fetch_array()
    if ((is_rs($table) || $table === false) && $q_ending === "")
      return $this->fetch_array($table);
    //

    $time1 = utime();

    // fast construction to get record by id
    if (is_numeric($q_ending))
      $q_ending = "WHERE id='$q_ending'";
    //

    $t = $this->get_table_name($table);
    $q = "SELECT $q_fields FROM $t $q_ending LIMIT 1";

    $rs = $this->__q($q);
    $r = $rs ? $this->fetch_array($rs) : false;

    $time2 = utime();
    $this->execution_time += $time2 - $time1;

    $this->time_log("ar", $time2 - $time1, $q);

    if (!$r)
    {
      $err = $this->error();

      if ($err)
      {
        $this->_log("unable to exec query \"$q\"", false);
        $this->_log($err, false);
      }

      return false;
    }

    return $r;
  }

  function insert($table, $fields_values = array())
  {
    $t = $this->get_table_name($table);

    if (!is_array(current($fields_values))) // preparing for multi-insert
    {
      $fields_values = array($fields_values);
    }

    $q2_ar = array();

    foreach ($fields_values as $ar)
    {
      foreach ($ar as $f => $v)
      {
        if ($f{0} == "*")
        {
          unset($ar[$f]);

          $f = substr($f, 1);
          $ar[$f] = $v;
        }
        else
        {
          $ar[$f] = "'$v'";
        }
      }

      $q1 = "(".join(",", array_keys($ar)).")";
      $q2_ar[] = "(".join(",", array_values($ar)).")";
    }

    $time1 = utime();

    $this->__q("LOCK TABLES $t WRITE");
    if (!$this->__rq("INSERT INTO {$t}{$q1} VALUES".join(",", $q2_ar).";"))
    {
      $this->_log("unable to insert into table $t");

      $this->__q("UNLOCK TABLES");

      return false;
    }
    $id = $this->__insert_id();
    $this->__q("UNLOCK TABLES");

    $time2 = utime();
    $this->execution_time += $time2 - $time1;
    $this->time_log("insert", $time2 - $time1);

    return $id;
  }

  function update($table, $fields_values = array(), $q_ending = "")
  {
    $time1 = utime();

    $t = $this->get_table_name($table);

    // fast construction to get record by id
    if (is_numeric($q_ending))
      $q_ending = "WHERE id='$q_ending' LIMIT 1";
    elseif (is_array($q_ending))
      $q_ending = "WHERE id".$this->in($q_ending);
    elseif (!$q_ending && $q_ending !== "")
    {
      $this->_log("Warning, empty Q_ENDING in update ($table)");
      return false;
      //$q_ending = "WHERE 1=0";
    }
    //

    $q1 = "";
    foreach ($fields_values as $f => $v)
    {
      $q1 .= $f{0} == "*" ? substr($f, 1)."=$v," : "$f='$v',";
    }
    if ($q1) $q1 = substr($q1, 0, -1);

    $q = "UPDATE $t SET $q1 $q_ending";

    $this->__q("LOCK TABLES $t WRITE");
    if (!$this->__rq($q))
    {
      $this->_log("unable to update");

      $this->__q("UNLOCK TABLES");

      return false;
    }
    $this->affected_rows = $this->__affected_rows();
    $this->__q("UNLOCK TABLES");

    $time2 = utime();
    $this->execution_time += $time2 - $time1;
    $this->time_log("update", $time2 - $time1, $q);

    return true;
  }

  function delete($table, $q_ending = "")
  {
    $time1 = utime();

    $t = $this->get_table_name($table);

    // fast construction to get record by id
    if (is_numeric($q_ending))
      $q_ending = "WHERE id='$q_ending' LIMIT 1";
    elseif (is_array($q_ending))
      $q_ending = "WHERE id".$this->in($q_ending);
    elseif (!$q_ending && $q_ending !== "")
    {
      $this->_log("Warning, empty Q_ENDING in delete ($table)");
      return false;
      //$q_ending = "WHERE 1=0";
    }
    //

    $q = "DELETE FROM $t $q_ending";

    $this->__q("LOCK TABLES $t WRITE");
    if (!$this->__rq($q))
    {
      $this->_log("unable to delete");

      $this->__q("UNLOCK TABLES");

      return false;
    }
    $this->__q("UNLOCK TABLES");

    $time2 = utime();
    $this->execution_time += $time2 - $time1;
    $this->time_log("delete", $time2 - $time1, $q);

    return true;
  }

  function drop($table)
  {
    $t = $this->get_table_name($table);

    if (!$this->__rq("DROP TABLE $t"))
      return $this->_log("unable to drop table $t");

    return true;
  }

  function reset(&$rs)
  {
    return $this->__reset($rs);
  }

  function fetch($rs)
  {
    return $this->__fetch($rs);
  }

  function fetch_array($rs)
  {
    return $this->__fetch_array($rs);
  }

  function fetch_ar($rs)
  {
    return $this->fetch_array($rs);
  }

  function rs_go($func, $table, $q_ending = "", $q_fields = "*")
  {
    $i = 0;

    $rs = $this->rs($table, $q_ending, $q_fields);
    while ($r = $this->fetch($rs))
    {
      if (is_object($func))
        $func->__invoke($r, $i++);
      else
        $func($r, $i++);
    }
  }

  function count($rs)
  {
    return $this->__count($rs);
  }

  function set_charset($name)
  {
    return $this->__set_charset($name);
  }

  function get_charset()
  {
    return $this->__get_charset();
  }

  /* these methods should get rewritten */

  abstract function __connect();
  abstract function __close();
  abstract function __error();
  abstract function __q($q);
  abstract function __rq($q);
  abstract function __reset(&$rs);
  abstract function __fetch($rs);
  abstract function __fetch_array($rs);
  abstract function __count($rs);
  abstract function __insert_id();
  abstract function __affected_rows();
  abstract function __set_charset($name);
  abstract function __get_charset();
}

// mysql class

class diMYSQL extends diDB
{
  function __connect()
  {
    $time1 = utime();

    if (!$this->link = @mysql_connect($this->host, $this->username, $this->password))
      return $this->_log("unable to connect to host $this->host");

    if (!@mysql_select_db($this->dbname, $this->link))
      return $this->_log("unable to select database $this->dbname");

    $time2 = utime();
    $this->execution_time += $time2 - $time1;

    $this->time_log("connect", $time2 - $time1);

    return true;
  }

  function __close()
  {
    if (!mysql_close($this->link))
      return $this->_log("unable to close connection");

    return true;
  }

  function __error()
  {
    return mysql_error();
  }

  function __q($q)
  {
    return mysql_query($q, $this->link);
  }

  function __rq($q)
  {
    return $this->__q($q);
  }

  function __reset(&$rs)
  {
    if ($this->count($rs))
      mysql_data_seek($rs, 0);
  }

  function __fetch($rs)
  {
    return mysql_fetch_object($rs);
  }

  function __fetch_array($rs)
  {
    return mysql_fetch_assoc($rs);
  }

  function __count($rs)
  {
    return mysql_num_rows($rs);
  }

  function __insert_id()
  {
    return mysql_insert_id();
  }

  function __affected_rows()
  {
    return mysql_affected_rows();
  }

  function escape_string($s)
  {
    return mysql_real_escape_string($s, $this->link);
  }

  function __set_charset($name)
  {
    return mysql_set_charset($name, $this->link);
  }

  function __get_charset()
  {
    return mysql_client_encoding($this->link);
  }
}

// mysqli class

class diMYSQLi extends diDB
{
  function __connect()
  {
    $time1 = utime();

    if (!$this->link = new mysqli($this->host, $this->username, $this->password, $this->dbname))
      return $this->_log("unable to connect to host $this->host/$this->dbname");

    $time2 = utime();
    $this->execution_time += $time2 - $time1;

    $this->time_log("connect", $time2 - $time1);

    return true;
  }

  function __close()
  {
    if (!$this->link->close())
      return $this->_log("unable to close connection");

    return true;
  }

  function __error()
  {
    return $this->link->error;
  }

  function __q($q)
  {
    return $this->link->query($q);
  }

  function __rq($q)
  {
    return $this->link->real_query($q);
  }

  function __reset(&$rs)
  {
    if ($this->count($rs))
      $rs->data_seek(0);
  }

  function __fetch($rs)
  {
    return $rs ? $rs->fetch_object() : false;
  }

  function __fetch_array($rs)
  {
    return $rs ? $rs->fetch_assoc() : false;
  }

  function __count($rs)
  {
    return $rs ? $rs->num_rows : false;
  }

  function __insert_id()
  {
    return $this->link->insert_id;
  }

  function __affected_rows()
  {
    return $this->link->affected_rows;
  }

  function escape_string($s)
  {
    return $this->link->escape_string($s);
  }

  function __set_charset($name)
  {
    return $this->link->set_charset($name);
  }

  function __get_charset()
  {
    return $this->link->character_set_name();
  }
}
?>