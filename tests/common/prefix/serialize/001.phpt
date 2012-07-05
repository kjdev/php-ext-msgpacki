--TEST--
msgpacki_serialize()/msgpacki_unserialize()/var_dump()
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

class t {
    function __construct() {
        $this->a = "hallo";
    }
}

class s {
    public $a;
    public $b;
    public $c;

    function __construct() {
        $this->a = "hallo";
        $this->b = "php";
        $this->c = "world";
        $this->d = "!";
    }

    function __sleep() {
        echo "__sleep called\n";
        return array("a","c");
    }

    function __wakeup() {
        echo "__wakeup called\n";
    }
}


echo bin2hex(msgpacki_serialize(NULL)) . "\n";
echo bin2hex(msgpacki_serialize((bool) true)) . "\n";
echo bin2hex(msgpacki_serialize((bool) false)) . "\n";
echo bin2hex(msgpacki_serialize(1)) . "\n";
echo bin2hex(msgpacki_serialize(0)) . "\n";
echo bin2hex(msgpacki_serialize(-1)) . "\n";
echo bin2hex(msgpacki_serialize(2147483647)) . "\n";
echo bin2hex(msgpacki_serialize(-2147483647)) . "\n";
echo bin2hex(msgpacki_serialize(1.123456789)) . "\n";
echo bin2hex(msgpacki_serialize(1.0)) . "\n";
echo bin2hex(msgpacki_serialize(0.0)) . "\n";
echo bin2hex(msgpacki_serialize(-1.0)) . "\n";
echo bin2hex(msgpacki_serialize(-1.123456789)) . "\n";
echo bin2hex(msgpacki_serialize("hallo")) . "\n";
echo bin2hex(msgpacki_serialize(array(1,1.1,"hallo",NULL,true,array()))) . "\n";

$t = new t();
$data = msgpacki_serialize($t);
echo bin2hex($data) . "\n";
$t = msgpacki_unserialize($data);
var_dump($t);

$t = new s();
$data = msgpacki_serialize($t);
echo bin2hex($data) . "\n";
$t = msgpacki_unserialize($data);
var_dump($t);

$a = array("a" => "test");
$a[ "b" ] = &$a[ "a" ];
var_dump($a);
$data = msgpacki_serialize($a);
echo bin2hex($data) . "\n";
$a = msgpacki_unserialize($data);
var_dump($a);
?>
===DONE===
--EXPECTF--
c0
c3
c2
01
00
ff
ce7fffffff
d280000001
cb3ff1f9add3739636
cb3ff0000000000000
cb0000000000000000
cbbff0000000000000
cbbff1f9add3739636
a568616c6c6f
86000101cb3ff199999999999a02a568616c6c6f03c004c30590
82c0a174a161a568616c6c6f
object(%s)#%d (1) {
  ["a"]=>
  string(5) "hallo"
}
__sleep called
83c0a173a161a568616c6c6fa163a5776f726c64
__wakeup called
object(%s)#%d (3) {
  ["a"]=>
  string(5) "hallo"
  ["b"]=>
  NULL
  ["c"]=>
  string(5) "world"
}
array(2) {
  ["a"]=>
  &string(4) "test"
  ["b"]=>
  &string(4) "test"
}
82a161a474657374a16282c0010002
array(2) {
  ["a"]=>
  &string(4) "test"
  ["b"]=>
  &string(4) "test"
}
===DONE===
