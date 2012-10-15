--TEST--
Check for serialization handler
--SKIPIF--
<?php
if (!extension_loaded("session")) {
    echo "skip needs session enabled";
}
?>
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$output = '';

function open($path, $name) {
    return true;
}

function close() {
    return true;
}

function read($id) {
    global $output;
    return pack('H*', '81a3666f6f01');
}

function write($id, $data) {
    global $output;
    $output .= bin2hex($data). PHP_EOL;
    return true;
}

function destroy($id) {
    return true;
}

function gc($time) {
    return true;
}

ini_set('session.serialize_handler', 'msgpacki');

session_set_save_handler('open', 'close', 'read', 'write', 'destroy', 'gc');

session_start();

echo ++$_SESSION['foo'], PHP_EOL;

session_write_close();

echo $output;
var_dump($_SESSION);
?>
--EXPECT--
2
81a3666f6f02
array(1) {
  ["foo"]=>
  int(2)
}
