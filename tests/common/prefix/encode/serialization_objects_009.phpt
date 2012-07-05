--TEST--
Custom unserialization of classes with no custom unserializer.
--FILE--
<?php


if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$ser = pack('H*', '82c003a143a6646173646173');
$a = msgpacki_decode($ser);
eval('class C {}');
$b = msgpacki_decode($ser);

var_dump($a, $b);

echo "Done";
?>
--EXPECTF--
object(stdClass)#%d (1) {
  ["C"]=>
  string(6) "dasdas"
}
object(stdClass)#%d (1) {
  ["C"]=>
  string(6) "dasdas"
}
Done
