--TEST--
serialize() and __sleep()
--FILE--
<?php
namespace MessagePacki;

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
$data = serialize($t);
echo bin2hex($data) . "\n";
$t = unserialize($data);
var_dump($t);

?>
--EXPECTF--
__sleep called

Notice: MessagePack serialize: "b" returned as member variable from __sleep() but does not exist in %s007.php on line %d
83c0ae4d6573736167655061636b695c74a161a568656c6c6fa162c0
object(MessagePacki\t)#%d (2) {
  ["a"]=>
  string(5) "hello"
  ["b"]=>
  NULL
}
