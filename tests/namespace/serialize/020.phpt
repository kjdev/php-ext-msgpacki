--TEST--
Unserializing of namespaced class object fails
--FILE--
<?php
namespace Foo;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

class Bar {
}
echo bin2hex(\MessagePacki\serialize(new Bar)) . "\n";
$x = \MessagePacki\unserialize(\MessagePacki\serialize(new Bar));
echo get_class($x) . "\n";
?>
--EXPECT--
81c0a7466f6f5c426172
Foo\Bar
