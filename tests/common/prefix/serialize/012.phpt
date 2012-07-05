--TEST--
msgpacki_unserialize() produces lowercase classnames
--SKIPIF--
<?php
    if (version_compare(zend_version(), '2.0.0-dev', '<')) die('skip ZendEngine 2 needed');
    if (class_exists('autoload_root')) die('skip Autoload test classes exist already');
?>
--FILE--
<?php


if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

ini_set('unserialize_callback_func', 'check');

function check($name) {
    var_dump($name);
    throw new exception;
}

try {
    @msgpacki_unserialize(pack('H*', '81c0a3464f4f'));
}
catch (Exception $e) {
    /* ignore */
}

?>
--EXPECTF--
string(3) "FOO"
