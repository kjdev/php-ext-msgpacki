--TEST--
MessagePacki filter class none
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

$m = new MessagePacki();

var_dump($m->append_filter('filter_test1'));
var_dump($m->append_filter('filter_test2'));
var_dump($m->append_filter('filter_test3'));

var_dump($m->get_filters());

$ser = $m->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));

?>
--EXPECTF--
Warning: MessagePacki::append_filter(): MessagePack filter "filter_test1" requires class "filter_test1", but that class is not defined in %s on line %d
bool(false)

Warning: MessagePacki::append_filter(): MessagePack filter "filter_test2" requires class "filter_test2", but that class is not defined in %s on line %d
bool(false)

Warning: MessagePacki::append_filter(): MessagePack filter "filter_test3" requires class "filter_test3", but that class is not defined in %s on line %d
bool(false)
array(0) {
}
string(20) "a95468616e6b20796f75"
string(9) "Thank you"
