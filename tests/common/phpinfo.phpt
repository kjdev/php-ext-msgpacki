--TEST--
TEST phpinfo() displays msgpacki info
--SKIPIF--
<?php
if (!extension_loaded('session')) {
    die("skip this test is for session support enabled");
}
?>
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

phpinfo();
--EXPECTF--
%a
msgpacki

msgpacki support => enabled
extension version => 1.0.0
session support => enabled

Directive => Local Value => Master Value
msgpacki.mode => 2 => 2%a
