--TEST--
msgpacki_serialize() and floats/doubles
--INI--
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$foo = 1.428571428571428647642857142;
$bar = msgpacki_unserialize(msgpacki_serialize($foo));
var_dump(($foo === $bar));
?>
--EXPECT--
bool(true)
