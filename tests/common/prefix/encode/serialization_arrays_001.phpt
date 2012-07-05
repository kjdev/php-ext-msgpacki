--TEST--
Test msgpacki_encode() & msgpacki_decode() functions: arrays (circular references)
--INI--
--FILE--
<?php


if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

echo "\n--- Testing Circular reference of an array ---\n";

echo "-- Normal array --\n";
$arr_circ = array(0, 1, -2, 3.333333, "a", array(), &$arr_circ);
$serialize_data = msgpacki_encode($arr_circ);
var_dump(bin2hex($serialize_data));
$arr_circ = msgpacki_decode($serialize_data);
var_dump($arr_circ);

echo "\n-- Associative array --\n";
$arr_asso = array("a" => "test");
$arr_asso[ "b" ] = &$arr_asso[ "a" ];
var_dump($arr_asso);
$serialize_data = msgpacki_encode($arr_asso);
var_dump(bin2hex($serialize_data));
$arr_asso = msgpacki_decode($serialize_data);
var_dump($arr_asso);

echo "\nDone";
?>
--EXPECTF--
--- Testing Circular reference of an array ---
-- Normal array --
string(98) "970001fecb400aaaaa7ded6ba9a16190970001fecb400aaaaa7ded6ba9a16190970001fecb400aaaaa7ded6ba9a16190c0"
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
      NULL
    }
  }
}

-- Associative array --
array(2) {
  ["a"]=>
  &string(4) "test"
  ["b"]=>
  &string(4) "test"
}
string(30) "82a161a474657374a162a474657374"
array(2) {
  ["a"]=>
  string(4) "test"
  ["b"]=>
  string(4) "test"
}

Done
