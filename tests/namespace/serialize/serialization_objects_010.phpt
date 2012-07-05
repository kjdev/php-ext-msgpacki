--TEST--
Serialize() must return a string or NULL
--SKIPIF--
<?php if (!interface_exists('Serializable')) die('skip Interface Serialzable not defined'); ?>
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

Class C implements \Serializable {
    public function serialize() {
        return $this;
    }

    public function unserialize($blah) {
    }
}

try {
    var_dump(bin2hex(serialize(new C)));
} catch (\Exception $e) {
    echo $e->getMessage(). "\n";
}

echo "Done";
?>
--EXPECTF--
MessagePacki\C::serialize() must return a string or NULL
Done
