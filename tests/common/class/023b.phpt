--TEST--
MessagePacki::get_filters() and append
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

echo "== all ==\n";
var_dump($m->get_filters());

echo "== pre_serialize ==\n";
var_dump($m->get_filters("pre_serialize"));
var_dump($m->get_filters(MSGPACKI_FILTER_PRE_SERIALIZE));

echo "== post_serialize ==\n";
var_dump($m->get_filters("post_serialize"));
var_dump($m->get_filters(MSGPACKI_FILTER_POST_SERIALIZE));

echo "== pre_unserialize ==\n";
var_dump($m->get_filters("pre_unserialize"));
var_dump($m->get_filters(MSGPACKI_FILTER_PRE_UNSERIALIZE));

echo "== post_unserialize ==\n";
var_dump($m->get_filters("post_unserialize"));
var_dump($m->get_filters(MSGPACKI_FILTER_POST_UNSERIALIZE));

echo "== nop ==\n";
var_dump($m->get_filters("nop"));

echo "== invalid ==\n";
var_dump($m->get_filters(1));
var_dump($m->get_filters(null));
var_dump($m->get_filters(array()));
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
array(3) {
  [0]=>
  string(11) "filter_full"
  [1]=>
  string(23) "filter_post_unserialize"
  [2]=>
  string(13) "filter_before"
}
== nop ==
array(0) {
}
== invalid ==
array(0) {
}
array(0) {
}

Warning: MessagePacki::get_filters() expects parameter 1 to be string, array given in %s on line %d
NULL
