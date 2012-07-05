--TEST--
Custom unserialization of classes with no custom unserializer.
--FILE--
<?php

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$ser = pack('H*', '82c003a143a6646173646173');
$a = msgpacki_unserialize($ser);
eval('class C {}');
$b = msgpacki_unserialize($ser);

var_dump($a, $b);

echo "Done";
?>
--EXPECTF--
Warning: MessagePack unserialize: Class __PHP_Incomplete_Class has no unserializer in %sserialization_objects_009.php on line %d

Warning: MessagePack unserialize: Class C has no unserializer in %sserialization_objects_009.php on line %d
object(__PHP_Incomplete_Class)#%d (1) {
  ["__PHP_Incomplete_Class_Name"]=>
  string(1) "C"
}
object(C)#%d (0) {
}
Done