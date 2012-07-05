--TEST--
class Filter extends
--FILE--
<?php

class filter_base extends MessagePacki_Filter
{
    public function pre_serialize($in) {
        var_dump(__METHOD__);
        return $in;
    }
    public function post_serialize($in) {
        var_dump(__METHOD__);
        return $in;
    }
    public function pre_unserialize($in) {
        var_dump(__METHOD__);
        return $in;
    }
    public function post_unserialize($in) {
        var_dump(__METHOD__);
        return $in;
    }
}

class filter_1 extends filter_base {}
class filter_2 extends filter_base {}
class filter_3 extends filter_base {}

$m = new MessagePacki();

var_dump($m->append_filter('filter_1'));
var_dump($m->append_filter('filter_2'));
var_dump($m->append_filter('filter_3'));

var_dump($m->get_filters());

$ser = $m->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
array(4) {
  ["pre_serialize"]=>
  array(3) {
    [0]=>
    string(8) "filter_1"
    [1]=>
    string(8) "filter_2"
    [2]=>
    string(8) "filter_3"
  }
  ["post_serialize"]=>
  array(3) {
    [0]=>
    string(8) "filter_1"
    [1]=>
    string(8) "filter_2"
    [2]=>
    string(8) "filter_3"
  }
  ["pre_unserialize"]=>
  array(3) {
    [0]=>
    string(8) "filter_1"
    [1]=>
    string(8) "filter_2"
    [2]=>
    string(8) "filter_3"
  }
  ["post_unserialize"]=>
  array(3) {
    [0]=>
    string(8) "filter_1"
    [1]=>
    string(8) "filter_2"
    [2]=>
    string(8) "filter_3"
  }
}
string(26) "filter_base::pre_serialize"
string(26) "filter_base::pre_serialize"
string(26) "filter_base::pre_serialize"
string(27) "filter_base::post_serialize"
string(27) "filter_base::post_serialize"
string(27) "filter_base::post_serialize"
string(20) "a95468616e6b20796f75"
string(28) "filter_base::pre_unserialize"
string(28) "filter_base::pre_unserialize"
string(28) "filter_base::pre_unserialize"
string(29) "filter_base::post_unserialize"
string(29) "filter_base::post_unserialize"
string(29) "filter_base::post_unserialize"
string(9) "Thank you"
