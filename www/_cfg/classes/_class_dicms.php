<?
class diCMS
{
  protected $modes = array();
  protected $orig_modes = array();

  public function __construct()
  {
    $this->get_modes();
  }

  public function get_modes()
  {
    $m = addslashes(ltrim($_SERVER["REQUEST_URI"], "/")); //strtolower(
    $x = strpos($m, "?");
    if ($x !== false)
      $m = rtrim(substr($m, 0, $x), "/");

    $this->modes = explode("/", $m);
    $this->orig_modes = $this->modes;

    if ($this->modes && !$this->modes[0]) array_splice($this->modes, 0, 1);

    for ($i = 0; $i < count($this->modes); $i++)
    {
      if ($this->modes[$i] === "")
      {
        array_splice($this->modes, $i, 1);
        $i--;
      }
      else
      {
        $this->{"m".$i} = $this->modes[$i];
      }
    }
  }

  public function m($idx = 0)
  {
    return isset($this->modes[$idx]) ? $this->modes[$idx] : false;
  }
}
?>