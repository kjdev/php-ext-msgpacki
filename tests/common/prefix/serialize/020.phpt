--TEST--
Unserializing of namespaced class object fails
--SKIPIF--
<?php
if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    die("skip this test is for PHP 5.3 or newer");
}
?>
--FILE--
<?php
namespace Foo;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

class Bar {
}
echo bin2hex(msgpacki_serialize(new Bar)) . "\n";
$x = msgpacki_unserialize(msgpacki_serialize(new Bar));
echo get_class($x) . "\n";
?>
--EXPECT--
81c0a7466f6f5c426172
Foo\Bar
