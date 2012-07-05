--TEST--
msgpacki_filter_remove() and serialize()/unserialize()
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

msgpacki_filter_register("a", 'filter_1');
msgpacki_filter_register("b", 'filter_2');
msgpacki_filter_register("c", 'filter_3');

msgpacki_filter_append("a");
msgpacki_filter_append("b");
msgpacki_filter_append("c");

echo "== filter a, b, c ==\n";
$ser = msgpacki_serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));

echo "== filter a, c ==\n";
var_dump(msgpacki_filter_remove("b"));
$ser = msgpacki_serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));

echo "== filter c ==\n";
var_dump(msgpacki_filter_remove("a"));
$ser = msgpacki_serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));

echo "== filter non ==\n";
var_dump(msgpacki_filter_remove("c"));
$ser = msgpacki_serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));

echo "== filter append b ==\n";
var_dump(msgpacki_filter_append("b"));
$ser = msgpacki_serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));

?>
--EXPECTF--
== filter a, b, c ==
string(23) "filter_1::pre_serialize"
string(23) "filter_2::pre_serialize"
string(23) "filter_3::pre_serialize"
string(24) "filter_1::post_serialize"
string(24) "filter_2::post_serialize"
string(24) "filter_3::post_serialize"
string(20) "a95468616e6b20796f75"
string(25) "filter_3::pre_unserialize"
string(25) "filter_2::pre_unserialize"
string(25) "filter_1::pre_unserialize"
string(26) "filter_3::post_unserialize"
string(26) "filter_2::post_unserialize"
string(26) "filter_1::post_unserialize"
string(9) "Thank you"
== filter a, c ==
bool(true)
string(23) "filter_1::pre_serialize"
string(23) "filter_3::pre_serialize"
string(24) "filter_1::post_serialize"
string(24) "filter_3::post_serialize"
string(20) "a95468616e6b20796f75"
string(25) "filter_3::pre_unserialize"
string(25) "filter_1::pre_unserialize"
string(26) "filter_3::post_unserialize"
string(26) "filter_1::post_unserialize"
string(9) "Thank you"
== filter c ==
bool(true)
string(23) "filter_3::pre_serialize"
string(24) "filter_3::post_serialize"
string(20) "a95468616e6b20796f75"
string(25) "filter_3::pre_unserialize"
string(26) "filter_3::post_unserialize"
string(9) "Thank you"
== filter non ==
bool(true)
string(20) "a95468616e6b20796f75"
string(9) "Thank you"
== filter append b ==
bool(true)
string(23) "filter_2::pre_serialize"
string(24) "filter_2::post_serialize"
string(20) "a95468616e6b20796f75"
string(25) "filter_2::pre_unserialize"
string(26) "filter_2::post_unserialize"
string(9) "Thank you"
