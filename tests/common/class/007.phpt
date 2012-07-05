--TEST--
MessagePacki in serialisation of circular references
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

class a {
    public $b;
}
class b {
    public $c;
}
class c {
    public $d;
}
$a = new a();
$a->b = new b();
$a->b->c = new c();
$a->b->c->d = $a;

$m = new MessagePacki();
var_dump($m->unpack($m->pack($a)));
?>
--EXPECTF--
object(a)#%d (1) {
  ["b"]=>
  object(b)#%d (1) {
    ["c"]=>
    object(c)#%d (1) {
      ["d"]=>
      *RECURSION*
    }
  }
}
