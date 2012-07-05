--TEST--
Test session_decode() function : error functionality
--INI--
session.serialize_handler=msgpacki
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

ob_start();

echo "*** Testing session_decode() : error functionality ***\n";

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
    if (version_compare(PHP_VERSION, '5.3.0') < 0 && $input === $fp) {
        session_decode($input);
        echo "\nWarning: session_decode() expects parameter 1 to be string, resource given in ", __FILE__, " on line ", __LINE__, "\nNULL\n";
    } else {
        var_dump(session_decode($input));
    }
    var_dump($_SESSION);
    $iterator++;
};

var_dump(session_destroy());
fclose($fp);
echo "Done";
ob_end_flush();
?>
--EXPECTF--
*** Testing session_decode() : error functionality ***
bool(true)

-- Iteration 1 --
bool(true)
array(0) {
}

-- Iteration 2 --
bool(true)
array(0) {
}

-- Iteration 3 --
bool(true)
array(0) {
}

-- Iteration 4 --
bool(true)
array(0) {
}

-- Iteration 5 --
bool(true)
array(0) {
}

-- Iteration 6 --
bool(true)
array(0) {
}

-- Iteration 7 --
bool(true)
array(0) {
}

-- Iteration 8 --
bool(true)
array(0) {
}

-- Iteration 9 --
bool(true)
array(0) {
}

-- Iteration 10 --
bool(true)
array(0) {
}

-- Iteration 11 --
bool(true)
array(0) {
}

-- Iteration 12 --
bool(true)
array(0) {
}

-- Iteration 13 --
bool(true)
array(0) {
}

-- Iteration 14 --
bool(true)
array(0) {
}

-- Iteration 15 --
bool(true)
array(0) {
}

-- Iteration 16 --
bool(true)
array(0) {
}

-- Iteration 17 --
bool(true)
array(0) {
}

-- Iteration 18 --
bool(true)
array(0) {
}

-- Iteration 19 --
bool(true)
array(0) {
}

-- Iteration 20 --
bool(true)
array(0) {
}

-- Iteration 21 --
bool(true)
array(0) {
}

-- Iteration 22 --
bool(true)
array(0) {
}

-- Iteration 23 --
bool(true)
array(0) {
}

-- Iteration 24 --

Warning: session_decode() expects parameter 1 to be string, resource given in %s on line %d
NULL
array(0) {
}
bool(true)
Done
