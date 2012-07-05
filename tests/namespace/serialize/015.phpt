--TEST--
__autoload() not invoked for interfaces
--SKIPIF--
<?php
    if (class_exists('autoload_root', false)) die('skip Autoload test classes exist already');
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

var_dump(interface_exists('MessagePacki\autoload_interface', false));
var_dump(class_exists('MessagePacki\autoload_implements', false));

$o = \MessagePacki\unserialize(pack('H*', '81c0da00204d6573736167655061636b695c4175746f6c6f61645f496d706c656d656e7473'));

var_dump($o);
var_dump($o instanceof MessagePacki\autoload_interface);
unset($o);

var_dump(interface_exists('MessagePacki\autoload_interface', false));
var_dump(class_exists('MessagePacki\autoload_implements', false));

?>
===DONE===
--EXPECTF--
bool(false)
bool(false)
__autoload(autoload_interface)
__autoload(Autoload_Implements)
object(MessagePacki\autoload_implements)#%d (0) {
}
bool(true)
bool(true)
bool(true)
===DONE===
