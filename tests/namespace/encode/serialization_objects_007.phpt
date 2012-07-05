--TEST--
Ensure __autoload is called twice if unserialize_callback_func is defined.
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

/*
function __autoload($name) {
    echo "in __autoload($name)\n";
}
*/
eval('function __autoload($name) { echo "in __autoload($name)\n"; }');

ini_set('unserialize_callback_func','MessagePacki\check');

function check($name) {
    echo "in check($name)\n";
}

$o = decode(pack('H*', '81c0a3464f4f'));

var_dump($o);

echo "Done";
?>
--EXPECTF--
object(stdClass)#1 (0) {
}
Done