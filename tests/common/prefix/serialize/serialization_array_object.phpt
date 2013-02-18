--TEST--
Test msgpacki_serialize() & msgpacki_unserialize() functions: ArrayObject
--INI--
--SKIPIF--
<?php
if (version_compare(PHP_VERSION, '5.4.0') < 0) {
    echo "skip tests before PHP 5.4";
}
?>
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

class Demo extends ArrayObject {
}

$obj = new StdClass();

$demo = new Demo;

$demo[] = $obj;
$demo[] = $obj;

$data = array(
    $demo,
    $obj,
    $obj,
);

var_dump(msgpacki_unserialize(msgpacki_serialize($data)));

echo "\nDone";
?>
--EXPECTF--
array(3) {
  [0]=>
  object(Demo)#%d (1) {
    [%r"?storage"?:("ArrayObject":)?private"?%r]=>
    array(2) {
      [0]=>
      object(stdClass)#%d (0) {
      }
      [1]=>
      object(stdClass)#%d (0) {
      }
    }
  }
  [1]=>
  object(stdClass)#%d (0) {
  }
  [2]=>
  object(stdClass)#%d (0) {
  }
}

Done
