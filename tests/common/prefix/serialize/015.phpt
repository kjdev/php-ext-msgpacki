--TEST--
__autoload() not invoked for interfaces
--SKIPIF--
<?php
    if (class_exists('autoload_root', false)) die('skip Autoload test classes exinst already');
?>
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

function __autoload($class_name) {
    $c = strrchr($class_name, '\\');
    if ($c !== false) {
        $class_name = substr($c, 1);
    }
    require_once(dirname(__FILE__) . '/' . strtolower($class_name) . '.p5c');
    echo __FUNCTION__ . '(' . $class_name . ")\n";
}

var_dump(interface_exists('autoload_interface', false));
var_dump(class_exists('autoload_implements', false));

$o = msgpacki_unserialize(pack('H*', '81c0b34175746f6c6f61645f496d706c656d656e7473'));

var_dump($o);
var_dump($o instanceof autoload_interface);
unset($o);

var_dump(interface_exists('autoload_interface', false));
var_dump(class_exists('autoload_implements', false));

?>
===DONE===
--EXPECTF--
bool(false)
bool(false)
__autoload(autoload_interface)
__autoload(Autoload_Implements)
object(autoload_implements)#%d (0) {
}
bool(true)
bool(true)
bool(true)
===DONE===
