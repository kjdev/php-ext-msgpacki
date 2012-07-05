--TEST--
MessagePacki::remove_filter() and MessagePacki::pack()/MessagePacki::unpack()
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

$m->append_filter('filter_1');
$m->append_filter('filter_2');
$m->append_filter('filter_3');

echo "== filter 1, 2, 3 ==\n";
$ser = $m->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));

echo "== filter 1, 3 ==\n";
var_dump($m->remove_filter("filter_2"));
$ser = $m->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));

echo "== filter 3 ==\n";
var_dump($m->remove_filter("filter_1"));
$ser = $m->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));

echo "== filter non ==\n";
var_dump($m->remove_filter("filter_3"));
$ser = $m->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));

echo "== filter append 2 ==\n";
var_dump($m->append_filter('filter_2'));
$ser = $m->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));

?>
--EXPECTF--
== filter 1, 2, 3 ==
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
== filter 1, 3 ==
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
== filter 3 ==
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
== filter append 2 ==
bool(true)
string(23) "filter_2::pre_serialize"
string(24) "filter_2::post_serialize"
string(20) "a95468616e6b20796f75"
string(25) "filter_2::pre_unserialize"
string(26) "filter_2::post_unserialize"
string(9) "Thank you"
