--TEST--
msgpacki_serialize()/msgpacki_unserialize() with exotic letters
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$åäöÅÄÖ = array('åäöÅÄÖ' => 'åäöÅÄÖ');

class ÜberKööliäå {
    public $åäöÅÄÖüÜber = 'åäöÅÄÖ';
}

$foo = new Überkööliäå();

var_dump(bin2hex(msgpacki_serialize($foo)));
var_dump(msgpacki_unserialize(msgpacki_serialize($foo)));
var_dump(bin2hex(msgpacki_serialize($åäöÅÄÖ)));
var_dump(msgpacki_unserialize(msgpacki_serialize($åäöÅÄÖ)));
?>
--EXPECT--
string(66) "82c0abdc6265724bf6f66c69e4e5abe5e4f6c5c4d6fcdc626572a6e5e4f6c5c4d6"
object(ÜberKööliäå)#2 (1) {
  ["åäöÅÄÖüÜber"]=>
  string(6) "åäöÅÄÖ"
}
string(30) "81a6e5e4f6c5c4d6a6e5e4f6c5c4d6"
array(1) {
  ["åäöÅÄÖ"]=>
  string(6) "åäöÅÄÖ"
}
