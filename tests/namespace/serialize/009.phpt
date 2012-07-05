--TEST--
serialize() and floats/doubles
--INI--
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$foo = 1.428571428571428647642857142;
$bar = unserialize(serialize($foo));
var_dump(($foo === $bar));
?>
--EXPECT--
bool(true)
