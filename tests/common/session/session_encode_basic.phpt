--TEST--
Test session_encode() function : basic functionality
--INI--
serialize_precision=100
session.serialize_handler=msgpacki
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

ob_start();

/*
 * Prototype : string session_encode(void)
 * Description : Encodes the current session data as a string
 * Source code : ext/session/session.c
 */

echo "*** Testing session_encode() : basic functionality ***\n";

// Get an unset variable
$unset_var = 10;
unset($unset_var);

class classA
{
    public function __toString() {
        return "Hello World!";
    }
}

$heredoc = <<<EOT
Hello World!
EOT;

$fp = fopen(__FILE__, "r");

// Unexpected values to be passed as arguments
$inputs = array(

       // Integer data
/*1*/  0,
       1,
       12345,
       -2345,

       // Float data
/*5*/  10.5,
       -10.5,
       12.3456789000e10,
       12.3456789000E-10,
       .5,

       // Null data
/*10*/ NULL,
       null,

       // Boolean data
/*12*/ true,
       false,
       TRUE,
       FALSE,

       // Empty strings
/*16*/ "",
       '',

       // Invalid string data
/*18*/ "Nothing",
       'Nothing',
       $heredoc,

       // Object data
/*21*/ new classA(),

       // Undefined data
/*22*/ @$undefined_var,

       // Unset data
/*23*/ @$unset_var,

       // Resource variable
/*24*/ $fp
);

var_dump(session_start());
$iterator = 1;
foreach($inputs as $input) {
    echo "\n-- Iteration $iterator --\n";
    $_SESSION["data"] = $input;
    $val = session_encode();
    var_dump(bin2hex($val));
    var_dump(msgpacki_unserialize($val));
    $iterator++;
};

var_dump(session_destroy());
fclose($fp);
echo "Done";
ob_end_flush();
?>
--EXPECTF--
*** Testing session_encode() : basic functionality ***
bool(true)

-- Iteration 1 --
string(14) "81a46461746100"
array(1) {
  ["data"]=>
  int(0)
}

-- Iteration 2 --
string(14) "81a46461746101"
array(1) {
  ["data"]=>
  int(1)
}

-- Iteration 3 --
string(18) "81a464617461cd3039"
array(1) {
  ["data"]=>
  int(12345)
}

-- Iteration 4 --
string(18) "81a464617461d1f6d7"
array(1) {
  ["data"]=>
  int(-2345)
}

-- Iteration 5 --
string(30) "81a464617461cb4025000000000000"
array(1) {
  ["data"]=>
  float(10.5)
}

-- Iteration 6 --
string(30) "81a464617461cbc025000000000000"
array(1) {
  ["data"]=>
  float(-10.5)
}

-- Iteration 7 --
string(30) "81a464617461cb423cbe991a080000"
array(1) {
  ["data"]=>
  float(123456789000)
}

-- Iteration 8 --
string(30) "81a464617461cb3e1535afdf51cc65"
array(1) {
  ["data"]=>
  float(1.23456789E-9)
}

-- Iteration 9 --
string(30) "81a464617461cb3fe0000000000000"
array(1) {
  ["data"]=>
  float(0.5)
}

-- Iteration 10 --
string(14) "81a464617461c0"
array(1) {
  ["data"]=>
  NULL
}

-- Iteration 11 --
string(14) "81a464617461c0"
array(1) {
  ["data"]=>
  NULL
}

-- Iteration 12 --
string(14) "81a464617461c3"
array(1) {
  ["data"]=>
  bool(true)
}

-- Iteration 13 --
string(14) "81a464617461c2"
array(1) {
  ["data"]=>
  bool(false)
}

-- Iteration 14 --
string(14) "81a464617461c3"
array(1) {
  ["data"]=>
  bool(true)
}

-- Iteration 15 --
string(14) "81a464617461c2"
array(1) {
  ["data"]=>
  bool(false)
}

-- Iteration 16 --
string(14) "81a464617461a0"
array(1) {
  ["data"]=>
  string(0) ""
}

-- Iteration 17 --
string(14) "81a464617461a0"
array(1) {
  ["data"]=>
  string(0) ""
}

-- Iteration 18 --
string(28) "81a464617461a74e6f7468696e67"
array(1) {
  ["data"]=>
  string(7) "Nothing"
}

-- Iteration 19 --
string(28) "81a464617461a74e6f7468696e67"
array(1) {
  ["data"]=>
  string(7) "Nothing"
}

-- Iteration 20 --
string(38) "81a464617461ac48656c6c6f20576f726c6421"
array(1) {
  ["data"]=>
  string(12) "Hello World!"
}

-- Iteration 21 --
string(30) "81a46461746181c0a6636c61737341"
array(1) {
  ["data"]=>
  object(classA)#%d (0) {
  }
}

-- Iteration 22 --
string(14) "81a464617461c0"
array(1) {
  ["data"]=>
  NULL
}

-- Iteration 23 --
string(14) "81a464617461c0"
array(1) {
  ["data"]=>
  NULL
}

-- Iteration 24 --
string(14) "81a46461746100"
array(1) {
  ["data"]=>
  int(0)
}
bool(true)
Done
