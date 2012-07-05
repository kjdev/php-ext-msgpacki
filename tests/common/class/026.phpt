--TEST--
MessagePacki filter duplicate class
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

$m = new MessagePacki();

var_dump($m->append_filter('filter_1'));
var_dump($m->append_filter('filter_1'));
var_dump($m->append_filter('filter_1'));

$ser = $m->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));
?>
--EXPECTF--
bool(true)

Warning: MessagePacki::append_filter(): "filter_1" filter already exsists in %s on line %d
bool(false)

Warning: MessagePacki::append_filter(): "filter_1" filter already exsists in %s on line %d
bool(false)
string(23) "filter_1::pre_serialize"
string(24) "filter_1::post_serialize"
string(20) "a95468616e6b20796f75"
string(25) "filter_1::pre_unserialize"
string(26) "filter_1::post_unserialize"
string(9) "Thank you"
