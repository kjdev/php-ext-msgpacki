--TEST--
msgpacki_serialize() and __sleep()
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

$t = new t();
$data = msgpacki_serialize($t);
echo bin2hex($data) . "\n";
$t = msgpacki_unserialize($data);
var_dump($t);

?>
--EXPECTF--
__sleep called

Notice: MessagePack serialize: "b" returned as member variable from __sleep() but does not exist in %s007.php on line %d
83c0a174a161a568656c6c6fa162c0
object(t)#%d (2) {
  ["a"]=>
  string(5) "hello"
  ["b"]=>
  NULL
}
