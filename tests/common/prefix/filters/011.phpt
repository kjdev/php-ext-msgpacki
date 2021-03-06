--TEST--
msgpacki_filter_register() duplicate key
--FILE--
<?php


class filter_1 extends MessagePacki_Filter
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

class filter_2 extends MessagePacki_Filter
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

class filter_3 extends MessagePacki_Filter
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

var_dump(msgpacki_filter_register("a", 'filter_1'));
var_dump(msgpacki_filter_register("a", 'filter_2'));
var_dump(msgpacki_filter_register("a", 'filter_3'));

msgpacki_filter_append("a");

$ser = msgpacki_serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));
?>
--EXPECTF--
bool(true)

Warning: MessagePack filter_register: "a" filter already exsists in %s on line %d
bool(false)

Warning: MessagePack filter_register: "a" filter already exsists in %s on line %d
bool(false)
string(23) "filter_1::pre_serialize"
string(24) "filter_1::post_serialize"
string(20) "a95468616e6b20796f75"
string(25) "filter_1::pre_unserialize"
string(26) "filter_1::post_unserialize"
string(9) "Thank you"
