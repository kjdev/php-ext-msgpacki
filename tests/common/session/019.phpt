--TEST--
serializing references test case using globals
--SKIPIF--
<?php
if (!extension_loaded("session")) {
    echo "skip needs session enabled";
}
?>
--INI--
session.use_cookies=0
session.cache_limiter=
session.serialize_handler=msgpacki
session.save_handler=files
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

error_reporting(E_ALL);

class TFoo {
    public $c;
    function TFoo($c) {
        $this->c = $c;
    }
    function inc() {
        $this->c++;
    }
}

session_id("abtest");
session_start();

$_SESSION["o1"] = new TFoo(42);
$_SESSION["o2"] =& $_SESSION["o1"];

session_write_close();

unset($_SESSION["o1"]);
unset($_SESSION["o2"]);

session_start();

var_dump($_SESSION);

$_SESSION["o1"]->inc();
$_SESSION["o2"]->inc();

var_dump($_SESSION);

session_destroy();
?>
--EXPECTF--

array(2) {
  ["o1"]=>
  &object(TFoo)#%d (1) {
    ["c"]=>
    int(42)
  }
  ["o2"]=>
  &object(TFoo)#%d (1) {
    ["c"]=>
    int(42)
  }
}
array(2) {
  ["o1"]=>
  &object(TFoo)#%d (1) {
    ["c"]=>
    int(44)
  }
  ["o2"]=>
  &object(TFoo)#%d (1) {
    ["c"]=>
    int(44)
  }
}
