--TEST--
filter_append() called
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

echo "append 1\n";
filter_register("a", '\MessagePacki\filter_1');
filter_append("a");

$ser = serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(unserialize($ser));

echo "append 2\n";
filter_register("b", '\MessagePacki\filter_2');
filter_append("b");

$ser = serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(unserialize($ser));

echo "append 3\n";
filter_register("c", '\MessagePacki\filter_3');
filter_append("c");

$ser = serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(unserialize($ser));
?>
--EXPECTF--
append 1
string(36) "MessagePacki\filter_1::pre_serialize"
string(37) "MessagePacki\filter_1::post_serialize"
string(20) "a95468616e6b20796f75"
string(38) "MessagePacki\filter_1::pre_unserialize"
string(39) "MessagePacki\filter_1::post_unserialize"
string(9) "Thank you"
append 2
string(36) "MessagePacki\filter_1::pre_serialize"
string(36) "MessagePacki\filter_2::pre_serialize"
string(37) "MessagePacki\filter_1::post_serialize"
string(37) "MessagePacki\filter_2::post_serialize"
string(20) "a95468616e6b20796f75"
string(38) "MessagePacki\filter_2::pre_unserialize"
string(38) "MessagePacki\filter_1::pre_unserialize"
string(39) "MessagePacki\filter_2::post_unserialize"
string(39) "MessagePacki\filter_1::post_unserialize"
string(9) "Thank you"
append 3
string(36) "MessagePacki\filter_1::pre_serialize"
string(36) "MessagePacki\filter_2::pre_serialize"
string(36) "MessagePacki\filter_3::pre_serialize"
string(37) "MessagePacki\filter_1::post_serialize"
string(37) "MessagePacki\filter_2::post_serialize"
string(37) "MessagePacki\filter_3::post_serialize"
string(20) "a95468616e6b20796f75"
string(38) "MessagePacki\filter_3::pre_unserialize"
string(38) "MessagePacki\filter_2::pre_unserialize"
string(38) "MessagePacki\filter_1::pre_unserialize"
string(39) "MessagePacki\filter_3::post_unserialize"
string(39) "MessagePacki\filter_2::post_unserialize"
string(39) "MessagePacki\filter_1::post_unserialize"
string(9) "Thank you"
