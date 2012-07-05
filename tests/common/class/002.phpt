--TEST--
MessagePacki::get_mode()/MessagePacki::set_mode()
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

echo "-- default --\n";
$m = new MessagePacki();
var_dump($m->get_mode());

echo "-- set_mode --\n";
echo "(1)\n";
var_dump($m->set_mode(1));
var_dump($m->get_mode());

echo "(123)\n";
var_dump($m->set_mode(123));
var_dump($m->get_mode());

echo "()\n";
var_dump($m->set_mode());
var_dump($m->get_mode());

echo "(a)\n";
var_dump($m->set_mode('a'));
var_dump($m->get_mode());

echo "('345')\n";
var_dump($m->set_mode('345'));
var_dump($m->get_mode());

echo "(null)\n";
var_dump($m->set_mode(null));
var_dump($m->get_mode());

echo "(array)\n";
var_dump($m->set_mode(array()));
var_dump($m->get_mode());

echo "(MODE_PHP)\n";
var_dump($m->set_mode(MSGPACKI_MODE_PHP));
var_dump($m->get_mode());

echo "(MODE_ORIGIN)\n";
var_dump($m->set_mode(MSGPACKI_MODE_ORIGIN));
var_dump($m->get_mode());

echo "-- construct --\n";
echo "(1)\n";
try {
    $m = new MessagePacki(1);
    var_dump($m->get_mode());
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}

echo "(123)\n";
try {
    $m = new MessagePacki(123);
    var_dump($m->get_mode());
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}

echo "(a)\n";
try {
    $m = new MessagePacki('a');
    var_dump($m->get_mode());
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}

echo "('345')\n";
try {
    $m = new MessagePacki('345');
    var_dump($m->get_mode());
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}

echo "(null)\n";
try {
    $m = new MessagePacki(null);
    var_dump($m->get_mode());
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}

echo "(array)\n";
try {
    $m = new MessagePacki(array());
    var_dump($m->get_mode());
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}

echo "(MODE_PHP)\n";
try {
    $m = new MessagePacki(MSGPACKI_MODE_PHP);
    var_dump($m->get_mode());
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}

echo "(MODE_ORIGIN)\n";
try {
    $m = new MessagePacki(MSGPACKI_MODE_ORIGIN);
    var_dump($m->get_mode());
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}
?>
===DONE===
--EXPECTF--
-- default --
int(2)
-- set_mode --
(1)
bool(true)
int(1)
(123)
bool(true)
int(123)
()

Warning: MessagePacki::set_mode() expects exactly 1 parameter, 0 given in %s on line %d
bool(false)
int(123)
(a)

Warning: MessagePacki::set_mode() expects parameter 1 to be long, string given in %s on line %d
bool(false)
int(123)
('345')
bool(true)
int(345)
(null)
bool(true)
int(0)
(array)

Warning: MessagePacki::set_mode() expects parameter 1 to be long, array given in %s on line %d
bool(false)
int(0)
(MODE_PHP)
bool(true)
int(2)
(MODE_ORIGIN)
bool(true)
int(1)
-- construct --
(1)
int(1)
(123)
int(123)
(a)
MessagePacki::__construct() expects parameter 1 to be long, string given
('345')
int(345)
(null)
int(0)
(array)
MessagePacki::__construct() expects parameter 1 to be long, array given
(MODE_PHP)
int(2)
(MODE_ORIGIN)
int(1)
===DONE===
