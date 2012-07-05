--TEST--
MessagePacki::get_filters() and append, remove
--FILE--
<?php

class filter_full extends MessagePacki_Filter
{
    public function pre_serialize($in) {}
    public function post_serialize($in) {}
    public function pre_unserialize($in) {}
    public function post_unserialize($in) {}
}

class filter_pre_serialize extends MessagePacki_Filter
{
    public function pre_serialize($in) {}
}

class filter_post_serialize extends MessagePacki_Filter
{
    public function post_serialize($in) {}
}

class filter_pre_unserialize extends MessagePacki_Filter
{
    public function pre_unserialize($in) {}
}

class filter_post_unserialize extends MessagePacki_Filter
{
    public function post_unserialize($in) {}
}

class filter_after extends MessagePacki_Filter
{
    public function post_serialize($in) {}
    public function pre_unserialize($in) {}
}

class filter_before extends MessagePacki_Filter
{
    public function pre_serialize($in) {}
    public function post_unserialize($in) {}
}

$m = new MessagePacki();

echo "== append ==\n";
var_dump($m->append_filter('filter_full'));
var_dump($m->append_filter('filter_pre_serialize'));
var_dump($m->append_filter('filter_post_serialize'));
var_dump($m->append_filter('filter_pre_unserialize'));
var_dump($m->append_filter('filter_post_unserialize'));
var_dump($m->append_filter('filter_after'));
var_dump($m->append_filter('filter_before'));

function print_filters($m) {
    echo "== all ==\n";
    var_dump($m->get_filters());
    echo "== pre_serialize ==\n";
    var_dump($m->get_filters(MSGPACKI_FILTER_PRE_SERIALIZE));
    echo "== post_serialize ==\n";
    var_dump($m->get_filters(MSGPACKI_FILTER_POST_SERIALIZE));
    echo "== pre_unserialize ==\n";
    var_dump($m->get_filters(MSGPACKI_FILTER_PRE_UNSERIALIZE));
    echo "== post_unserialize ==\n";
    var_dump($m->get_filters(MSGPACKI_FILTER_POST_UNSERIALIZE));
}

print_filters($m);

echo "-- remove serialize_pre --\n";
var_dump($m->remove_filter("filter_pre_serialize"));
print_filters($m);

echo "-- remove serialize_post --\n";
var_dump($m->remove_filter("filter_post_serialize"));
print_filters($m);

echo "-- remove serialize_after --\n";
var_dump($m->remove_filter("filter_after"));
print_filters($m);

echo "-- remove serialize_before --\n";
var_dump($m->remove_filter("filter_before"));
print_filters($m);
?>
--EXPECTF--
== append ==
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
== all ==
array(4) {
  ["pre_serialize"]=>
  array(3) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(20) "filter_pre_serialize"
    [2]=>
    string(13) "filter_before"
  }
  ["post_serialize"]=>
  array(3) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(21) "filter_post_serialize"
    [2]=>
    string(12) "filter_after"
  }
  ["pre_unserialize"]=>
  array(3) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(22) "filter_pre_unserialize"
    [2]=>
    string(12) "filter_after"
  }
  ["post_unserialize"]=>
  array(3) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(23) "filter_post_unserialize"
    [2]=>
    string(13) "filter_before"
  }
}
== pre_serialize ==
array(3) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(20) "filter_pre_serialize"
  [2]=>
  string(13) "filter_before"
}
== post_serialize ==
array(3) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(21) "filter_post_serialize"
  [2]=>
  string(12) "filter_after"
}
== pre_unserialize ==
array(3) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(22) "filter_pre_unserialize"
  [2]=>
  string(12) "filter_after"
}
== post_unserialize ==
array(3) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(23) "filter_post_unserialize"
  [2]=>
  string(13) "filter_before"
}
-- remove serialize_pre --
bool(true)
== all ==
array(4) {
  ["pre_serialize"]=>
  array(2) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(13) "filter_before"
  }
  ["post_serialize"]=>
  array(3) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(21) "filter_post_serialize"
    [2]=>
    string(12) "filter_after"
  }
  ["pre_unserialize"]=>
  array(3) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(22) "filter_pre_unserialize"
    [2]=>
    string(12) "filter_after"
  }
  ["post_unserialize"]=>
  array(3) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(23) "filter_post_unserialize"
    [2]=>
    string(13) "filter_before"
  }
}
== pre_serialize ==
array(2) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(13) "filter_before"
}
== post_serialize ==
array(3) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(21) "filter_post_serialize"
  [2]=>
  string(12) "filter_after"
}
== pre_unserialize ==
array(3) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(22) "filter_pre_unserialize"
  [2]=>
  string(12) "filter_after"
}
== post_unserialize ==
array(3) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(23) "filter_post_unserialize"
  [2]=>
  string(13) "filter_before"
}
-- remove serialize_post --
bool(true)
== all ==
array(4) {
  ["pre_serialize"]=>
  array(2) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(13) "filter_before"
  }
  ["post_serialize"]=>
  array(2) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(12) "filter_after"
  }
  ["pre_unserialize"]=>
  array(3) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(22) "filter_pre_unserialize"
    [2]=>
    string(12) "filter_after"
  }
  ["post_unserialize"]=>
  array(3) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(23) "filter_post_unserialize"
    [2]=>
    string(13) "filter_before"
  }
}
== pre_serialize ==
array(2) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(13) "filter_before"
}
== post_serialize ==
array(2) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(12) "filter_after"
}
== pre_unserialize ==
array(3) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(22) "filter_pre_unserialize"
  [2]=>
  string(12) "filter_after"
}
== post_unserialize ==
array(3) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(23) "filter_post_unserialize"
  [2]=>
  string(13) "filter_before"
}
-- remove serialize_after --
bool(true)
== all ==
array(4) {
  ["pre_serialize"]=>
  array(2) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(13) "filter_before"
  }
  ["post_serialize"]=>
  array(1) {
    [0]=>
    string(11) "filter_full"
  }
  ["pre_unserialize"]=>
  array(2) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(22) "filter_pre_unserialize"
  }
  ["post_unserialize"]=>
  array(3) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(23) "filter_post_unserialize"
    [2]=>
    string(13) "filter_before"
  }
}
== pre_serialize ==
array(2) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(13) "filter_before"
}
== post_serialize ==
array(1) {
  [0]=>
  string(11) "filter_full"
}
== pre_unserialize ==
array(2) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(22) "filter_pre_unserialize"
}
== post_unserialize ==
array(3) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(23) "filter_post_unserialize"
  [2]=>
  string(13) "filter_before"
}
-- remove serialize_before --
bool(true)
== all ==
array(4) {
  ["pre_serialize"]=>
  array(1) {
    [0]=>
    string(11) "filter_full"
  }
  ["post_serialize"]=>
  array(1) {
    [0]=>
    string(11) "filter_full"
  }
  ["pre_unserialize"]=>
  array(2) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(22) "filter_pre_unserialize"
  }
  ["post_unserialize"]=>
  array(2) {
    [0]=>
    string(11) "filter_full"
    [1]=>
    string(23) "filter_post_unserialize"
  }
}
== pre_serialize ==
array(1) {
  [0]=>
  string(11) "filter_full"
}
== post_serialize ==
array(1) {
  [0]=>
  string(11) "filter_full"
}
== pre_unserialize ==
array(2) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(22) "filter_pre_unserialize"
}
== post_unserialize ==
array(2) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(23) "filter_post_unserialize"
}
