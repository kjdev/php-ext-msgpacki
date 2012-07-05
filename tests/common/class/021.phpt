--TEST--
MessagePacki::append_filter() called
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

$m = new MessagePacki();

echo "append 1\n";
$m->append_filter("filter_1");

$ser = $m->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));

echo "append 2\n";
$m->append_filter("filter_2");

$ser = $m->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));

echo "append 3\n";
$m->append_filter("filter_3");

$ser = $m->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));
?>
--EXPECTF--
append 1
string(23) "filter_1::pre_serialize"
string(24) "filter_1::post_serialize"
string(20) "a95468616e6b20796f75"
string(25) "filter_1::pre_unserialize"
string(26) "filter_1::post_unserialize"
string(9) "Thank you"
append 2
string(23) "filter_1::pre_serialize"
string(23) "filter_2::pre_serialize"
string(24) "filter_1::post_serialize"
string(24) "filter_2::post_serialize"
string(20) "a95468616e6b20796f75"
string(25) "filter_2::pre_unserialize"
string(25) "filter_1::pre_unserialize"
string(26) "filter_2::post_unserialize"
string(26) "filter_1::post_unserialize"
string(9) "Thank you"
append 3
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
