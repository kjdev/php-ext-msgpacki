--TEST--
msgpacki_filter_append() pre and post
--FILE--
<?php
class filter_1 extends MessagePacki_Filter
{
    public function pre_serialize($in) {
        return $in . '-' . strtolower(__CLASS__);
    }
    public function post_unserialize($in) {
        return $in . '-' . strtoupper(__CLASS__);
    }
}

class filter_2 extends MessagePacki_Filter
{
    public function pre_serialize($in) {
        return $in . '-' . strtolower(__CLASS__);
    }
    public function post_unserialize($in) {
        return $in . '-' . strtoupper(__CLASS__);
    }
}

class filter_3 extends MessagePacki_Filter
{
    public function pre_serialize($in) {
        return $in . '-' . strtolower(__CLASS__);
    }
    public function post_unserialize($in) {
        return $in . '-' . strtoupper(__CLASS__);
    }
}

echo "append 1\n";
msgpacki_filter_register("a", 'filter_1');
msgpacki_filter_append("a");

$value = "Thank you";
$ser = msgpacki_serialize($value);
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));

echo "append 2\n";
msgpacki_filter_register("b", 'filter_2');
msgpacki_filter_append("b");

$ser = msgpacki_serialize($value);
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));

echo "append 3\n";
msgpacki_filter_register("c", 'filter_3');
msgpacki_filter_append("c");

$ser = msgpacki_serialize($value);
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));
?>
--EXPECTF--
append 1
string(38) "b25468616e6b20796f752d66696c7465725f31"
string(27) "Thank you-filter_1-FILTER_1"
append 2
string(56) "bb5468616e6b20796f752d66696c7465725f312d66696c7465725f32"
string(45) "Thank you-filter_1-filter_2-FILTER_2-FILTER_1"
append 3
string(78) "da00245468616e6b20796f752d66696c7465725f312d66696c7465725f322d66696c7465725f33"
string(63) "Thank you-filter_1-filter_2-filter_3-FILTER_3-FILTER_2-FILTER_1"
