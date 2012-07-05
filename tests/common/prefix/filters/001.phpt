--TEST--
msgpacki_filter_register() and invalid arguments
--FILE--
<?php

var_dump(msgpacki_filter_register("", ""));
var_dump(msgpacki_filter_register("test", ""));
var_dump(msgpacki_filter_register("", "test"));
var_dump(msgpacki_filter_register("------", "\\nonexistentclass"));
var_dump(msgpacki_filter_register(array(), "aa"));
var_dump(msgpacki_filter_register("", array()));

echo "Done\n";
?>
--EXPECTF--
Warning: MessagePack filter_register: Filter name cannot be empty in %s on line %d
bool(false)

Warning: MessagePack filter_register: Class name cannot be empty in %s on line %d
bool(false)

Warning: MessagePack filter_register: Filter name cannot be empty in %s on line %d
bool(false)

Warning: MessagePack filter_register: MessagePack filter "------" requires class "\nonexistentclass", but that class is not defined in %son line %d
bool(false)

Warning: msgpacki_filter_register() expects parameter 1 to be string, array given in %s on line %d
bool(false)

Warning: msgpacki_filter_register() expects parameter 2 to be string, array given in %s on line %d
bool(false)
Done
