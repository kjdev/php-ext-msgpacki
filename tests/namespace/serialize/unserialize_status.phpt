--TEST--
MessagePacki\unserialize status
--INI--
error_reporting=0
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$data = array(
    'ce7fffffff',
    'd280000001',
    'cb3ff1f9add3739636',
    'cb3ff0000000000000',
    'cb0000000000000000',
    'cbbff0000000000000',
    'cbbff1f9add3739636',
    'a568616c6c6f',
    '86000101cb3ff199999999999a02a568616c6c6f03c004c30590',
    '82c0a174a161a568616c6c6f',
);

foreach ($data as $val) {
    echo "data: $val\n";
    echo "success:\n";
    $data = unserialize(pack('H*', $val), $status);
    var_dump($data);
    var_dump($status);
    echo "failure: (less)\n";
    $data = unserialize(pack('H*', substr($val, 1)), $status);
    var_dump($data);
    var_dump($status);
    echo "failure: (over)\n";
    $data = unserialize(pack('H*', $val . 'c0'), $status);
    var_dump($data);
    var_dump($status);
    echo "\n";
}
?>
DONE
--EXPECTF--
data: ce7fffffff
success:
int(2147483647)
bool(true)
failure: (less)
bool(false)
bool(false)
failure: (over)
bool(false)
bool(false)

data: d280000001
success:
int(-2147483647)
bool(true)
failure: (less)
bool(false)
bool(false)
failure: (over)
bool(false)
bool(false)

data: cb3ff1f9add3739636
success:
float(1.123456789)
bool(true)
failure: (less)
bool(false)
bool(false)
failure: (over)
bool(false)
bool(false)

data: cb3ff0000000000000
success:
float(1)
bool(true)
failure: (less)
bool(false)
bool(false)
failure: (over)
bool(false)
bool(false)

data: cb0000000000000000
success:
float(0)
bool(true)
failure: (less)
bool(false)
bool(false)
failure: (over)
bool(false)
bool(false)

data: cbbff0000000000000
success:
float(-1)
bool(true)
failure: (less)
bool(false)
bool(false)
failure: (over)
bool(false)
bool(false)

data: cbbff1f9add3739636
success:
float(-1.123456789)
bool(true)
failure: (less)
bool(false)
bool(false)
failure: (over)
bool(false)
bool(false)

data: a568616c6c6f
success:
string(5) "hallo"
bool(true)
failure: (less)
bool(false)
bool(false)
failure: (over)
bool(false)
bool(false)

data: 86000101cb3ff199999999999a02a568616c6c6f03c004c30590
success:
array(6) {
  [0]=>
  int(1)
  [1]=>
  float(1.1)
  [2]=>
  string(5) "hallo"
  [3]=>
  NULL
  [4]=>
  bool(true)
  [5]=>
  array(0) {
  }
}
bool(true)
failure: (less)
bool(false)
bool(false)
failure: (over)
bool(false)
bool(false)

data: 82c0a174a161a568616c6c6f
success:
object(__PHP_Incomplete_Class)#%d (2) {
  ["__PHP_Incomplete_Class_Name"]=>
  string(1) "t"
  ["a"]=>
  string(5) "hallo"
}
bool(true)
failure: (less)
bool(false)
bool(false)
failure: (over)
bool(false)
bool(false)

DONE
