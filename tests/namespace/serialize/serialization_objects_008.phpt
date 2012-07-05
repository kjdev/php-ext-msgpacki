--TEST--
Bad unserialize_callback_func
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

ini_set('unserialize_callback_func','Nonexistent');
$o = unserialize(pack('H*', '81c0a3464f4f'));
var_dump($o);
echo "Done";
?>
--EXPECTF--

Warning: MessagePack unserialize: defined (Nonexistent) but not found in %s on line 9
object(__PHP_Incomplete_Class)#%d (1) {
  ["__PHP_Incomplete_Class_Name"]=>
  string(3) "FOO"
}
Done