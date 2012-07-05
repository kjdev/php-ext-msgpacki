--TEST--
mode:serialize ini-directive(MSGPACKI_MODE_ORIGIN:1)
--INI--
msgpacki.mode=1
--FILE--
<?php


if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

class C {
    public $a = 'Pub:a';
    protected $b = 'Pro:b';
    private $c = 'Pri:c';
}
$val = new C;

var_dump(ini_get('msgpacki.mode'));

var_dump($val);

$ses = msgpacki_serialize($val);
var_dump(bin2hex($ses));
$uns = msgpacki_unserialize($ses);
var_dump($uns);
?>
===DONE===
--EXPECTF--
string(1) "1"
object(C)#1 (3) {
  ["a"]=>
  string(5) "Pub:a"
  [%r"?b"?:protected"?%r]=>
  string(5) "Pro:b"
  [%r"?c"?:("C":)?private"?%r]=>
  string(5) "Pri:c"
}
string(50) "83a161a55075623a61a162a550726f3a62a163a55072693a63"
array(3) {
  ["a"]=>
  string(5) "Pub:a"
  ["b"]=>
  string(5) "Pro:b"
  ["c"]=>
  string(5) "Pri:c"
}
===DONE===
