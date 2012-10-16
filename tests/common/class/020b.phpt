--TEST--
MessagePacki::append_filter() and invalid arguments
--FILE--
<?php

class test_filter extends MessagePacki_Filter {}

$m = new MessagePacki();

$m->append_filter("hoge");
$m->append_filter("");
$m->append_filter(array());
$m->append_filter(null);

$m->append_filter('test_filter');
$m->append_filter("foo_filter");

$value = "Thank you";
$ser = $m->pack($value);
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));
?>
--EXPECTF--
Warning: MessagePacki::append_filter(): MessagePack filter "hoge" requires class "hoge", but that class is not defined in %s on line %d

Warning: MessagePacki::append_filter(): Filter/Class name cannot be empty in %s on line %d

Warning: MessagePacki::append_filter() expects parameter 1 to be string, array given in %s on line %d

Warning: MessagePacki::append_filter(): Filter/Class name cannot be empty in %s on line %d

Warning: MessagePacki::append_filter(): MessagePack filter "foo_filter" requires class "foo_filter", but that class is not defined in %s on line %d
string(20) "a95468616e6b20796f75"
string(9) "Thank you"
