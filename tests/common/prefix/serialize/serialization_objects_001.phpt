--TEST--
Test msgpacki_serialize() & msgpacki_unserialize() functions: objects
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
$serialize_data = msgpacki_serialize( $members_obj );
var_dump(bin2hex($serialize_data));
$members_obj = msgpacki_unserialize( $serialize_data );
var_dump($members_obj);

echo "\n--- testing reference to an obj ---\n";
$ref_members_obj = &$members_obj;
$serialize_data = msgpacki_serialize( $ref_members_obj );
var_dump(bin2hex($serialize_data));
$ref_members_obj = msgpacki_unserialize( $serialize_data );
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
string(176) "84c0a76d656d62657273b4006d656d62657273007661725f707269766174650ab0002a007661725f70726f746563746564a6737472696e67aa7661725f7075626c69638300cbc05907df3b645a1d01a6737472696e6702c3"
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

--- testing reference to an obj ---
string(176) "84c0a76d656d62657273b4006d656d62657273007661725f707269766174650ab0002a007661725f70726f746563746564a6737472696e67aa7661725f7075626c69638300cbc05907df3b645a1d01a6737472696e6702c3"
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

Done
