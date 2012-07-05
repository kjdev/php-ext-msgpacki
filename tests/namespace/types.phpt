--TEST--
TEST types
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$values = array();

$values['nil'] = array('val' => null, 'valid' => null);
$values['true'] = array('val' => true, 'valid' => true);
$values['false'] = array('val' => false, 'valid' => false);
$values['float'] = array('serialize' => 'ca41480000', 'valid' => 12.5);
$values['double'] = array('val' => 1.234, 'valid' => 1.234);
$values['Positive FixNum'] = array('val' => 5, 'valid' => 5);
$values['uint 8'] = array('val' => 150, 'valid' => 150);
$values['uint 16'] = array('val' => 300, 'valid' => 300);
$values['uint 32'] = array('val' => 765432, 'valid' => 765432);
$values['uint 64'] = array('val' => 9876543210, 'valid' => 9876543210);
$values['Negative FixNum'] = array('val' => -5, 'valid' => -5);
$values['int 8'] = array('val' => -50, 'valid' => -50);
$values['int 16'] = array('val' => -150, 'valid' => -150);
$values['int 32'] = array('val' => -54321, 'valid' => -54321);
$values['int 64'] = array('val' => -9876543210, 'valid' => -9876543210);
$values['FixRaw'] = array('val' => 'a', 'valid' => 'a');
function val_raw($n = 5) {
    $val = '';
    $str = str_split(sha1(srand()));
    for ($i = 0; $i < $n; $i ++) {
        $val .= array_rand($str, 1);
    }
    return $val;
}
$val = val_raw(50);
$values['raw 16'] = array('val' => $val, 'valid' => $val);
$val = val_raw(70000);
$values['raw 32'] = array('val' => $val, 'valid' => $val);
$val = array(1, 2, 3);
$values['FixArray'] = array('val' => $val, 'valid' => $val, 'ini' => MSGPACKI_MODE_ORIGIN);
$val = array_fill(0, 50, 'a');
$values['array 16'] = array('val' => $val, 'valid' => $val, 'ini' => MSGPACKI_MODE_ORIGIN);
$val = array_fill(0, 70000, 'a');
$values['array 32'] = array('val' => $val, 'valid' => $val, 'ini' => MSGPACKI_MODE_ORIGIN);
$val = array('k1' => 'a', 'k2' => 'b', 'k3' => 'c');
$values['FixMap'] = array('val' => $val, 'valid' => $val);
function val_map($n = 5) {
    $val = array();
    for ($i = 0; $i < $n; $i ++) {
        $val['k' . $i] = 'a';
    }
    return $val;
}
$val = val_map(50);
$values['map 16'] = array('val' => $val, 'valid' => $val);
$val = val_map(70000);
$values['map 32'] = array('val' => $val, 'valid' => $val);

$val = array_combine(array(2, 3, 4), array('a', 'b', 'c'));
$values['custom(1)'] =  array('val' => $val, 'valid' => $val);

$val = array('flags' => array(0 => 16777216, 2 => 4));
$val['flags'][1] = 65536;
$values['custom(2)'] =  array('val' => $val, 'valid' => $val);

$val = array('key' => 2, 1 => 3);
$values['custom(3)'] =  array('val' => $val, 'valid' => $val);

$val = array('flags' => array(0 => 16777216, 2 => 4));
$val['flags'][1] = 65536;
$val['flags'][3] = 12345;
$values['custom(4)'] =  array('val' => $val, 'valid' => $val);

//PHP class
$val = new \stdClass();
$values['stdClass(empty)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

class C1 extends \stdClass {}
$val = new C1();
$values['C1(empty)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

$val = new \stdClass();
$val->a = 'A';
$val->{0} = 0;
$val->array = array('a');
$values['stdClass'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

$val = new C1();
$val->a = 'A';
$val->{0} = 0;
$val->array = array('a');
$values['C1'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

//PHP array reference
$a = array('a');
$val = array($a, $a);
$values['array(1)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

$val = array(&$a, &$a);
$values['array(refer)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

$val = array(&$a, array('a', 'b'), &$a);
$values['array(refer2)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

//PHP object reference
$a = new \stdClass();
$val = array($a, $a);
$values['object(1)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

$val = array(&$a, &$a);
$values['object(refer)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

$val = array(&$a, new C1(), &$a);
$values['object(refer2)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

//PHP object serializable
class C2 implements \Serializable {
    var $a;
    var $b;
    public function __construct($a, $b) {
        $this->a = $a;
        $this->b = $b;
    }
    public function serialize() {
        return pack('NN', $this->a, $this->b);
    }
    public function unserialize($serialized) {
        $tmp = unpack('N*', $serialized);
        $this->__construct($tmp[1], $tmp[2]);
    }
}

$val = new C2(1, 2);
$values['object(serializable)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

//PHP object (0xde)
function val_obj($n = 5) {
    $a = new \stdClass();
    $a->{0} = 1;
    $val = array();
    for ($i = 0; $i < $n; $i++) {
        $val['k' . $i] = $a;
    }
    return $val;
}

$val = val_obj(30);
$values['object(map 16)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

$val = val_obj(70000);
$values['object(map 32)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

//PHP class properties
class C3 {
    public $a = 'Pub:a';
    protected $b = 'Pro:b';
    private $c = 'Pri:c';
}
$val = new C3;
$values['class(property)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

foreach ($values as $key => $value) {
    if (!array_key_exists('valid', $value)) {
        continue;
    }

    if (PHP_INT_SIZE == 4) {
        if (strcmp($key, 'uint 64') == 0) {
            echo "uint 64 (uint 64) => SKIP\n";
            continue;
        } else if (strcmp($key, 'int 64') == 0) {
            echo "int 64 (int 64) => SKIP\n";
            continue;
        }
    }

    $valid = $value['valid'];

    $se = '';
    if (array_key_exists('val', $value)) {
        $val = $value['val'];
        $serialize = true;
    } else if (array_key_exists('serialize', $value)) {
        $se = $value['serialize'];
        $serialize = false;
    } else {
        continue;
    }

    if (array_key_exists('equal', $value)) {
        $equal = (bool)$value['equal'];
    } else {
        $equal = true;
    }

    if (array_key_exists('ini', $value)) {
        $ini = ini_get('msgpacki.mode');
        ini_set('msgpacki.mode', $value['ini']);
    } else {
        $ini = null;
    }

    if ($serialize === true) {
        $se = serialize($val);
    } else {
        $se = pack('H*', $se);
    }

    //type
    $bin = substr(bin2hex($se), 0, 2);
    switch ($bin) {
        case 'c0':
            $type = "nil";
            break;
        case 'c2':
            $type = "false";
            break;
        case 'c3':
            $type = "true";
            break;
        case 'ca':
            $type = "float";
            break;
        case 'cb':
            $type = "double";
            break;
        case 'cc':
            $type = "uint 8";
            break;
        case 'cd':
            $type = "uint 16";
            break;
        case 'ce':
            $type = "uint 32";
            break;
        case 'cf':
            $type = "uint 64";
            break;
        case 'd0':
            $type = "int 8";
            break;
        case 'd1':
            $type = "int 16";
            break;
        case 'd2':
            $type = "int 32";
            break;
        case 'd3':
            $type = "int 64";
            break;
        case 'da':
            $type = "raw 16";
            break;
        case 'db':
            $type = "raw 32";
            break;
        case 'dc':
            $type = "array 16";
            break;
        case 'dd':
            $type = "array 32";
            break;
        case 'de':
            $type = "map 16";
            break;
        case 'df':
            $type = "map 32";
            break;
        default:
            if (strcmp($bin, '00') >= 0 && strcmp($bin, '7f') <= 0) {
                $type = "Positive FixNum";
            } else if (strcmp($bin, 'e0') >= 0 && strcmp($bin, 'ff') <= 0) {
                $type = "Negative FixNum";
            } else if (strcmp($bin, 'a0') >= 0 && strcmp($bin, 'bf') <= 0) {
                $type = "FixRaw";
            } else if (strcmp($bin, '90') >= 0 && strcmp($bin, '9f') <= 0) {
                $type = "FixArray";
            } else if (strcmp($bin, '80') >= 0 && strcmp($bin, '8f') <= 0) {
                $type = "FixMap";
            } else {
                $type = "Unknown";
            }
    }

    //unserialize
    $un = unserialize($se);

    //valid
    echo "$key ($type) => ";
    if ($equal === true) {
        if ($valid === $un) {
            echo "SUCCESS\n";
        } else {
            echo "FAILED\n";
        }
    } else {
        if ($valid == $un) {
            echo "SUCCESS\n";
        } else {
            echo "FAILED\n";
        }
    }

    if ($ini) {
        ini_set('msgpacki.mode', $ini);
    }
}
?>
===DONE===
--EXPECTF--
nil (nil) => SUCCESS
true (true) => SUCCESS
false (false) => SUCCESS
float (float) => SUCCESS
double (double) => SUCCESS
Positive FixNum (Positive FixNum) => SUCCESS
uint 8 (uint 8) => SUCCESS
uint 16 (uint 16) => SUCCESS
uint 32 (uint 32) => SUCCESS
uint 64 (uint 64) => S%s
Negative FixNum (Negative FixNum) => SUCCESS
int 8 (int 8) => SUCCESS
int 16 (int 16) => SUCCESS
int 32 (int 32) => SUCCESS
int 64 (int 64) => S%s
FixRaw (FixRaw) => SUCCESS
raw 16 (raw 16) => SUCCESS
raw 32 (raw 32) => SUCCESS
FixArray (FixArray) => SUCCESS
array 16 (array 16) => SUCCESS
array 32 (array 32) => SUCCESS
FixMap (FixMap) => SUCCESS
map 16 (map 16) => SUCCESS
map 32 (map 32) => SUCCESS
custom(1) (FixMap) => SUCCESS
custom(2) (FixMap) => SUCCESS
custom(3) (FixMap) => SUCCESS
custom(4) (FixMap) => SUCCESS
stdClass(empty) (FixMap) => SUCCESS
C1(empty) (FixMap) => SUCCESS
stdClass (FixMap) => SUCCESS
C1 (FixMap) => SUCCESS
array(1) (FixMap) => SUCCESS
array(refer) (FixMap) => SUCCESS
array(refer2) (FixMap) => SUCCESS
object(1) (FixMap) => SUCCESS
object(refer) (FixMap) => SUCCESS
object(refer2) (FixMap) => SUCCESS
object(serializable) (FixMap) => SUCCESS
object(map 16) (map 16) => SUCCESS
object(map 32) (map 32) => SUCCESS
class(property) (FixMap) => SUCCESS
===DONE===
