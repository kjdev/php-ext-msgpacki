--TEST--
session object serialization
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

class foo {
    public $bar = "ok";

    function method() { $this->yes = "done"; }
}

$baz = new foo;
$baz->method();

$arr[3] = new foo;
$arr[3]->method();
session_start();
$_SESSION["baz"] = $baz;
$_SESSION["arr"] = $arr;
$val = session_encode();
var_dump(bin2hex($val));
var_dump(msgpacki_unserialize($val));
session_destroy();
?>
--EXPECTF--
string(110) "82a362617a83c0a3666f6fa3626172a26f6ba3796573a4646f6e65a3617272810383c0a3666f6fa3626172a26f6ba3796573a4646f6e65"
array(2) {
  ["baz"]=>
  object(foo)#%d (2) {
    ["bar"]=>
    string(2) "ok"
    ["yes"]=>
    string(4) "done"
  }
  ["arr"]=>
  array(1) {
    [3]=>
    object(foo)#%d (2) {
      ["bar"]=>
      string(2) "ok"
      ["yes"]=>
      string(4) "done"
    }
  }
}