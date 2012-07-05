--TEST--
get_filters() and append, remove
--FILE--
<?php
namespace MessagePacki;

class filter_1 extends Filter
{
    public function pre_serialize($in) {}
    public function post_serialize($in) {}
    public function pre_unserialize($in) {}
    public function post_unserialize($in) {}
}

class filter_2 extends Filter
{
    public function pre_serialize($in) {}
}

class filter_3 extends Filter
{
    public function post_serialize($in) {}
}

class filter_4 extends Filter
{
    public function pre_unserialize($in) {}
}

class filter_5 extends Filter
{
    public function post_unserialize($in) {}
}

class filter_6 extends Filter
{
    public function post_serialize($in) {}
    public function pre_unserialize($in) {}
}

class filter_7 extends Filter
{
    public function pre_serialize($in) {}
    public function post_unserialize($in) {}
}

filter_register("full", '\MessagePacki\filter_1');
filter_register("serialize_pre", '\MessagePacki\filter_2');
filter_register("serialize_post", '\MessagePacki\filter_3');
filter_register("unserialize_pre", '\MessagePacki\filter_4');
filter_register("unserialize_post", '\MessagePacki\filter_5');
filter_register("serialize_after", '\MessagePacki\filter_6');
filter_register("serialize_before", '\MessagePacki\filter_7');

filter_append("full");
filter_append("serialize_pre");
filter_append("serialize_post");
filter_append("unserialize_pre");
filter_append("unserialize_post");
filter_append("serialize_after");
filter_append("serialize_before");

function print_filters() {
    echo "== all ==\n";
    var_dump(get_filters());
    echo "== pre_serialize ==\n";
    var_dump(get_filters(MSGPACKI_FILTER_PRE_SERIALIZE));
    echo "== post_serialize ==\n";
    var_dump(get_filters(MSGPACKI_FILTER_POST_SERIALIZE));
    echo "== pre_unserialize ==\n";
    var_dump(get_filters(MSGPACKI_FILTER_PRE_UNSERIALIZE));
    echo "== post_unserialize ==\n";
    var_dump(get_filters(MSGPACKI_FILTER_POST_UNSERIALIZE));
}

print_filters();

echo "-- remove serialize_pre --\n";
filter_remove("serialize_pre");
print_filters();

echo "-- remove serialize_post --\n";
filter_remove("serialize_post");
print_filters();

echo "-- remove serialize_after --\n";
filter_remove("serialize_after");
print_filters();

echo "-- remove serialize_before --\n";
filter_remove("serialize_before");
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
