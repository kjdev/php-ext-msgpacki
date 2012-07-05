--TEST--
filter_append() duplicate and none
--FILE--
<?php
namespace MessagePacki;

class filter_test extends Filter
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

var_dump(filter_register("test", '\MessagePacki\filter_test'));

var_dump(filter_append("test"));
var_dump(filter_append("test"));
var_dump(filter_append("test"));

var_dump(filter_append("test1"));
var_dump(filter_append("test2"));
var_dump(filter_append("test3"));

var_dump(get_filters());

$ser = serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(unserialize($ser));

?>
--EXPECTF--
bool(true)
bool(true)

Warning: MessagePack filter_append: "test" filter already exsists in %s on line %d
bool(false)

Warning: MessagePack filter_append: "test" filter already exsists in %s on line %d
bool(false)

Warning: MessagePack filter_append: No such find filter: "test1" in %s on line %d
bool(false)

Warning: MessagePack filter_append: No such find filter: "test2" in %s on line %d
bool(false)

Warning: MessagePack filter_append: No such find filter: "test3" in %s on line %d
bool(false)
array(5) {
  ["registers"]=>
  array(1) {
    [0]=>
    string(4) "test"
  }
  ["pre_serialize"]=>
  array(1) {
    [0]=>
    string(4) "test"
  }
  ["post_serialize"]=>
  array(1) {
    [0]=>
    string(4) "test"
  }
  ["pre_unserialize"]=>
  array(1) {
    [0]=>
    string(4) "test"
  }
  ["post_unserialize"]=>
  array(1) {
    [0]=>
    string(4) "test"
  }
}
string(39) "MessagePacki\filter_test::pre_serialize"
string(40) "MessagePacki\filter_test::post_serialize"
string(20) "a95468616e6b20796f75"
string(41) "MessagePacki\filter_test::pre_unserialize"
string(42) "MessagePacki\filter_test::post_unserialize"
string(9) "Thank you"
