--TEST--
mode:serialize ini_set(MSGPACKI_MODE_PHP)
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

ini_set('msgpacki.mode', MSGPACKI_MODE_PHP);
var_dump(ini_get('msgpacki.mode'));

var_dump($val);

$ses = msgpacki_serialize($val);
var_dump(bin2hex($ses));
$uns = msgpacki_unserialize($ses);
var_dump($uns);
?>
===DONE===
--EXPECTF--
string(1) "2"
object(C)#1 (3) {
  ["a"]=>
  string(5) "Pub:a"
  [%r"?b"?:protected"?%r]=>
  string(5) "Pro:b"
  [%r"?c"?:("C":)?private"?%r]=>
  string(5) "Pri:c"
}
string(68) "84c0a143a161a55075623a61a4002a0062a550726f3a62a400430063a55072693a63"
object(C)#2 (3) {
  ["a"]=>
  string(5) "Pub:a"
  [%r"?b"?:protected"?%r]=>
  string(5) "Pro:b"
  [%r"?c"?:("C":)?private"?%r]=>
  string(5) "Pri:c"
}
===DONE===
