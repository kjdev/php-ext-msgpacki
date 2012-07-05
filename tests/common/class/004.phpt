--TEST--
MessagePacki::pack() and __sleep()
--FILE--
<?php

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

class t {
    function __construct() {
        $this->a = 'hello';
    }

    function __sleep() {
        echo "__sleep called\n";
        return array('a','b');
    }
}

$m = new MessagePacki();

$t = new t();
$data = $m->pack($t);
echo bin2hex($data) . "\n";
$t = $m->unpack($data);
var_dump($t);

?>
--EXPECTF--
__sleep called

Notice: MessagePack serialize: "b" returned as member variable from __sleep() but does not exist in %s on line %d
83c0a174a161a568656c6c6fa162c0
object(t)#%d (2) {
  ["a"]=>
  string(5) "hello"
  ["b"]=>
  NULL
}
