--TEST--
Object serialization / unserialization: references amongst properties
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

echo "\n\n--- a refs b:\n";
$obj = new stdClass;
$obj->a = &$obj->b;
$obj->b = 1;
$obj->c = 1;
check($obj);

echo "\n\n--- a refs c:\n";
$obj = new stdClass;
$obj->a = &$obj->c;
$obj->b = 1;
$obj->c = 1;
check($obj);

echo "\n\n--- b refs a:\n";
$obj = new stdClass;
$obj->a = 1;
$obj->b = &$obj->a;
$obj->c = 1;
check($obj);

echo "\n\n--- b refs c:\n";
$obj = new stdClass;
$obj->a = 1;
$obj->b = &$obj->c;
$obj->c = 1;
check($obj);

echo "\n\n--- c refs a:\n";
$obj = new stdClass;
$obj->a = 1;
$obj->b = 1;
$obj->c = &$obj->a;
check($obj);

echo "\n\n--- c refs b:\n";
$obj = new stdClass;
$obj->a = 1;
$obj->b = 1;
$obj->c = &$obj->b;
check($obj);

echo "\n\n--- a,b refs c:\n";
$obj = new stdClass;
$obj->a = &$obj->c;
$obj->b = &$obj->c;
$obj->c = 1;
check($obj);

echo "\n\n--- a,c refs b:\n";
$obj = new stdClass;
$obj->a = &$obj->b;
$obj->b = 1;
$obj->c = &$obj->b;
check($obj);

echo "\n\n--- b,c refs a:\n";
$obj = new stdClass;
$obj->a = 1;
$obj->b = &$obj->a;
$obj->c = &$obj->a;
check($obj);

echo "Done";
?>
--EXPECTF--

--- a refs b:
object(stdClass)#%d (3) {
  ["b"]=>
  &int(1)
  ["a"]=>
  &int(1)
  ["c"]=>
  int(1)
}
string(20) "83a16201a16101a16301"
array(3) {
  ["b"]=>
  int(1)
  ["a"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["b"]=>
  int(1)
  ["a"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["b"]=>
  int(1)
  ["a"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["b"]=>
  int(1)
  ["a"]=>
  int(1)
  ["c"]=>
  int(1)
}


--- a refs c:
object(stdClass)#%d (3) {
  ["c"]=>
  &int(1)
  ["a"]=>
  &int(1)
  ["b"]=>
  int(1)
}
string(20) "83a16301a16101a16201"
array(3) {
  ["c"]=>
  int(1)
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["c"]=>
  int(1)
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["c"]=>
  int(1)
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["c"]=>
  int(1)
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
}


--- b refs a:
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


--- b refs c:
object(stdClass)#%d (3) {
  ["a"]=>
  int(1)
  ["c"]=>
  &int(1)
  ["b"]=>
  &int(1)
}
string(20) "83a16101a16301a16201"
array(3) {
  ["a"]=>
  int(1)
  ["c"]=>
  int(1)
  ["b"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["c"]=>
  int(1)
  ["b"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["c"]=>
  int(1)
  ["b"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  int(1)
  ["c"]=>
  int(1)
  ["b"]=>
  int(1)
}


--- c refs a:
object(stdClass)#%d (3) {
  ["a"]=>
  &int(1)
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


--- c refs b:
object(stdClass)#%d (3) {
  ["a"]=>
  int(1)
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


--- a,b refs c:
object(stdClass)#%d (3) {
  ["c"]=>
  &int(1)
  ["a"]=>
  &int(1)
  ["b"]=>
  &int(1)
}
string(20) "83a16301a16101a16201"
array(3) {
  ["c"]=>
  int(1)
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["c"]=>
  int(1)
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["c"]=>
  int(1)
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["c"]=>
  int(1)
  ["a"]=>
  int(1)
  ["b"]=>
  int(1)
}


--- a,c refs b:
object(stdClass)#%d (3) {
  ["b"]=>
  &int(1)
  ["a"]=>
  &int(1)
  ["c"]=>
  &int(1)
}
string(20) "83a16201a16101a16301"
array(3) {
  ["b"]=>
  int(1)
  ["a"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["b"]=>
  int(1)
  ["a"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["b"]=>
  int(1)
  ["a"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["b"]=>
  int(1)
  ["a"]=>
  int(1)
  ["c"]=>
  int(1)
}


--- b,c refs a:
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
