--TEST--
Test msgpacki_encode() & msgpacki_decode() functions: objects
--INI--
--FILE--
<?php


if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

echo "\n--- Testing objects ---\n";

class members
{
  private $var_private = 10;
  protected $var_protected = "string";
  public $var_public = array(-100.123, "string", TRUE);
}

$members_obj = new members();
var_dump($members_obj);
$serialize_data = msgpacki_encode( $members_obj );
var_dump(bin2hex($serialize_data));
$members_obj = msgpacki_decode( $serialize_data );
var_dump($members_obj);

echo "\n--- testing reference to an obj ---\n";
$ref_members_obj = &$members_obj;
$serialize_data = msgpacki_encode( $ref_members_obj );
var_dump(bin2hex($serialize_data));
$ref_members_obj = msgpacki_decode( $serialize_data );
var_dump($ref_members_obj);

echo "\nDone";
?>
--EXPECTF--

--- Testing objects ---
object(members)#%d (3) {
  [%r"?var_private"?:("members":)?private"?%r]=>
  int(10)
  [%r"?var_protected"?:protected"?%r]=>
  string(6) "string"
  ["var_public"]=>
  array(3) {
    [0]=>
    float(-100.123)
    [1]=>
    string(6) "string"
    [2]=>
    bool(true)
  }
}
string(128) "83ab7661725f707269766174650aad7661725f70726f746563746564a6737472696e67aa7661725f7075626c696393cbc05907df3b645a1da6737472696e67c3"
array(3) {
  ["var_private"]=>
  int(10)
  ["var_protected"]=>
  string(6) "string"
  ["var_public"]=>
  array(3) {
    [0]=>
    float(-100.123)
    [1]=>
    string(6) "string"
    [2]=>
    bool(true)
  }
}

--- testing reference to an obj ---
string(128) "83ab7661725f707269766174650aad7661725f70726f746563746564a6737472696e67aa7661725f7075626c696393cbc05907df3b645a1da6737472696e67c3"
array(3) {
  ["var_private"]=>
  int(10)
  ["var_protected"]=>
  string(6) "string"
  ["var_public"]=>
  array(3) {
    [0]=>
    float(-100.123)
    [1]=>
    string(6) "string"
    [2]=>
    bool(true)
  }
}

Done
