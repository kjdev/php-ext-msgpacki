--TEST--
serialize()/unserialize() with exotic letters
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$åäöÅÄÖ = array('åäöÅÄÖ' => 'åäöÅÄÖ');

class ÜberKööliäå {
    public $åäöÅÄÖüÜber = 'åäöÅÄÖ';
}

$foo = new Überkööliäå();

var_dump(bin2hex(serialize($foo)));
var_dump(unserialize(serialize($foo)));
var_dump(bin2hex(serialize($åäöÅÄÖ)));
var_dump(unserialize(serialize($åäöÅÄÖ)));
?>
--EXPECT--
string(92) "82c0b84d6573736167655061636b695cdc6265724bf6f66c69e4e5abe5e4f6c5c4d6fcdc626572a6e5e4f6c5c4d6"
object(MessagePacki\ÜberKööliäå)#2 (1) {
  ["åäöÅÄÖüÜber"]=>
  string(6) "åäöÅÄÖ"
}
string(30) "81a6e5e4f6c5c4d6a6e5e4f6c5c4d6"
array(1) {
  ["åäöÅÄÖ"]=>
  string(6) "åäöÅÄÖ"
}
