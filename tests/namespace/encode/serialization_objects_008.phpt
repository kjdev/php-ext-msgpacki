--TEST--
Bad unserialize_callback_func
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

ini_set('unserialize_callback_func','Nonexistent');
$o = decode(pack('H*', '81c0a3464f4f'));
var_dump($o);
echo "Done";
?>
--EXPECTF--
object(stdClass)#1 (0) {
}
Done
