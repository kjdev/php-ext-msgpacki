--TEST--
mode:serialize ini-directive(MSGPACKI_MODE_PHP:2)
--INI--
msgpacki.mode=2
--FILE--
<?php
namespace MessagePacki;

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

$ses = serialize($val);
var_dump(bin2hex($ses));
$uns = unserialize($ses);
var_dump($uns);
?>
===DONE===
--EXPECTF--
string(1) "2"
object(MessagePacki\C)#1 (3) {
  ["a"]=>
  string(5) "Pub:a"
  ["b":protected]=>
  string(5) "Pro:b"
  ["c":"MessagePacki\C":private]=>
  string(5) "Pri:c"
}
string(120) "84c0ae4d6573736167655061636b695c43a161a55075623a61a4002a0062a550726f3a62b1004d6573736167655061636b695c430063a55072693a63"
object(MessagePacki\C)#2 (3) {
  ["a"]=>
  string(5) "Pub:a"
  ["b":protected]=>
  string(5) "Pro:b"
  ["c":"MessagePacki\C":private]=>
  string(5) "Pri:c"
}
===DONE===
