--TEST--
filter_register() duplicate key
--FILE--
<?php
namespace MessagePacki;

class filter_1 extends Filter
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

class filter_2 extends Filter
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

class filter_3 extends Filter
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

var_dump(filter_register("a", '\MessagePacki\filter_1'));
var_dump(filter_register("a", '\MessagePacki\filter_2'));
var_dump(filter_register("a", '\MessagePacki\filter_3'));

filter_append("a");

$ser = serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(unserialize($ser));
?>
--EXPECTF--
bool(true)

Warning: MessagePack filter_register: "a" filter already exsists in %s on line %d
bool(false)

Warning: MessagePack filter_register: "a" filter already exsists in %s on line %d
bool(false)
string(36) "MessagePacki\filter_1::pre_serialize"
string(37) "MessagePacki\filter_1::post_serialize"
string(20) "a95468616e6b20796f75"
string(38) "MessagePacki\filter_1::pre_unserialize"
string(39) "MessagePacki\filter_1::post_unserialize"
string(9) "Thank you"
