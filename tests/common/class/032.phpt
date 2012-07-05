--TEST--
MessagePacki alias check
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

class filter_test1 extends MessagePacki_Filter
{
    public function pre_serialize($in) {}
    public function post_serialize($in) {}
    public function pre_unserialize($in) {}
    public function post_unserialize($in) {}
}

class filter_test2 extends MessagePacki_Filter
{
    public function pre_serialize($in) {}
    public function post_serialize($in) {}
    public function pre_unserialize($in) {}
    public function post_unserialize($in) {}
}

class filter_test3 extends MessagePacki_Filter
{
    public function pre_serialize($in) {}
    public function post_serialize($in) {}
    public function pre_unserialize($in) {}
    public function post_unserialize($in) {}
}

$m = new MessagePacki();

echo "[getMode/setMode]\n";
var_dump($m->getMode());
var_dump($m->setMode(MSGPACKI_MODE_ORIGIN));
var_dump($m->getMode());
var_dump($m->setMode(MSGPACKI_MODE_PHP));
var_dump($m->getMode());

echo "[appendFilter/getFilters]\n";
var_dump($m->appendFilter('filter_test1'));
var_dump($m->appendFilter('filter_test2'));
var_dump($m->getFilters());

echo "[prependFilter/getFilters]\n";
var_dump($m->prependFilter('filter_test3'));
var_dump($m->getFilters());

echo "[removeFilter/getFilters]\n";
var_dump($m->removeFilter('filter_test1'));
var_dump($m->getFilters());

echo "[getFilters]\n";
var_dump($m->getFilters(MSGPACKI_FILTER_PRE_SERIALIZE));
var_dump($m->getFilters(MSGPACKI_FILTER_POST_SERIALIZE));
var_dump($m->getFilters(MSGPACKI_FILTER_PRE_UNSERIALIZE));
var_dump($m->getFilters(MSGPACKI_FILTER_POST_UNSERIALIZE));
?>
===DONE===
--EXPECTF--
[getMode/setMode]
int(2)
bool(true)
int(1)
bool(true)
int(2)
[appendFilter/getFilters]
bool(true)
bool(true)
array(4) {
  ["pre_serialize"]=>
  array(2) {
    [0]=>
    string(12) "filter_test1"
    [1]=>
    string(12) "filter_test2"
  }
  ["post_serialize"]=>
  array(2) {
    [0]=>
    string(12) "filter_test1"
    [1]=>
    string(12) "filter_test2"
  }
  ["pre_unserialize"]=>
  array(2) {
    [0]=>
    string(12) "filter_test1"
    [1]=>
    string(12) "filter_test2"
  }
  ["post_unserialize"]=>
  array(2) {
    [0]=>
    string(12) "filter_test1"
    [1]=>
    string(12) "filter_test2"
  }
}
[prependFilter/getFilters]
bool(true)
array(4) {
  ["pre_serialize"]=>
  array(3) {
    [0]=>
    string(12) "filter_test3"
    [1]=>
    string(12) "filter_test1"
    [2]=>
    string(12) "filter_test2"
  }
  ["post_serialize"]=>
  array(3) {
    [0]=>
    string(12) "filter_test3"
    [1]=>
    string(12) "filter_test1"
    [2]=>
    string(12) "filter_test2"
  }
  ["pre_unserialize"]=>
  array(3) {
    [0]=>
    string(12) "filter_test3"
    [1]=>
    string(12) "filter_test1"
    [2]=>
    string(12) "filter_test2"
  }
  ["post_unserialize"]=>
  array(3) {
    [0]=>
    string(12) "filter_test3"
    [1]=>
    string(12) "filter_test1"
    [2]=>
    string(12) "filter_test2"
  }
}
[removeFilter/getFilters]
bool(true)
array(4) {
  ["pre_serialize"]=>
  array(2) {
    [0]=>
    string(12) "filter_test3"
    [1]=>
    string(12) "filter_test2"
  }
  ["post_serialize"]=>
  array(2) {
    [0]=>
    string(12) "filter_test3"
    [1]=>
    string(12) "filter_test2"
  }
  ["pre_unserialize"]=>
  array(2) {
    [0]=>
    string(12) "filter_test3"
    [1]=>
    string(12) "filter_test2"
  }
  ["post_unserialize"]=>
  array(2) {
    [0]=>
    string(12) "filter_test3"
    [1]=>
    string(12) "filter_test2"
  }
}
[getFilters]
array(2) {
  [0]=>
  string(12) "filter_test3"
  [1]=>
  string(12) "filter_test2"
}
array(2) {
  [0]=>
  string(12) "filter_test3"
  [1]=>
  string(12) "filter_test2"
}
array(2) {
  [0]=>
  string(12) "filter_test3"
  [1]=>
  string(12) "filter_test2"
}
array(2) {
  [0]=>
  string(12) "filter_test3"
  [1]=>
  string(12) "filter_test2"
}
===DONE===
