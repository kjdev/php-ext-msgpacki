--TEST--
serialize()/msgpacki_unserialize() objects
--SKIPIF--
<?php if (!interface_exists('Serializable')) die('skip Interface Serialzable not defined'); ?>
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

// This test verifies that old and new style (un)serializing do not interfere.

function do_autoload($class_name) {
    if ($class_name != 'autoload_not_available') {
        require_once(dirname(__FILE__) . '/' . strtolower($class_name) . '.p5c');
    }
    echo __FUNCTION__ . "($class_name)\n";
}

function unserializer($class_name) {
    echo __METHOD__ . "($class_name)\n";
    switch($class_name) {
        case 'TestNAOld':
            eval("class TestNAOld extends TestOld {}");
            break;
        case 'TestNANew':
            eval("class TestNANew extends TestNew {}");
            break;
        case 'TestNANew2':
            eval("class TestNANew2 extends TestNew {}");
            break;
        default:
            echo "Try __autoload()\n";
            if (!function_exists('__autoload')) {
                eval('function __autoload($class_name) { do_autoload($class_name); }');
            }
            __autoload($class_name);
            break;
    }
}

ini_set('unserialize_callback_func', 'unserializer');

class TestOld {
    function serialize() {
        echo __METHOD__ . "()\n";
    }

    function unserialize($serialized) {
        echo __METHOD__ . "()\n";
    }

    function __wakeup() {
        echo __METHOD__ . "()\n";
    }

    function __sleep() {
        echo __METHOD__ . "()\n";
        return array();
    }
}

class TestNew implements Serializable {
    protected static $check = 0;

    function serialize() {
        echo __METHOD__ . "()\n";
        switch(++self::$check) {
            case 1:
                return NULL;
            case 2:
                return "2";
        }
    }

    function unserialize($serialized) {
        echo __METHOD__ . "()\n";
    }

    function __wakeup() {
        echo __METHOD__ . "()\n";
    }

    function __sleep() {
        echo __METHOD__ . "()\n";
    }
}

echo "===O1===\n";
$ser = msgpacki_serialize(new TestOld);
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));

echo "===N1===\n";
$ser = msgpacki_serialize(new TestNew);
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));

echo "===N2===\n";
$ser = msgpacki_serialize(new TestNew);
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));

echo "===NAOld===\n";
var_dump(msgpacki_unserialize(pack('H*','81c0a9546573744e414f6c64')));

echo "===NANew===\n";
var_dump(msgpacki_unserialize(pack('H*', '81c0a9546573744e414e6577')));

echo "===NANew2===\n";
var_dump(msgpacki_unserialize(pack('H*', '82c003aa546573744e414e657732a0')));

echo "===AutoOld===\n";
var_dump(msgpacki_unserialize(pack('H*', '81c0b36175746f6c6f61645f696d706c656d656e7473')));

// Now we have __autoload(), that will be called before the old style header.
// If the old style handler also fails to register the class then the object
// becomes an incomplete class instance.

echo "===AutoNA===\n";
var_dump(msgpacki_unserialize(pack('H*', '81c0b66175746f6c6f61645f6e6f745f617661696c61626c65')));

?>
===DONE===
<?php exit(0); ?>
--EXPECTF--
===O1===
TestOld::__sleep()
string(20) "81c0a7546573744f6c64"
TestOld::__wakeup()
object(TestOld)#%d (0) {
}
===N1===
TestNew::serialize()
string(2) "c0"
NULL
===N2===
TestNew::serialize()
string(26) "82c003a7546573744e6577a132"
TestNew::unserialize()
object(TestNew)#%d (0) {
}
===NAOld===
unserializer(TestNAOld)
TestOld::__wakeup()
object(TestNAOld)#%d (0) {
}
===NANew===
unserializer(TestNANew)
TestNew::__wakeup()
object(TestNANew)#%d (0) {
}
===NANew2===
unserializer(TestNANew2)
TestNew::unserialize()
object(TestNANew2)#%d (0) {
}
===AutoOld===
unserializer(autoload_implements)
Try __autoload()
do_autoload(autoload_interface)
do_autoload(autoload_implements)
object(autoload_implements)#%d (0) {
}
===AutoNA===
do_autoload(autoload_not_available)
unserializer(autoload_not_available)
Try __autoload()
do_autoload(autoload_not_available)
do_autoload(autoload_not_available)

Warning: MessagePack unserialize: Function unserializer() hasn't defined the class it was called for in %s005.php on line %d
object(__PHP_Incomplete_Class)#%d (1) {
  ["__PHP_Incomplete_Class_Name"]=>
  string(22) "autoload_not_available"
}
===DONE===
