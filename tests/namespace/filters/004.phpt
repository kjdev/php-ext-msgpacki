--TEST--
filter_append() and invalid arguments
--FILE--
<?php
namespace MessagePacki;

class test_filter extends Filter
{}

filter_append("hoge");
filter_append("");
filter_append(array());
filter_append(null);

filter_register("test", '\MessagePacki\test_filter');
filter_append("foo");
filter_append("test");
filter_append("huge");

$ser = serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(unserialize($ser));
?>
--EXPECTF--
Warning: MessagePack filter_append: Enable filter empty in %s on line %d

Warning: MessagePack filter_append: Filter name cannot be empty in %s on line %d

Warning: MessagePacki\filter_append() expects parameter 1 to be string, array given in %s on line %d

Warning: MessagePack filter_append: Filter name cannot be empty in %s on line %d

Warning: MessagePack filter_append: No such find filter: "foo" in %s on line %d

Warning: MessagePack filter_append: No such find filter: "huge" in %s on line %d
string(20) "a95468616e6b20796f75"
string(9) "Thank you"
