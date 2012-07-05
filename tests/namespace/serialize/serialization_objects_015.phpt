--TEST--
Object serialization / unserialization: properties reference containing object
--INI--
error_reporting = E_ALL & ~E_STRICT
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

function check(&$obj) {
    var_dump($obj);
    $ser = serialize($obj);
    var_dump(bin2hex($ser));

    $uobj = unserialize($ser);
    var_dump($uobj);
    $uobj->a = "obj->a.changed";
    var_dump($uobj);
    $uobj->b = "obj->b.changed";
    var_dump($uobj);
    $uobj->c = "obj->c.changed";
    var_dump($uobj);
}

echo "\n\n--- a refs container:\n";
$ext = 1;
$obj = new \stdClass;
$obj->a = &$obj;
$obj->b = 1;
$obj->c = 1;
check($obj);

echo "\n\n--- a eqs container:\n";
$ext = 1;
$obj = new \stdClass;
$obj->a = $obj;
$obj->b = 1;
$obj->c = 1;
check($obj);

echo "\n\n--- a,b ref container:\n";
$ext = 1;
$obj = new \stdClass;
$obj->a = &$obj;
$obj->b = &$obj;
$obj->c = 1;
check($obj);

echo "\n\n--- a,b eq container:\n";
$ext = 1;
$obj = new \stdClass;
$obj->a = $obj;
$obj->b = $obj;
$obj->c = 1;
check($obj);

echo "\n\n--- a,b,c ref container:\n";
$ext = 1;
$obj = new \stdClass;
$obj->a = &$obj;
$obj->b = &$obj;
$obj->c = &$obj;
check($obj);

echo "\n\n--- a,b,c eq container:\n";
$ext = 1;
$obj = new \stdClass;
$obj->a = $obj;
$obj->b = $obj;
$obj->c = $obj;
check($obj);

echo "Done";
?>
--EXPECTF--
--- a refs container:
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}
string(48) "84c0a8737464436c617373a16182c0010001a16201a16301"
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  ["a"]=>
  string(14) "obj->a.changed"
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  ["a"]=>
  string(14) "obj->a.changed"
  ["b"]=>
  string(14) "obj->b.changed"
  ["c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  ["a"]=>
  string(14) "obj->a.changed"
  ["b"]=>
  string(14) "obj->b.changed"
  ["c"]=>
  string(14) "obj->c.changed"
}


--- a eqs container:
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}
string(48) "84c0a8737464436c617373a16182c0020001a16201a16301"
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  ["a"]=>
  string(14) "obj->a.changed"
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  ["a"]=>
  string(14) "obj->a.changed"
  ["b"]=>
  string(14) "obj->b.changed"
  ["c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  ["a"]=>
  string(14) "obj->a.changed"
  ["b"]=>
  string(14) "obj->b.changed"
  ["c"]=>
  string(14) "obj->c.changed"
}


--- a,b ref container:
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  *RECURSION*
  ["c"]=>
  int(1)
}
string(56) "84c0a8737464436c617373a16182c0010001a16282c0010001a16301"
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  *RECURSION*
  ["c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  ["a"]=>
  &string(14) "obj->a.changed"
  ["b"]=>
  &string(14) "obj->a.changed"
  ["c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  ["a"]=>
  &string(14) "obj->b.changed"
  ["b"]=>
  &string(14) "obj->b.changed"
  ["c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  ["a"]=>
  &string(14) "obj->b.changed"
  ["b"]=>
  &string(14) "obj->b.changed"
  ["c"]=>
  string(14) "obj->c.changed"
}


--- a,b eq container:
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  *RECURSION*
  ["c"]=>
  int(1)
}
string(56) "84c0a8737464436c617373a16182c0020001a16282c0020001a16301"
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  *RECURSION*
  ["c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  ["a"]=>
  string(14) "obj->a.changed"
  ["b"]=>
  *RECURSION*
  ["c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  ["a"]=>
  string(14) "obj->a.changed"
  ["b"]=>
  string(14) "obj->b.changed"
  ["c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  ["a"]=>
  string(14) "obj->a.changed"
  ["b"]=>
  string(14) "obj->b.changed"
  ["c"]=>
  string(14) "obj->c.changed"
}


--- a,b,c ref container:
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  *RECURSION*
  ["c"]=>
  *RECURSION*
}
string(64) "84c0a8737464436c617373a16182c0010001a16282c0010001a16382c0010001"
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  *RECURSION*
  ["c"]=>
  *RECURSION*
}
object(stdClass)#%d (3) {
  ["a"]=>
  &string(14) "obj->a.changed"
  ["b"]=>
  &string(14) "obj->a.changed"
  ["c"]=>
  &string(14) "obj->a.changed"
}
object(stdClass)#%d (3) {
  ["a"]=>
  &string(14) "obj->b.changed"
  ["b"]=>
  &string(14) "obj->b.changed"
  ["c"]=>
  &string(14) "obj->b.changed"
}
object(stdClass)#%d (3) {
  ["a"]=>
  &string(14) "obj->c.changed"
  ["b"]=>
  &string(14) "obj->c.changed"
  ["c"]=>
  &string(14) "obj->c.changed"
}


--- a,b,c eq container:
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  *RECURSION*
  ["c"]=>
  *RECURSION*
}
string(64) "84c0a8737464436c617373a16182c0020001a16282c0020001a16382c0020001"
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  *RECURSION*
  ["c"]=>
  *RECURSION*
}
object(stdClass)#%d (3) {
  ["a"]=>
  string(14) "obj->a.changed"
  ["b"]=>
  *RECURSION*
  ["c"]=>
  *RECURSION*
}
object(stdClass)#%d (3) {
  ["a"]=>
  string(14) "obj->a.changed"
  ["b"]=>
  string(14) "obj->b.changed"
  ["c"]=>
  *RECURSION*
}
object(stdClass)#%d (3) {
  ["a"]=>
  string(14) "obj->a.changed"
  ["b"]=>
  string(14) "obj->b.changed"
  ["c"]=>
  string(14) "obj->c.changed"
}
Done
