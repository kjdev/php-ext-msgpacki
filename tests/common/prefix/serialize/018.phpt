--TEST--
Serializable interface breaks object references
--FILE--
<?php

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

echo "-TEST\n";

class a implements Serializable {
    function serialize() {
        return msgpacki_serialize(get_object_vars($this));
    }
    function unserialize($s) {
        foreach (msgpacki_unserialize($s) as $p=>$v) {
            $this->$p=$v;
        }
    }
}
class b extends a {}
class c extends b {}

$c = new c;
$c->a = new a;
$c->a->b = new b;
$c->a->b->c = $c;
$c->a->c = $c;
$c->a->b->a = $c->a;
$c->a->a = $c->a;

$s = msgpacki_serialize($c);
printf("%s\n", bin2hex($s));

$d = msgpacki_unserialize($s);

var_dump(
    $d === $d->a->b->c,
    $d->a->a === $d->a,
    $d->a->b->a === $d->a,
    $d->a->c === $d
);

print_r($d);

echo "Done\n";

?>
--EXPECTF--
%aTEST
82c003a163da003181a16182c003a161da002683a16282c003a162af82a16382c0020001a16182c0020003a16382c0020001a16182c0020003
bool(true)
bool(true)
bool(true)
bool(true)
c Object
(
    [a] => a Object
        (
            [b] => b Object
                (
                    [c] => c Object
 *RECURSION*
                    [a] => a Object
 *RECURSION*
                )

            [c] => c Object
 *RECURSION*
            [a] => a Object
 *RECURSION*
        )

)
Done
