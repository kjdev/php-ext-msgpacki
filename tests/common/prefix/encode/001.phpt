--TEST--
msgpacki_encode()/msgpacki_decode()/var_dump()
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


echo bin2hex(msgpacki_encode(NULL)) . "\n";
echo bin2hex(msgpacki_encode((bool) true)) . "\n";
echo bin2hex(msgpacki_encode((bool) false)) . "\n";
echo bin2hex(msgpacki_encode(1)) . "\n";
echo bin2hex(msgpacki_encode(0)) . "\n";
echo bin2hex(msgpacki_encode(-1)) . "\n";
echo bin2hex(msgpacki_encode(2147483647)) . "\n";
echo bin2hex(msgpacki_encode(-2147483647)) . "\n";
echo bin2hex(msgpacki_encode(1.123456789)) . "\n";
echo bin2hex(msgpacki_encode(1.0)) . "\n";
echo bin2hex(msgpacki_encode(0.0)) . "\n";
echo bin2hex(msgpacki_encode(-1.0)) . "\n";
echo bin2hex(msgpacki_encode(-1.123456789)) . "\n";
echo bin2hex(msgpacki_encode("hallo")) . "\n";
echo bin2hex(msgpacki_encode(array(1,1.1,"hallo",NULL,true,array()))) . "\n";

$t = new t();
$data = msgpacki_encode($t);
echo bin2hex($data) . "\n";
$t = msgpacki_decode($data);
var_dump($t);

$t = new s();
$data = msgpacki_encode($t);
echo bin2hex($data) . "\n";
$t = msgpacki_decode($data);
var_dump($t);

$a = array("a" => "test");
$a[ "b" ] = &$a[ "a" ];
var_dump($a);
$data = msgpacki_encode($a);
echo bin2hex($data) . "\n";
$a = msgpacki_decode($data);
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
9601cb3ff199999999999aa568616c6c6fc0c390
81a161a568616c6c6f
array(1) {
  ["a"]=>
  string(5) "hallo"
}
__sleep called
82a161a568616c6c6fa163a5776f726c64
array(2) {
  ["a"]=>
  string(5) "hallo"
  ["c"]=>
  string(5) "world"
}
array(2) {
  ["a"]=>
  &string(4) "test"
  ["b"]=>
  &string(4) "test"
}
82a161a474657374a162a474657374
array(2) {
  ["a"]=>
  string(4) "test"
  ["b"]=>
  string(4) "test"
}
===DONE===
