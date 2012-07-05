--TEST--
Class MessagePacki
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$m = new MessagePacki();
var_dump($m);
var_dump(get_class_methods($m));
?>
===DONE===
--EXPECTF--
object(MessagePacki)#1 (0) {
}
array(15) {
  [0]=>
  string(11) "__construct"
  [1]=>
  string(4) "pack"
  [2]=>
  string(6) "unpack"
  [3]=>
  string(8) "get_mode"
  [4]=>
  string(8) "set_mode"
  [5]=>
  string(13) "append_filter"
  [6]=>
  string(14) "prepend_filter"
  [7]=>
  string(13) "remove_filter"
  [8]=>
  string(11) "get_filters"
  [9]=>
  string(7) "getMode"
  [10]=>
  string(7) "setMode"
  [11]=>
  string(12) "appendFilter"
  [12]=>
  string(13) "prependFilter"
  [13]=>
  string(12) "removeFilter"
  [14]=>
  string(10) "getFilters"
}
===DONE===
