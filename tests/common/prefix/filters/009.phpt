--TEST--
msgpacki_get_filters() and append, remove
--FILE--
<?php


class filter_1 extends MessagePacki_Filter
{
    public function pre_serialize($in) {}
    public function post_serialize($in) {}
    public function pre_unserialize($in) {}
    public function post_unserialize($in) {}
}

class filter_2 extends MessagePacki_Filter
{
    public function pre_serialize($in) {}
}

class filter_3 extends MessagePacki_Filter
{
    public function post_serialize($in) {}
}

class filter_4 extends MessagePacki_Filter
{
    public function pre_unserialize($in) {}
}

class filter_5 extends MessagePacki_Filter
{
    public function post_unserialize($in) {}
}

class filter_6 extends MessagePacki_Filter
{
    public function post_serialize($in) {}
    public function pre_unserialize($in) {}
}

class filter_7 extends MessagePacki_Filter
{
    public function pre_serialize($in) {}
    public function post_unserialize($in) {}
}

msgpacki_filter_register("full", 'filter_1');
msgpacki_filter_register("serialize_pre", 'filter_2');
msgpacki_filter_register("serialize_post", 'filter_3');
msgpacki_filter_register("unserialize_pre", 'filter_4');
msgpacki_filter_register("unserialize_post", 'filter_5');
msgpacki_filter_register("serialize_after", 'filter_6');
msgpacki_filter_register("serialize_before", 'filter_7');

msgpacki_filter_append("full");
msgpacki_filter_append("serialize_pre");
msgpacki_filter_append("serialize_post");
msgpacki_filter_append("unserialize_pre");
msgpacki_filter_append("unserialize_post");
msgpacki_filter_append("serialize_after");
msgpacki_filter_append("serialize_before");

function print_filters() {
    echo "== all ==\n";
    var_dump(msgpacki_get_filters());
    echo "== pre_serialize ==\n";
    var_dump(msgpacki_get_filters(MSGPACKI_FILTER_PRE_SERIALIZE));
    echo "== post_serialize ==\n";
    var_dump(msgpacki_get_filters(MSGPACKI_FILTER_POST_SERIALIZE));
    echo "== pre_unserialize ==\n";
    var_dump(msgpacki_get_filters(MSGPACKI_FILTER_PRE_UNSERIALIZE));
    echo "== post_unserialize ==\n";
    var_dump(msgpacki_get_filters(MSGPACKI_FILTER_POST_UNSERIALIZE));
}

print_filters();

echo "-- remove serialize_pre --\n";
msgpacki_filter_remove("serialize_pre");
print_filters();

echo "-- remove serialize_post --\n";
msgpacki_filter_remove("serialize_post");
print_filters();

echo "-- remove serialize_after --\n";
msgpacki_filter_remove("serialize_after");
print_filters();

echo "-- remove serialize_before --\n";
msgpacki_filter_remove("serialize_before");
print_filters();
?>
--EXPECTF--
== all ==
array(5) {
  ["registers"]=>
  array(7) {
    [0]=>
    string(4) "full"
    [1]=>
    string(13) "serialize_pre"
    [2]=>
    string(14) "serialize_post"
    [3]=>
    string(15) "unserialize_pre"
    [4]=>
    string(16) "unserialize_post"
    [5]=>
    string(15) "serialize_after"
    [6]=>
    string(16) "serialize_before"
  }
  ["pre_serialize"]=>
  array(3) {
    [0]=>
    string(4) "full"
    [1]=>
    string(13) "serialize_pre"
    [2]=>
    string(16) "serialize_before"
  }
  ["post_serialize"]=>
  array(3) {
    [0]=>
    string(4) "full"
    [1]=>
    string(14) "serialize_post"
    [2]=>
    string(15) "serialize_after"
  }
  ["pre_unserialize"]=>
  array(3) {
    [0]=>
    string(4) "full"
    [1]=>
    string(15) "unserialize_pre"
    [2]=>
    string(15) "serialize_after"
  }
  ["post_unserialize"]=>
  array(3) {
    [0]=>
    string(4) "full"
    [1]=>
    string(16) "unserialize_post"
    [2]=>
    string(16) "serialize_before"
  }
}
== pre_serialize ==
array(3) {
  [0]=>
  string(4) "full"
  [1]=>
  string(13) "serialize_pre"
  [2]=>
  string(16) "serialize_before"
}
== post_serialize ==
array(3) {
  [0]=>
  string(4) "full"
  [1]=>
  string(14) "serialize_post"
  [2]=>
  string(15) "serialize_after"
}
== pre_unserialize ==
array(3) {
  [0]=>
  string(4) "full"
  [1]=>
  string(15) "unserialize_pre"
  [2]=>
  string(15) "serialize_after"
}
== post_unserialize ==
array(3) {
  [0]=>
  string(4) "full"
  [1]=>
  string(16) "unserialize_post"
  [2]=>
  string(16) "serialize_before"
}
-- remove serialize_pre --
== all ==
array(5) {
  ["registers"]=>
  array(7) {
    [0]=>
    string(4) "full"
    [1]=>
    string(13) "serialize_pre"
    [2]=>
    string(14) "serialize_post"
    [3]=>
    string(15) "unserialize_pre"
    [4]=>
    string(16) "unserialize_post"
    [5]=>
    string(15) "serialize_after"
    [6]=>
    string(16) "serialize_before"
  }
  ["pre_serialize"]=>
  array(2) {
    [0]=>
    string(4) "full"
    [1]=>
    string(16) "serialize_before"
  }
  ["post_serialize"]=>
  array(3) {
    [0]=>
    string(4) "full"
    [1]=>
    string(14) "serialize_post"
    [2]=>
    string(15) "serialize_after"
  }
  ["pre_unserialize"]=>
  array(3) {
    [0]=>
    string(4) "full"
    [1]=>
    string(15) "unserialize_pre"
    [2]=>
    string(15) "serialize_after"
  }
  ["post_unserialize"]=>
  array(3) {
    [0]=>
    string(4) "full"
    [1]=>
    string(16) "unserialize_post"
    [2]=>
    string(16) "serialize_before"
  }
}
== pre_serialize ==
array(2) {
  [0]=>
  string(4) "full"
  [1]=>
  string(16) "serialize_before"
}
== post_serialize ==
array(3) {
  [0]=>
  string(4) "full"
  [1]=>
  string(14) "serialize_post"
  [2]=>
  string(15) "serialize_after"
}
== pre_unserialize ==
array(3) {
  [0]=>
  string(4) "full"
  [1]=>
  string(15) "unserialize_pre"
  [2]=>
  string(15) "serialize_after"
}
== post_unserialize ==
array(3) {
  [0]=>
  string(4) "full"
  [1]=>
  string(16) "unserialize_post"
  [2]=>
  string(16) "serialize_before"
}
-- remove serialize_post --
== all ==
array(5) {
  ["registers"]=>
  array(7) {
    [0]=>
    string(4) "full"
    [1]=>
    string(13) "serialize_pre"
    [2]=>
    string(14) "serialize_post"
    [3]=>
    string(15) "unserialize_pre"
    [4]=>
    string(16) "unserialize_post"
    [5]=>
    string(15) "serialize_after"
    [6]=>
    string(16) "serialize_before"
  }
  ["pre_serialize"]=>
  array(2) {
    [0]=>
    string(4) "full"
    [1]=>
    string(16) "serialize_before"
  }
  ["post_serialize"]=>
  array(2) {
    [0]=>
    string(4) "full"
    [1]=>
    string(15) "serialize_after"
  }
  ["pre_unserialize"]=>
  array(3) {
    [0]=>
    string(4) "full"
    [1]=>
    string(15) "unserialize_pre"
    [2]=>
    string(15) "serialize_after"
  }
  ["post_unserialize"]=>
  array(3) {
    [0]=>
    string(4) "full"
    [1]=>
    string(16) "unserialize_post"
    [2]=>
    string(16) "serialize_before"
  }
}
== pre_serialize ==
array(2) {
  [0]=>
  string(4) "full"
  [1]=>
  string(16) "serialize_before"
}
== post_serialize ==
array(2) {
  [0]=>
  string(4) "full"
  [1]=>
  string(15) "serialize_after"
}
== pre_unserialize ==
array(3) {
  [0]=>
  string(4) "full"
  [1]=>
  string(15) "unserialize_pre"
  [2]=>
  string(15) "serialize_after"
}
== post_unserialize ==
array(3) {
  [0]=>
  string(4) "full"
  [1]=>
  string(16) "unserialize_post"
  [2]=>
  string(16) "serialize_before"
}
-- remove serialize_after --
== all ==
array(5) {
  ["registers"]=>
  array(7) {
    [0]=>
    string(4) "full"
    [1]=>
    string(13) "serialize_pre"
    [2]=>
    string(14) "serialize_post"
    [3]=>
    string(15) "unserialize_pre"
    [4]=>
    string(16) "unserialize_post"
    [5]=>
    string(15) "serialize_after"
    [6]=>
    string(16) "serialize_before"
  }
  ["pre_serialize"]=>
  array(2) {
    [0]=>
    string(4) "full"
    [1]=>
    string(16) "serialize_before"
  }
  ["post_serialize"]=>
  array(1) {
    [0]=>
    string(4) "full"
  }
  ["pre_unserialize"]=>
  array(2) {
    [0]=>
    string(4) "full"
    [1]=>
    string(15) "unserialize_pre"
  }
  ["post_unserialize"]=>
  array(3) {
    [0]=>
    string(4) "full"
    [1]=>
    string(16) "unserialize_post"
    [2]=>
    string(16) "serialize_before"
  }
}
== pre_serialize ==
array(2) {
  [0]=>
  string(4) "full"
  [1]=>
  string(16) "serialize_before"
}
== post_serialize ==
array(1) {
  [0]=>
  string(4) "full"
}
== pre_unserialize ==
array(2) {
  [0]=>
  string(4) "full"
  [1]=>
  string(15) "unserialize_pre"
}
== post_unserialize ==
array(3) {
  [0]=>
  string(4) "full"
  [1]=>
  string(16) "unserialize_post"
  [2]=>
  string(16) "serialize_before"
}
-- remove serialize_before --
== all ==
array(5) {
  ["registers"]=>
  array(7) {
    [0]=>
    string(4) "full"
    [1]=>
    string(13) "serialize_pre"
    [2]=>
    string(14) "serialize_post"
    [3]=>
    string(15) "unserialize_pre"
    [4]=>
    string(16) "unserialize_post"
    [5]=>
    string(15) "serialize_after"
    [6]=>
    string(16) "serialize_before"
  }
  ["pre_serialize"]=>
  array(1) {
    [0]=>
    string(4) "full"
  }
  ["post_serialize"]=>
  array(1) {
    [0]=>
    string(4) "full"
  }
  ["pre_unserialize"]=>
  array(2) {
    [0]=>
    string(4) "full"
    [1]=>
    string(15) "unserialize_pre"
  }
  ["post_unserialize"]=>
  array(2) {
    [0]=>
    string(4) "full"
    [1]=>
    string(16) "unserialize_post"
  }
}
== pre_serialize ==
array(1) {
  [0]=>
  string(4) "full"
}
== post_serialize ==
array(1) {
  [0]=>
  string(4) "full"
}
== pre_unserialize ==
array(2) {
  [0]=>
  string(4) "full"
  [1]=>
  string(15) "unserialize_pre"
}
== post_unserialize ==
array(2) {
  [0]=>
  string(4) "full"
  [1]=>
  string(16) "unserialize_post"
}
