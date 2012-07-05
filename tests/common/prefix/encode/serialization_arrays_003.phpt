--TEST--
serialization: arrays with references to an external variable
--FILE--
<?php


if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

function check(&$a) {
    var_dump($a);
    $ser = msgpacki_encode($a);
    var_dump(bin2hex($ser));

    $b = msgpacki_decode($ser);
    var_dump($b);
    $b[0] = "b0.changed";
    var_dump($b);
    $b[1] = "b1.changed";
    var_dump($b);
    $b[2] = "b2.changed";
    var_dump($b);
}

echo "\n\n--- 0 refs external:\n";
$ext = 1;
$a = array();
$a[0] = &$ext;
$a[1] = 1;
$a[2] = 1;
check($a);

echo "\n\n--- 1 refs external:\n";
$ext = 1;
$a = array();
$a[0] = 1;
$a[1] = &$ext;
$a[2] = 1;
check($a);

echo "\n\n--- 2 refs external:\n";
$ext = 1;
$a = array();
$a[0] = 1;
$a[1] = 1;
$a[2] = &$ext;
check($a);

echo "\n\n--- 1,2 ref external:\n";
$ext = 1;
$a = array();
$a[0] = &$ext;
$a[1] = &$ext;
$a[2] = 1;
check($a);

echo "\n\n--- 1,2,3 ref external:\n";
$ext = 1;
$a = array();
$a[0] = &$ext;
$a[1] = &$ext;
$a[2] = &$ext;
check($a);

echo "Done";
?>
--EXPECTF--


--- 0 refs external:
array(3) {
  [0]=>
  &int(1)
  [1]=>
  int(1)
  [2]=>
  int(1)
}
string(8) "93010101"
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(1)
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  int(1)
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  string(10) "b2.changed"
}


--- 1 refs external:
array(3) {
  [0]=>
  int(1)
  [1]=>
  &int(1)
  [2]=>
  int(1)
}
string(8) "93010101"
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(1)
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  int(1)
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  string(10) "b2.changed"
}


--- 2 refs external:
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(1)
  [2]=>
  &int(1)
}
string(8) "93010101"
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(1)
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  int(1)
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  string(10) "b2.changed"
}


--- 1,2 ref external:
array(3) {
  [0]=>
  &int(1)
  [1]=>
  &int(1)
  [2]=>
  int(1)
}
string(8) "93010101"
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(1)
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  int(1)
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  string(10) "b2.changed"
}


--- 1,2,3 ref external:
array(3) {
  [0]=>
  &int(1)
  [1]=>
  &int(1)
  [2]=>
  &int(1)
}
string(8) "93010101"
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(1)
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  int(1)
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  string(10) "b2.changed"
}
Done
