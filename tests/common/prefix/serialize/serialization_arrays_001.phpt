--TEST--
Test msgpacki_serialize() & msgpacki_unserialize() functions: arrays (circular references)
--INI--
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

echo "\n--- Testing Circular reference of an array ---\n";

echo "-- Normal array --\n";
$arr_circ = array(0, 1, -2, 3.333333, "a", array(), &$arr_circ);
$serialize_data = msgpacki_serialize($arr_circ);
if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    $arr_circ[6] = null;
}
var_dump(bin2hex($serialize_data));
$arr_circ = msgpacki_unserialize($serialize_data);
var_dump($arr_circ);
if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    $arr_circ[6] = null;
}

echo "\n-- Associative array --\n";
$arr_asso = array("a" => "test");
$arr_asso[ "b" ] = &$arr_asso[ "a" ];
var_dump($arr_asso);
$serialize_data = msgpacki_serialize($arr_asso);
var_dump(bin2hex($serialize_data));
$arr_asso = msgpacki_unserialize($serialize_data);
var_dump($arr_asso);

echo "\nDone";
?>
--EXPECTF--
--- Testing Circular reference of an array ---
-- Normal array --
string(102) "870000010102fe03cb400aaaaa7ded6ba904a161059006870000010102fe03cb400aaaaa7ded6ba904a16105900682c0010008"
array(7) {
  [0]=>
  int(0)
  [1]=>
  int(1)
  [2]=>
  int(-2)
  [3]=>
  float(3.333333)
  [4]=>
  string(1) "a"
  [5]=>
  array(0) {
  }
  [6]=>
  &array(7) {
    [0]=>
    int(0)
    [1]=>
    int(1)
    [2]=>
    int(-2)
    [3]=>
    float(3.333333)
    [4]=>
    string(1) "a"
    [5]=>
    array(0) {
    }
    [6]=>
    *RECURSION*
  }
}

-- Associative array --
array(2) {
  ["a"]=>
  &string(4) "test"
  ["b"]=>
  &string(4) "test"
}
string(30) "82a161a474657374a16282c0010002"
array(2) {
  ["a"]=>
  &string(4) "test"
  ["b"]=>
  &string(4) "test"
}

Done
