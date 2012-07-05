--TEST--
MessagePacki clone mode
--FILE--
<?php

$m1 = new MessagePacki();
$m2 = new MessagePacki(MSGPACKI_MODE_ORIGIN);

$c1 = clone $m1;
$c2 = clone $m2;

function check ($ins) {
    $obj = new stdClass();
    $obj->test = "TEST";
    $ser = $ins->pack($obj);
    var_dump(bin2hex($ser));
    var_dump($ins->unpack($ser));
}

echo "[mode]\n";
var_dump($m1->get_mode());
var_dump($m2->get_mode());
var_dump($c1->get_mode());
var_dump($c2->get_mode());

check($m1);
check($m2);
check($c1);
check($c2);

echo "[clone set_mode]\n";
$c1->set_mode(MSGPACKI_MODE_ORIGIN);
var_dump($m1->get_mode());
var_dump($m2->get_mode());
var_dump($c1->get_mode());
var_dump($c2->get_mode());

check($m1);
check($m2);
check($c1);
check($c2);

echo "[clone unset]\n";
unset($c2);
var_dump($m1->get_mode());
var_dump($m2->get_mode());
var_dump($c1->get_mode());

check($m1);
check($m2);
check($c1);
?>
--EXPECTF--
[mode]
int(2)
int(1)
int(2)
int(1)
string(42) "82c0a8737464436c617373a474657374a454455354"
object(stdClass)#%d (1) {
  ["test"]=>
  string(4) "TEST"
}
string(22) "81a474657374a454455354"
array(1) {
  ["test"]=>
  string(4) "TEST"
}
string(42) "82c0a8737464436c617373a474657374a454455354"
object(stdClass)#%d (1) {
  ["test"]=>
  string(4) "TEST"
}
string(22) "81a474657374a454455354"
array(1) {
  ["test"]=>
  string(4) "TEST"
}
[clone set_mode]
int(2)
int(1)
int(1)
int(1)
string(42) "82c0a8737464436c617373a474657374a454455354"
object(stdClass)#%d (1) {
  ["test"]=>
  string(4) "TEST"
}
string(22) "81a474657374a454455354"
array(1) {
  ["test"]=>
  string(4) "TEST"
}
string(22) "81a474657374a454455354"
array(1) {
  ["test"]=>
  string(4) "TEST"
}
string(22) "81a474657374a454455354"
array(1) {
  ["test"]=>
  string(4) "TEST"
}
[clone unset]
int(2)
int(1)
int(1)
string(42) "82c0a8737464436c617373a474657374a454455354"
object(stdClass)#5 (1) {
  ["test"]=>
  string(4) "TEST"
}
string(22) "81a474657374a454455354"
array(1) {
  ["test"]=>
  string(4) "TEST"
}
string(22) "81a474657374a454455354"
array(1) {
  ["test"]=>
  string(4) "TEST"
}
