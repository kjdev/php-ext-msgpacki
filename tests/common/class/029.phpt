--TEST--
MessagePacki filter other instance
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
$m3 = new MessagePacki();

echo "-- instance 1 --\n";
var_dump($m1->append_filter('filter_test1'));
var_dump($m1->append_filter('filter_test2'));
var_dump($m1->append_filter('filter_test3'));
var_dump($m1->get_filters());

echo "-- instance 2 --\n";
var_dump($m2->append_filter('filter_test2'));
var_dump($m2->get_filters());

echo "-- instance 3 --\n";
var_dump($m3->get_filters());

echo "[pack]\n";
echo "-- instance 1 --\n";
$ser = $m1->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m1->unpack($ser));

echo "-- instance 2 --\n";
$ser = $m2->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m2->unpack($ser));

echo "-- instance 3 --\n";
$ser = $m3->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m3->unpack($ser));

echo "[remove filter]\n";
echo "-- instance 1 --\n";
var_dump($m1->remove_filter('filter_test2'));
var_dump($m1->get_filters());

echo "[pack]\n";
echo "-- instance 1 --\n";
$ser = $m1->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m1->unpack($ser));

echo "-- instance 2 --\n";
$ser = $m2->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m2->unpack($ser));

echo "-- instance 3 --\n";
$ser = $m3->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m3->unpack($ser));


echo "[add filter]\n";
echo "-- instance 3 --\n";
var_dump($m3->append_filter('filter_test3'));
var_dump($m3->get_filters());

echo "[pack]\n";
echo "-- instance 1 --\n";
$ser = $m1->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m1->unpack($ser));

echo "-- instance 2 --\n";
$ser = $m2->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m2->unpack($ser));

echo "-- instance 3 --\n";
$ser = $m3->pack("Thank you");
var_dump(bin2hex($ser));
var_dump($m3->unpack($ser));

?>
--EXPECTF--
-- instance 1 --
bool(true)
bool(true)
bool(true)
array(4) {
  ["pre_serialize"]=>
  array(3) {
    [0]=>
    string(12) "filter_test1"
    [1]=>
    string(12) "filter_test2"
    [2]=>
    string(12) "filter_test3"
  }
  ["post_serialize"]=>
  array(3) {
    [0]=>
    string(12) "filter_test1"
    [1]=>
    string(12) "filter_test2"
    [2]=>
    string(12) "filter_test3"
  }
  ["pre_unserialize"]=>
  array(3) {
    [0]=>
    string(12) "filter_test1"
    [1]=>
    string(12) "filter_test2"
    [2]=>
    string(12) "filter_test3"
  }
  ["post_unserialize"]=>
  array(3) {
    [0]=>
    string(12) "filter_test1"
    [1]=>
    string(12) "filter_test2"
    [2]=>
    string(12) "filter_test3"
  }
}
-- instance 2 --
bool(true)
array(4) {
  ["pre_serialize"]=>
  array(1) {
    [0]=>
    string(12) "filter_test2"
  }
  ["post_serialize"]=>
  array(1) {
    [0]=>
    string(12) "filter_test2"
  }
  ["pre_unserialize"]=>
  array(1) {
    [0]=>
    string(12) "filter_test2"
  }
  ["post_unserialize"]=>
  array(1) {
    [0]=>
    string(12) "filter_test2"
  }
}
-- instance 3 --
array(0) {
}
[pack]
-- instance 1 --
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
-- instance 2 --
string(27) "filter_test2::pre_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(9) "Thank you"
-- instance 3 --
string(20) "a95468616e6b20796f75"
string(9) "Thank you"
[remove filter]
-- instance 1 --
bool(true)
array(4) {
  ["pre_serialize"]=>
  array(2) {
    [0]=>
    string(12) "filter_test1"
    [1]=>
    string(12) "filter_test3"
  }
  ["post_serialize"]=>
  array(2) {
    [0]=>
    string(12) "filter_test1"
    [1]=>
    string(12) "filter_test3"
  }
  ["pre_unserialize"]=>
  array(2) {
    [0]=>
    string(12) "filter_test1"
    [1]=>
    string(12) "filter_test3"
  }
  ["post_unserialize"]=>
  array(2) {
    [0]=>
    string(12) "filter_test1"
    [1]=>
    string(12) "filter_test3"
  }
}
[pack]
-- instance 1 --
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
-- instance 2 --
string(27) "filter_test2::pre_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(9) "Thank you"
-- instance 3 --
string(20) "a95468616e6b20796f75"
string(9) "Thank you"
[add filter]
-- instance 3 --
bool(true)
array(4) {
  ["pre_serialize"]=>
  array(1) {
    [0]=>
    string(12) "filter_test3"
  }
  ["post_serialize"]=>
  array(1) {
    [0]=>
    string(12) "filter_test3"
  }
  ["pre_unserialize"]=>
  array(1) {
    [0]=>
    string(12) "filter_test3"
  }
  ["post_unserialize"]=>
  array(1) {
    [0]=>
    string(12) "filter_test3"
  }
}
[pack]
-- instance 1 --
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
-- instance 2 --
string(27) "filter_test2::pre_serialize"
string(28) "filter_test2::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test2::pre_unserialize"
string(30) "filter_test2::post_unserialize"
string(9) "Thank you"
-- instance 3 --
string(27) "filter_test3::pre_serialize"
string(28) "filter_test3::post_serialize"
string(20) "a95468616e6b20796f75"
string(29) "filter_test3::pre_unserialize"
string(30) "filter_test3::post_unserialize"
string(9) "Thank you"
