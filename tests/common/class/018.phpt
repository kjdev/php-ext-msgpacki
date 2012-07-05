--TEST--
MessagePacki instance set_mode
--FILE--
<?php

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$values = array();

$val = array(1, 2, 3);
$values['array'] = array('val' => $val, 'valid' => $val);

$val = array('k1' => 'a', 'k2' => 'b', 'k3' => 'c');
$values['map'] = array('val' => $val, 'valid' => $val);

$val = array_combine(array(2, 3, 4), array('a', 'b', 'c'));
$values['custom(1)'] =  array('val' => $val, 'valid' => $val);

$val = array('flags' => array(0 => 16777216, 2 => 4));
$val['flags'][1] = 65536;
$values['custom(2)'] =  array('val' => $val, 'valid' => $val);

$val = array('key' => 2, 1 => 3);
$values['custom(3)'] =  array('val' => $val, 'valid' => $val);

//PHP class
$val = new stdClass();
$values['stdClass(empty)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

class C1 extends stdClass {}
$val = new C1();
$values['C1(empty)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

$val = new stdClass();
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
$a = new stdClass();
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
class C2 implements Serializable {
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

//PHP object
function val_obj($n = 5) {
    $a = new stdClass();
    $a->{0} = 1;
    $val = array();
    for ($i = 0; $i < $n; $i++) {
        $val['k' . $i] = $a;
    }
    return $val;
}

//PHP class properties
class C3 {
    public $a = 'Pub:a';
    protected $b = 'Pro:b';
    private $c = 'Pri:c';
}
$val = new C3;
$values['object(class)'] =
    array('val' => $val, 'valid' => $val, 'equal' => false);

$m_php = new MessagePacki(MSGPACKI_MODE_PHP);
$m_origin = new MessagePacki(MSGPACKI_MODE_ORIGIN);


foreach ($values as $key => $value) {
    if (!array_key_exists('valid', $value)) {
        echo "\n== ERROR ==\n";
        continue;
    }

    $valid = $value['valid'];

    if (array_key_exists('val', $value)) {
        $val = $value['val'];
    } else {
        echo "\n== ERROR ==\n";
        continue;
    }

    if (array_key_exists('debug', $value)) {
        $debug = (bool)$value['debug'];
    }

    if (array_key_exists('equal', $value)) {
        $equal = (bool)$value['equal'];
    } else {
        $equal = true;
    }

    echo "\n-- $key --\n";
    var_dump($val);

    for ($i = 0; $i < 2; $i++) {
        if ($i == 0) {
            echo "[mode:MSGPACKI_MODE_PHP]\n";
            $m = $m_php;
        } else {
            echo "[mode:MSGPACKI_MODE_ORIGIN]\n";
            $m = $m_origin;
        }

        //serialize
        $ses = $m->pack($val);
        var_dump(bin2hex($ses));
        $uns = $m->unpack($ses);
        var_dump($uns);
        if ($equal === true) {
            if ($valid === $uns) {
                echo "SUCCESS\n";
            } else {
                echo "FAILED\n";
            }
        } else {
            if ($valid == $uns) {
                echo "SUCCESS\n";
            } else {
                echo "FAILED\n";
            }
        }
    }
}
?>
===DONE===
--EXPECTF--
-- array --
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
}
[mode:MSGPACKI_MODE_PHP]
string(14) "83000101020203"
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(8) "93010203"
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
}
SUCCESS

-- map --
array(3) {
  ["k1"]=>
  string(1) "a"
  ["k2"]=>
  string(1) "b"
  ["k3"]=>
  string(1) "c"
}
[mode:MSGPACKI_MODE_PHP]
string(32) "83a26b31a161a26b32a162a26b33a163"
array(3) {
  ["k1"]=>
  string(1) "a"
  ["k2"]=>
  string(1) "b"
  ["k3"]=>
  string(1) "c"
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(32) "83a26b31a161a26b32a162a26b33a163"
array(3) {
  ["k1"]=>
  string(1) "a"
  ["k2"]=>
  string(1) "b"
  ["k3"]=>
  string(1) "c"
}
SUCCESS

-- custom(1) --
array(3) {
  [2]=>
  string(1) "a"
  [3]=>
  string(1) "b"
  [4]=>
  string(1) "c"
}
[mode:MSGPACKI_MODE_PHP]
string(20) "8302a16103a16204a163"
array(3) {
  [2]=>
  string(1) "a"
  [3]=>
  string(1) "b"
  [4]=>
  string(1) "c"
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(20) "8302a16103a16204a163"
array(3) {
  [2]=>
  string(1) "a"
  [3]=>
  string(1) "b"
  [4]=>
  string(1) "c"
}
SUCCESS

-- custom(2) --
array(1) {
  ["flags"]=>
  array(3) {
    [0]=>
    int(16777216)
    [2]=>
    int(4)
    [1]=>
    int(65536)
  }
}
[mode:MSGPACKI_MODE_PHP]
string(44) "81a5666c6167738300ce01000000020401ce00010000"
array(1) {
  ["flags"]=>
  array(3) {
    [0]=>
    int(16777216)
    [2]=>
    int(4)
    [1]=>
    int(65536)
  }
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(38) "81a5666c61677393ce01000000ce0001000004"
array(1) {
  ["flags"]=>
  array(3) {
    [0]=>
    int(16777216)
    [1]=>
    int(65536)
    [2]=>
    int(4)
  }
}
FAILED

-- custom(3) --
array(2) {
  ["key"]=>
  int(2)
  [1]=>
  int(3)
}
[mode:MSGPACKI_MODE_PHP]
string(16) "82a36b6579020103"
array(2) {
  ["key"]=>
  int(2)
  [1]=>
  int(3)
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(16) "82a36b6579020103"
array(2) {
  ["key"]=>
  int(2)
  [1]=>
  int(3)
}
SUCCESS

-- stdClass(empty) --
object(stdClass)#%d (0) {
}
[mode:MSGPACKI_MODE_PHP]
string(22) "81c0a8737464436c617373"
object(stdClass)#%d (0) {
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(2) "80"
array(0) {
}
FAILED

-- C1(empty) --
object(C1)#%d (0) {
}
[mode:MSGPACKI_MODE_PHP]
string(10) "81c0a24331"
object(C1)#%d (0) {
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(2) "80"
array(0) {
}
FAILED

-- stdClass --
object(stdClass)#%d (3) {
  ["a"]=>
  string(1) "A"
  ["0"]=>
  int(0)
  ["array"]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
}
[mode:MSGPACKI_MODE_PHP]
string(56) "84c0a8737464436c617373a161a141a13000a561727261798100a161"
object(stdClass)#%d (3) {
  ["a"]=>
  string(1) "A"
  ["0"]=>
  int(0)
  ["array"]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(34) "83a161a141a13000a5617272617991a161"
array(3) {
  ["a"]=>
  string(1) "A"
  [0]=>
  int(0)
  ["array"]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
}
FAILED

-- C1 --
object(C1)#%d (3) {
  ["a"]=>
  string(1) "A"
  ["0"]=>
  int(0)
  ["array"]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
}
[mode:MSGPACKI_MODE_PHP]
string(44) "84c0a24331a161a141a13000a561727261798100a161"
object(C1)#%d (3) {
  ["a"]=>
  string(1) "A"
  ["0"]=>
  int(0)
  ["array"]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(34) "83a161a141a13000a5617272617991a161"
array(3) {
  ["a"]=>
  string(1) "A"
  [0]=>
  int(0)
  ["array"]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
}
FAILED

-- array(1) --
array(2) {
  [0]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
  [1]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
}
[mode:MSGPACKI_MODE_PHP]
string(22) "82008100a161018100a161"
array(2) {
  [0]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
  [1]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(14) "9291a16191a161"
array(2) {
  [0]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
  [1]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
}
SUCCESS

-- array(refer) --
array(2) {
  [0]=>
  &object(stdClass)#%d (0) {
  }
  [1]=>
  &object(stdClass)#%d (0) {
  }
}
[mode:MSGPACKI_MODE_PHP]
string(38) "820081c0a8737464436c6173730182c0010002"
array(2) {
  [0]=>
  &object(stdClass)#%d (0) {
  }
  [1]=>
  &object(stdClass)#%d (0) {
  }
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(6) "928080"
array(2) {
  [0]=>
  array(0) {
  }
  [1]=>
  array(0) {
  }
}
FAILED

-- array(refer2) --
array(3) {
  [0]=>
  &object(stdClass)#%d (0) {
  }
  [1]=>
  array(2) {
    [0]=>
    string(1) "a"
    [1]=>
    string(1) "b"
  }
  [2]=>
  &object(stdClass)#%d (0) {
  }
}
[mode:MSGPACKI_MODE_PHP]
string(54) "830081c0a8737464436c617373018200a16101a1620282c0010002"
array(3) {
  [0]=>
  &object(stdClass)#%d (0) {
  }
  [1]=>
  array(2) {
    [0]=>
    string(1) "a"
    [1]=>
    string(1) "b"
  }
  [2]=>
  &object(stdClass)#%d (0) {
  }
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(16) "938092a161a16280"
array(3) {
  [0]=>
  array(0) {
  }
  [1]=>
  array(2) {
    [0]=>
    string(1) "a"
    [1]=>
    string(1) "b"
  }
  [2]=>
  array(0) {
  }
}
FAILED

-- object(1) --
array(2) {
  [0]=>
  object(stdClass)#%d (0) {
  }
  [1]=>
  object(stdClass)#%d (0) {
  }
}
[mode:MSGPACKI_MODE_PHP]
string(38) "820081c0a8737464436c6173730182c0020002"
array(2) {
  [0]=>
  object(stdClass)#%d (0) {
  }
  [1]=>
  object(stdClass)#%d (0) {
  }
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(6) "928080"
array(2) {
  [0]=>
  array(0) {
  }
  [1]=>
  array(0) {
  }
}
FAILED

-- object(refer) --
array(2) {
  [0]=>
  &object(stdClass)#%d (0) {
  }
  [1]=>
  &object(stdClass)#%d (0) {
  }
}
[mode:MSGPACKI_MODE_PHP]
string(38) "820081c0a8737464436c6173730182c0010002"
array(2) {
  [0]=>
  &object(stdClass)#%d (0) {
  }
  [1]=>
  &object(stdClass)#%d (0) {
  }
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(6) "928080"
array(2) {
  [0]=>
  array(0) {
  }
  [1]=>
  array(0) {
  }
}
FAILED

-- object(refer2) --
array(3) {
  [0]=>
  &object(stdClass)#%d (0) {
  }
  [1]=>
  object(C1)#6 (0) {
  }
  [2]=>
  &object(stdClass)#%d (0) {
  }
}
[mode:MSGPACKI_MODE_PHP]
string(50) "830081c0a8737464436c6173730181c0a243310282c0010002"
array(3) {
  [0]=>
  &object(stdClass)#%d (0) {
  }
  [1]=>
  object(C1)#%d (0) {
  }
  [2]=>
  &object(stdClass)#%d (0) {
  }
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(8) "93808080"
array(3) {
  [0]=>
  array(0) {
  }
  [1]=>
  array(0) {
  }
  [2]=>
  array(0) {
  }
}
FAILED

-- object(serializable) --
object(C2)#%d (2) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(2)
}
[mode:MSGPACKI_MODE_PHP]
string(30) "82c003a24332a80000000100000002"
object(C2)#%d (2) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(2)
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(14) "82a16101a16202"
array(2) {
  ["a"]=>
  int(1)
  ["b"]=>
  int(2)
}
FAILED

-- object(class) --
object(C3)#%d (3) {
  ["a"]=>
  string(5) "Pub:a"
  [%r"?b"?:protected"?%r]=>
  string(5) "Pro:b"
  [%r"?c"?:("C3":)?private"?%r]=>
  string(5) "Pri:c"
}
[mode:MSGPACKI_MODE_PHP]
string(72) "84c0a24333a161a55075623a61a4002a0062a550726f3a62a50043330063a55072693a63"
object(C3)#%d (3) {
  ["a"]=>
  string(5) "Pub:a"
  [%r"?b"?:protected"?%r]=>
  string(5) "Pro:b"
  [%r"?c"?:("C3":)?private"?%r]=>
  string(5) "Pri:c"
}
SUCCESS
[mode:MSGPACKI_MODE_ORIGIN]
string(50) "83a161a55075623a61a162a550726f3a62a163a55072693a63"
array(3) {
  ["a"]=>
  string(5) "Pub:a"
  ["b"]=>
  string(5) "Pro:b"
  ["c"]=>
  string(5) "Pri:c"
}
FAILED
===DONE===