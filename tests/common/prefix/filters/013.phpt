--TEST--
msgpacki_filter_append() duplicate and none
--FILE--
<?php


class filter_test extends MessagePacki_Filter
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

var_dump(msgpacki_filter_register("test", 'filter_test'));

var_dump(msgpacki_filter_append("test"));
var_dump(msgpacki_filter_append("test"));
var_dump(msgpacki_filter_append("test"));

var_dump(msgpacki_filter_append("test1"));
var_dump(msgpacki_filter_append("test2"));
var_dump(msgpacki_filter_append("test3"));

var_dump(msgpacki_get_filters());

$ser = msgpacki_serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));

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
string(26) "filter_test::pre_serialize"
string(27) "filter_test::post_serialize"
string(20) "a95468616e6b20796f75"
string(28) "filter_test::pre_unserialize"
string(29) "filter_test::post_unserialize"
string(9) "Thank you"
