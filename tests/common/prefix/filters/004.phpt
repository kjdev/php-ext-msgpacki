--TEST--
msgpacki_filter_append() and invalid arguments
--FILE--
<?php

class test_filter extends MessagePacki_Filter
{}

msgpacki_filter_append("hoge");
msgpacki_filter_append("");
msgpacki_filter_append(array());
msgpacki_filter_append(null);

msgpacki_filter_register("test", 'test_filter');
msgpacki_filter_append("foo");
msgpacki_filter_append("test");
msgpacki_filter_append("huge");

$ser = msgpacki_serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));
?>
--EXPECTF--
Warning: MessagePack filter_append: Enable filter empty in %s on line %d

Warning: MessagePack filter_append: Filter name cannot be empty in %s on line %d

Warning: msgpacki_filter_append() expects parameter 1 to be string, array given in %s on line %d

Warning: MessagePack filter_append: Filter name cannot be empty in %s on line %d

Warning: MessagePack filter_append: No such find filter: "foo" in %s on line %d

Warning: MessagePack filter_append: No such find filter: "huge" in %s on line %d
string(20) "a95468616e6b20796f75"
string(9) "Thank you"
