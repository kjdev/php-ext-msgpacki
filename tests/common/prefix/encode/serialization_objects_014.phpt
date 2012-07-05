--TEST--
Object serialization / unserialization: references to external values
--INI--
error_reporting = E_ALL & ~E_STRICT
--FILE--
<?php


if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

function check(&$obj) {
    var_dump($obj);
    $ser = msgpacki_encode($obj);
    var_dump(bin2hex($ser));

    $uobj = msgpacki_decode($ser);
    var_dump($uobj);
    $uobj->a = "obj->a.changed";
    var_dump($uobj);
    $uobj->b = "obj->b.changed";
    var_dump($uobj);
    $uobj->c = "obj->c.changed";
    var_dump($uobj);
}

echo "\n\n--- a refs external:\n";
$ext = 1;
$obj = new stdClass;
$obj->a = &$ext;
$obj->b = 1;
$obj->c = 1;
check($obj);

echo "\n\n--- b refs external:\n";
$ext = 1;
$obj = new stdClass;
$obj->a = 1;
$obj->b = &$ext;
$obj->c = 1;
check($obj);

echo "\n\n--- c refs external:\n";
$ext = 1;
$obj = new stdClass;
$obj->a = 1;
$obj->b = 1;
$obj->c = &$ext;
check($obj);

echo "\n\n--- a,b ref external:\n";
$ext = 1;
$obj = new stdClass;
$obj->a = &$ext;
$obj->b = &$ext;
$obj->c = 1;
check($obj);

echo "\n\n--- a,b,c ref external:\n";
$ext = 1;
$obj = new stdClass;
$obj->a = &$ext;
$obj->b = &$ext;
$obj->c = &$ext;
check($obj);

echo "Done";
?>
--EXPECTF--

--- a refs external:
object(stdClass)#%d (3) {
  ["a"]=>
  &int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}
string(20) "83a16101a16201a16301"
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}


--- b refs external:
object(stdClass)#%d (3) {
  ["a"]=>
  int(1)
  ["b"]=>
  &int(1)
  ["c"]=>
  int(1)
}
string(20) "83a16101a16201a16301"
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}


--- c refs external:
object(stdClass)#%d (3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  &int(1)
}
string(20) "83a16101a16201a16301"
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}


--- a,b ref external:
object(stdClass)#%d (3) {
  ["a"]=>
  &int(1)
  ["b"]=>
  &int(1)
  ["c"]=>
  int(1)
}
string(20) "83a16101a16201a16301"
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}


--- a,b,c ref external:
object(stdClass)#%d (3) {
  ["a"]=>
  &int(1)
  ["b"]=>
  &int(1)
  ["c"]=>
  &int(1)
}
string(20) "83a16101a16201a16301"
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}
Done
