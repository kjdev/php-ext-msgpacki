--TEST--
session object deserialization
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
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

error_reporting(E_ALL);

class foo {
    public $bar = "ok";
    function method() { $this->yes++; }
}

session_id("abtest");
session_start();
session_decode(pack('H*', '82a362617a83c0a3666f6fa3626172a26f6ba379657301a3617272810383c0a3666f6fa3626172a26f6ba379657301'));

$_SESSION["baz"]->method();
$_SESSION["arr"][3]->method();

var_dump($_SESSION["baz"]);
var_dump($_SESSION["arr"]);
session_destroy();
--EXPECTF--
object(foo)#%d (2) {
  ["bar"]=>
  string(2) "ok"
  ["yes"]=>
  int(2)
}
array(1) {
  [3]=>
  object(foo)#%d (2) {
    ["bar"]=>
    string(2) "ok"
    ["yes"]=>
    int(2)
  }
}
