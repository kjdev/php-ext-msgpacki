--TEST--
Ensure __autoload is called twice if unserialize_callback_func is defined.
--FILE--
<?php


if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

/*
function __autoload($name) {
    echo "in __autoload($name)\n";
}
*/
eval('function __autoload($name) { echo "in __autoload($name)\n"; }');

ini_set('unserialize_callback_func','check');

function check($name) {
    echo "in check($name)\n";
}

$o = msgpacki_unserialize(pack('H*', '81c0a3464f4f'));

var_dump($o);

echo "Done";
?>
--EXPECTF--
in __autoload(FOO)
in check(FOO)
in __autoload(FOO)

Warning: MessagePack unserialize: Function check() hasn't defined the class it was called for in %s on line 21
object(__PHP_Incomplete_Class)#%d (1) {
  ["__PHP_Incomplete_Class_Name"]=>
  string(3) "FOO"
}
Done