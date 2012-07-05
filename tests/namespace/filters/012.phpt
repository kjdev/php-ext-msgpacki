--TEST--
class Filter extends
--FILE--
<?php
namespace MessagePacki;

class filter_base extends Filter
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

var_dump(filter_register("a", '\MessagePacki\filter_1'));
var_dump(filter_register("b", '\MessagePacki\filter_2'));
var_dump(filter_register("c", '\MessagePacki\filter_3'));

var_dump(filter_append("a"));
var_dump(filter_append("b"));
var_dump(filter_append("c"));

var_dump(get_filters());

$ser = serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(unserialize($ser));

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
array(5) {
  ["registers"]=>
  array(3) {
    [0]=>
    string(1) "a"
    [1]=>
    string(1) "b"
    [2]=>
    string(1) "c"
  }
  ["pre_serialize"]=>
  array(3) {
    [0]=>
    string(1) "a"
    [1]=>
    string(1) "b"
    [2]=>
    string(1) "c"
  }
  ["post_serialize"]=>
  array(3) {
    [0]=>
    string(1) "a"
    [1]=>
    string(1) "b"
    [2]=>
    string(1) "c"
  }
  ["pre_unserialize"]=>
  array(3) {
    [0]=>
    string(1) "a"
    [1]=>
    string(1) "b"
    [2]=>
    string(1) "c"
  }
  ["post_unserialize"]=>
  array(3) {
    [0]=>
    string(1) "a"
    [1]=>
    string(1) "b"
    [2]=>
    string(1) "c"
  }
}
string(39) "MessagePacki\filter_base::pre_serialize"
string(39) "MessagePacki\filter_base::pre_serialize"
string(39) "MessagePacki\filter_base::pre_serialize"
string(40) "MessagePacki\filter_base::post_serialize"
string(40) "MessagePacki\filter_base::post_serialize"
string(40) "MessagePacki\filter_base::post_serialize"
string(20) "a95468616e6b20796f75"
string(41) "MessagePacki\filter_base::pre_unserialize"
string(41) "MessagePacki\filter_base::pre_unserialize"
string(41) "MessagePacki\filter_base::pre_unserialize"
string(42) "MessagePacki\filter_base::post_unserialize"
string(42) "MessagePacki\filter_base::post_unserialize"
string(42) "MessagePacki\filter_base::post_unserialize"
string(9) "Thank you"
