--TEST--
get_filters() and register
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

var_dump(get_filters());

filter_register("full", '\MessagePacki\filter_1');
filter_register("serialize_pre", '\MessagePacki\filter_2');
filter_register("serialize_post", '\MessagePacki\filter_3');
filter_register("unserialize_pre", '\MessagePacki\filter_4');
filter_register("unserialize_post", '\MessagePacki\filter_5');
filter_register("serialize_after", '\MessagePacki\filter_6');
filter_register("serialize_before", '\MessagePacki\filter_7');

echo "== all ==\n";
var_dump(get_filters());

echo "== pre_serialize ==\n";
var_dump(get_filters("pre_serialize"));
var_dump(get_filters(MSGPACKI_FILTER_PRE_SERIALIZE));

echo "== post_serialize ==\n";
var_dump(get_filters("post_serialize"));
var_dump(get_filters(MSGPACKI_FILTER_POST_SERIALIZE));

echo "== pre_unserialize ==\n";
var_dump(get_filters("pre_unserialize"));
var_dump(get_filters(MSGPACKI_FILTER_PRE_UNSERIALIZE));

echo "== post_unserialize ==\n";
var_dump(get_filters("post_unserialize"));
var_dump(get_filters(MSGPACKI_FILTER_POST_UNSERIALIZE));

echo "== nop ==\n";
var_dump(get_filters("nop"));

echo "== invalid ==\n";
var_dump(get_filters(1));
var_dump(get_filters(null));
var_dump(get_filters(array()));
?>
--EXPECTF--
array(0) {
}
== all ==
array(1) {
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
}
== pre_serialize ==
array(0) {
}
array(0) {
}
== post_serialize ==
array(0) {
}
array(0) {
}
== pre_unserialize ==
array(0) {
}
array(0) {
}
== post_unserialize ==
array(0) {
}
array(0) {
}
== nop ==
array(0) {
}
== invalid ==
array(0) {
}
array(0) {
}

Warning: MessagePacki\get_filters() expects parameter 1 to be string, array given in %s on line %d
NULL
