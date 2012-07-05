--TEST--
Check behaviour of incomplete class
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$serialized = pack('H*', '82c0a143a17001');

$incomplete = decode($serialized);
eval('Class C {}');
$complete   = decode($serialized);


echo "\n\n---> Various types of access on complete class:\n" ;
var_dump($complete);
var_dump(is_object($complete));
var_dump($complete->p);

$ref1 = "ref1.original";
$complete->p = &$ref1;
var_dump($complete->p);
$ref1 = "ref1.changed";
var_dump($complete->p);
$complete->p = "p.changed";
var_dump($ref1);

var_dump(isset($complete->x));
$complete->x = "x.new";
var_dump(isset($complete->x));
unset($complete->x);
var_dump($complete->x);


echo "\n\n---> Same types of access on incomplete class:\n" ;
var_dump($incomplete);
var_dump(is_object($incomplete));
var_dump($incomplete->p);

$ref2 = "ref1.original";
$incomplete->p = &$ref2;
var_dump($incomplete->p);
$ref2 = "ref1.changed";
var_dump($incomplete->p);
$incomplete->p = "p.changed";
var_dump($ref1);

var_dump(isset($incomplete->x));
$incomplete->x = "x.new";
var_dump(isset($incomplete->x));
unset($incomplete->x);
var_dump($incomplete->x);

$incomplete->f();

echo "Done";
?>
--EXPECTF--
---> Various types of access on complete class:
object(stdClass)#%d (1) {
  ["p"]=>
  int(1)
}
bool(true)
int(1)
string(13) "ref1.original"
string(12) "ref1.changed"
string(9) "p.changed"
bool(false)
bool(true)

Notice: Undefined property: stdClass::$x in %s on line 32
NULL


---> Same types of access on incomplete class:
object(stdClass)#%d (1) {
  ["p"]=>
  int(1)
}
bool(true)
int(1)
string(13) "ref1.original"
string(12) "ref1.changed"
string(9) "p.changed"
bool(false)
bool(true)

Notice: Undefined property: stdClass::$x in %s on line 52
NULL

Fatal error: Call to undefined method stdClass::f() in %s on line 54
