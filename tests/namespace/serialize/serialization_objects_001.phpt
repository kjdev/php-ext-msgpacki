--TEST--
Test serialize() & unserialize() functions: objects
--INI--
--FILE--
<?php
namespace MessagePacki;

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
$serialize_data = serialize( $members_obj );
var_dump(bin2hex($serialize_data));
$members_obj = unserialize( $serialize_data );
var_dump($members_obj);

echo "\n--- testing reference to an obj ---\n";
$ref_members_obj = &$members_obj;
$serialize_data = serialize( $ref_members_obj );
var_dump(bin2hex($serialize_data));
$ref_members_obj = unserialize( $serialize_data );
var_dump($ref_members_obj);

echo "\nDone";
?>
--EXPECTF--

--- Testing objects ---
object(MessagePacki\members)#%d (3) {
  ["var_private":"MessagePacki\members":private]=>
  int(10)
  ["var_protected":protected]=>
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
string(232) "84c0b44d6573736167655061636b695c6d656d62657273da0021004d6573736167655061636b695c6d656d62657273007661725f707269766174650ab0002a007661725f70726f746563746564a6737472696e67aa7661725f7075626c69638300cbc05907df3b645a1d01a6737472696e6702c3"
object(MessagePacki\members)#%d (3) {
  ["var_private":"MessagePacki\members":private]=>
  int(10)
  ["var_protected":protected]=>
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
string(232) "84c0b44d6573736167655061636b695c6d656d62657273da0021004d6573736167655061636b695c6d656d62657273007661725f707269766174650ab0002a007661725f70726f746563746564a6737472696e67aa7661725f7075626c69638300cbc05907df3b645a1d01a6737472696e6702c3"
object(MessagePacki\members)#%d (3) {
  ["var_private":"MessagePacki\members":private]=>
  int(10)
  ["var_protected":protected]=>
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
