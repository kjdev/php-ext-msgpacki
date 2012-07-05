--TEST--
MessagePacki Unserializing of namespaced class object fails
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

$m = new \MessagePacki();

echo bin2hex($m->pack(new Bar)) . "\n";
$x = $m->unpack($m->pack(new Bar));
echo get_class($x) . "\n";
?>
--EXPECT--
81c0a7466f6f5c426172
Foo\Bar
