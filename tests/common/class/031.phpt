--TEST--
MessagePacki clone filter
--FILE--
<?php

class filter_test1 extends MessagePacki_Filter
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

class filter_test2 extends MessagePacki_Filter
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

class filter_test3 extends MessagePacki_Filter
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

$m1 = new MessagePacki();
$m2 = new MessagePacki();

echo "[m1 append filter 1, 2]\n";
var_dump($m1->append_filter('filter_test1'));
var_dump($m1->append_filter('filter_test2'));

echo "[m2 append filter_2]\n";
var_dump($m2->append_filter('filter_test2'));

$c1 = clone $m1;
$c2 = clone $m2;

function check ($name, $ins) {
    echo "-$name-\n";
    $ser = $ins->pack("Thank you");
    var_dump(bin2hex($ser));
    var_dump($ins->unpack($ser));
}

echo "[mode]\n";
check("origin1(1,2)", $m1);
check("origin2(2)", $m2);
check("clone1(1,2)", $c1);
check("clone2(2)", $c2);

echo "[clone1 append filter 3]\n";
var_dump($c1->append_filter('filter_test3'));
check("origin1(1,2)", $m1);
check("origin2(2)", $m2);
check("clone1(1,2,3)", $c1);
check("clone2(2)", $c2);

echo "[origin2 prepend filter 3]\n";
var_dump($m2->prepend_filter('filter_test3'));
check("origin1(1,2)", $m1);
check("origin2(3,2)", $m2);
check("clone1(1,2,3)", $c1);
check("clone2(2)", $c2);

echo "[clone1 remove filter 2]\n";
var_dump($c1->remove_filter('filter_test2'));
check("origin1(1,2)", $m1);
check("origin2(3,2)", $m2);
check("clone1(1,3)", $c1);
check("clone2(2)", $c2);

echo "[origin1 remove filter 2]\n";
var_dump($m1->remove_filter('filter_test2'));
check("origin1(1)", $m1);
check("origin2(3,2)", $m2);
check("clone1(1,3)", $c1);
check("clone2(2)", $c2);

echo "[unset clone2]\n";
unset($c2);
check("origin1(1)", $m1);
check("origin2(3,2)", $m2);
check("clone1(1,3)", $c1);

echo "[unset origin1]\n";
unset($m1);
check("origin2(3,2)", $m2);
check("clone1(1,3)", $c1);
?>
--EXPECTF--
[m1 append filter 1, 2]
bool(true)
bool(true)
[m2 append filter_2]
bool(true)
[mode]
-origin1(1,2)-
string(27) "filter_test1::pre_serialize"
string(27) "filter_test2::pre_serialize"
string(28) "filter_test1::post_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(29) "filter_test1::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(30) "filter_test1::post_unserialize"
string(9) "Thank you"
-origin2(2)-
string(27) "filter_test2::pre_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(9) "Thank you"
-clone1(1,2)-
string(27) "filter_test1::pre_serialize"
string(27) "filter_test2::pre_serialize"
string(28) "filter_test1::post_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(29) "filter_test1::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(30) "filter_test1::post_unserialize"
string(9) "Thank you"
-clone2(2)-
string(27) "filter_test2::pre_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(9) "Thank you"
[clone1 append filter 3]
bool(true)
-origin1(1,2)-
string(27) "filter_test1::pre_serialize"
string(27) "filter_test2::pre_serialize"
string(28) "filter_test1::post_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(29) "filter_test1::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(30) "filter_test1::post_unserialize"
string(9) "Thank you"
-origin2(2)-
string(27) "filter_test2::pre_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(9) "Thank you"
-clone1(1,2,3)-
string(27) "filter_test1::pre_serialize"
string(27) "filter_test2::pre_serialize"
string(27) "filter_test3::pre_serialize"
string(28) "filter_test1::post_serialize"
string(28) "filter_test2::post_serialize"
string(28) "filter_test3::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test3::pre_unserialize"
string(29) "filter_test2::pre_unserialize"
string(29) "filter_test1::pre_unserialize"
string(30) "filter_test3::post_unserialize"
string(30) "filter_test2::post_unserialize"
string(30) "filter_test1::post_unserialize"
string(9) "Thank you"
-clone2(2)-
string(27) "filter_test2::pre_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(9) "Thank you"
[origin2 prepend filter 3]
bool(true)
-origin1(1,2)-
string(27) "filter_test1::pre_serialize"
string(27) "filter_test2::pre_serialize"
string(28) "filter_test1::post_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(29) "filter_test1::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(30) "filter_test1::post_unserialize"
string(9) "Thank you"
-origin2(3,2)-
string(27) "filter_test3::pre_serialize"
string(27) "filter_test2::pre_serialize"
string(28) "filter_test3::post_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(29) "filter_test3::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(30) "filter_test3::post_unserialize"
string(9) "Thank you"
-clone1(1,2,3)-
string(27) "filter_test1::pre_serialize"
string(27) "filter_test2::pre_serialize"
string(27) "filter_test3::pre_serialize"
string(28) "filter_test1::post_serialize"
string(28) "filter_test2::post_serialize"
string(28) "filter_test3::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test3::pre_unserialize"
string(29) "filter_test2::pre_unserialize"
string(29) "filter_test1::pre_unserialize"
string(30) "filter_test3::post_unserialize"
string(30) "filter_test2::post_unserialize"
string(30) "filter_test1::post_unserialize"
string(9) "Thank you"
-clone2(2)-
string(27) "filter_test2::pre_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(9) "Thank you"
[clone1 remove filter 2]
bool(true)
-origin1(1,2)-
string(27) "filter_test1::pre_serialize"
string(27) "filter_test2::pre_serialize"
string(28) "filter_test1::post_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(29) "filter_test1::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(30) "filter_test1::post_unserialize"
string(9) "Thank you"
-origin2(3,2)-
string(27) "filter_test3::pre_serialize"
string(27) "filter_test2::pre_serialize"
string(28) "filter_test3::post_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(29) "filter_test3::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(30) "filter_test3::post_unserialize"
string(9) "Thank you"
-clone1(1,3)-
string(27) "filter_test1::pre_serialize"
string(27) "filter_test3::pre_serialize"
string(28) "filter_test1::post_serialize"
string(28) "filter_test3::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test3::pre_unserialize"
string(29) "filter_test1::pre_unserialize"
string(30) "filter_test3::post_unserialize"
string(30) "filter_test1::post_unserialize"
string(9) "Thank you"
-clone2(2)-
string(27) "filter_test2::pre_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(9) "Thank you"
[origin1 remove filter 2]
bool(true)
-origin1(1)-
string(27) "filter_test1::pre_serialize"
string(28) "filter_test1::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test1::pre_unserialize"
string(30) "filter_test1::post_unserialize"
string(9) "Thank you"
-origin2(3,2)-
string(27) "filter_test3::pre_serialize"
string(27) "filter_test2::pre_serialize"
string(28) "filter_test3::post_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(29) "filter_test3::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(30) "filter_test3::post_unserialize"
string(9) "Thank you"
-clone1(1,3)-
string(27) "filter_test1::pre_serialize"
string(27) "filter_test3::pre_serialize"
string(28) "filter_test1::post_serialize"
string(28) "filter_test3::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test3::pre_unserialize"
string(29) "filter_test1::pre_unserialize"
string(30) "filter_test3::post_unserialize"
string(30) "filter_test1::post_unserialize"
string(9) "Thank you"
-clone2(2)-
string(27) "filter_test2::pre_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(9) "Thank you"
[unset clone2]
-origin1(1)-
string(27) "filter_test1::pre_serialize"
string(28) "filter_test1::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test1::pre_unserialize"
string(30) "filter_test1::post_unserialize"
string(9) "Thank you"
-origin2(3,2)-
string(27) "filter_test3::pre_serialize"
string(27) "filter_test2::pre_serialize"
string(28) "filter_test3::post_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(29) "filter_test3::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(30) "filter_test3::post_unserialize"
string(9) "Thank you"
-clone1(1,3)-
string(27) "filter_test1::pre_serialize"
string(27) "filter_test3::pre_serialize"
string(28) "filter_test1::post_serialize"
string(28) "filter_test3::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test3::pre_unserialize"
string(29) "filter_test1::pre_unserialize"
string(30) "filter_test3::post_unserialize"
string(30) "filter_test1::post_unserialize"
string(9) "Thank you"
[unset origin1]
-origin2(3,2)-
string(27) "filter_test3::pre_serialize"
string(27) "filter_test2::pre_serialize"
string(28) "filter_test3::post_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(29) "filter_test3::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(30) "filter_test3::post_unserialize"
string(9) "Thank you"
-clone1(1,3)-
string(27) "filter_test1::pre_serialize"
string(27) "filter_test3::pre_serialize"
string(28) "filter_test1::post_serialize"
string(28) "filter_test3::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test3::pre_unserialize"
string(29) "filter_test1::pre_unserialize"
string(30) "filter_test3::post_unserialize"
string(30) "filter_test1::post_unserialize"
string(9) "Thank you"
