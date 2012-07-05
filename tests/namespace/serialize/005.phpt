--TEST--
serialize()/unserialize() objects
--SKIPIF--
<?php if (!interface_exists('Serializable')) die('skip Interface Serialzable not defined'); ?>
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

// This test verifies that old and new style (un)serializing do not interfere.

function do_autoload($class_name) {
    if ($class_name != __NAMESPACE__ . '\autoload_not_available') {
        $c = strrchr($class_name, '\\');
        if ($c !== false) {
            $class_name = substr($c, 1);
        }
        require_once(dirname(__FILE__) . '/' . strtolower($class_name) . '.p5c');
    }
    echo __FUNCTION__ . "($class_name)\n";
}

function unserializer($class_name) {
    echo __METHOD__ . "($class_name)\n";
    switch($class_name) {
        case __NAMESPACE__ . '\TestNAOld':
            eval("namespace MessagePacki; class TestNAOld extends TestOld {}");
            break;
        case __NAMESPACE__ . '\TestNANew':
            eval("namespace MessagePacki; class TestNANew extends TestNew {}");
            break;
        case __NAMESPACE__ . '\TestNANew2':
            eval("namespace MessagePacki; class TestNANew2 extends TestNew {}");
            break;
        default:
            echo "Try __autoload()\n";
            if (!function_exists('__autoload')) {
                eval('function __autoload($class_name) { ' . __NAMESPACE__ . '\do_autoload($class_name); }');
            }
            __autoload($class_name);
            break;
    }
}

ini_set('unserialize_callback_func', __NAMESPACE__ . '\unserializer');

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

class TestNew implements \Serializable {
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
$ser = serialize(new TestOld);
var_dump(bin2hex($ser));
var_dump(unserialize($ser));

echo "===N1===\n";
$ser = serialize(new TestNew);
var_dump(bin2hex($ser));
var_dump(unserialize($ser));

echo "===N2===\n";
$ser = serialize(new TestNew);
var_dump(bin2hex($ser));
var_dump(unserialize($ser));

echo "===NAOld===\n";
var_dump(unserialize(pack('H*','81c0b64d6573736167655061636b695c546573744e414f6c64')));

echo "===NANew===\n";
var_dump(unserialize(pack('H*', '81c0b64d6573736167655061636b695c546573744e414e6577')));

echo "===NANew2===\n";
var_dump(unserialize(pack('H*', '82c003b74d6573736167655061636b695c546573744e414e657732a0')));

echo "===AutoOld===\n";
var_dump(unserialize(pack('H*', '81c0da00204d6573736167655061636b695c6175746f6c6f61645f696d706c656d656e7473')));

// Now we have __autoload(), that will be called before the old style header.
// If the old style handler also fails to register the class then the object
// becomes an incomplete class instance.

echo "===AutoNA===\n";
var_dump(unserialize(pack('H*', '81c0da00234d6573736167655061636b695c6175746f6c6f61645f6e6f745f617661696c61626c65')));

?>
===DONE===
<?php exit(0); ?>
--EXPECTF--
===O1===
MessagePacki\TestOld::__sleep()
string(46) "81c0b44d6573736167655061636b695c546573744f6c64"
MessagePacki\TestOld::__wakeup()
object(MessagePacki\TestOld)#%d (0) {
}
===N1===
MessagePacki\TestNew::serialize()
string(2) "c0"
NULL
===N2===
MessagePacki\TestNew::serialize()
string(52) "82c003b44d6573736167655061636b695c546573744e6577a132"
MessagePacki\TestNew::unserialize()
object(MessagePacki\TestNew)#%d (0) {
}
===NAOld===
MessagePacki\unserializer(MessagePacki\TestNAOld)
MessagePacki\TestOld::__wakeup()
object(MessagePacki\TestNAOld)#%d (0) {
}
===NANew===
MessagePacki\unserializer(MessagePacki\TestNANew)
MessagePacki\TestNew::__wakeup()
object(MessagePacki\TestNANew)#%d (0) {
}
===NANew2===
MessagePacki\unserializer(MessagePacki\TestNANew2)
MessagePacki\TestNew::unserialize()
object(MessagePacki\TestNANew2)#%d (0) {
}
===AutoOld===
MessagePacki\unserializer(MessagePacki\autoload_implements)
Try __autoload()
MessagePacki\do_autoload(autoload_interface)
MessagePacki\do_autoload(autoload_implements)
object(MessagePacki\autoload_implements)#%d (0) {
}
===AutoNA===
MessagePacki\do_autoload(MessagePacki\autoload_not_available)
MessagePacki\unserializer(MessagePacki\autoload_not_available)
Try __autoload()
MessagePacki\do_autoload(MessagePacki\autoload_not_available)
MessagePacki\do_autoload(MessagePacki\autoload_not_available)

Warning: MessagePack unserialize: Function MessagePacki\unserializer() hasn't defined the class it was called for in %s005.php on line %d
object(__PHP_Incomplete_Class)#%d (1) {
  ["__PHP_Incomplete_Class_Name"]=>
  string(35) "MessagePacki\autoload_not_available"
}
===DONE===
