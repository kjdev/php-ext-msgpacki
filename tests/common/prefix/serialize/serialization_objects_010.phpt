--TEST--
Serialize() must return a string or NULL
--SKIPIF--
<?php if (!interface_exists('Serializable')) die('skip Interface Serialzable not defined'); ?>
--FILE--
<?php

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

Class C implements Serializable {
    public function serialize() {
        return $this;
    }

    public function unserialize($blah) {
    }
}

try {
    var_dump(bin2hex(msgpacki_serialize(new C)));
} catch (Exception $e) {
    echo $e->getMessage(). "\n";
}

echo "Done";
?>
--EXPECTF--
C::serialize() must return a string or NULL
Done
