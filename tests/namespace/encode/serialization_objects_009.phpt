--TEST--
Custom unserialization of classes with no custom unserializer.
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$ser = pack('H*', '82c003a143a6646173646173');
$a = decode($ser);
eval('class C {}');
$b = decode($ser);

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
