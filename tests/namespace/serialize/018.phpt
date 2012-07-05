--TEST--
Serializable interface breaks object references
--FILE--
<?php

namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

echo "-TEST\n";

class a implements \Serializable {
    function serialize() {
        return serialize(get_object_vars($this));
    }
    function unserialize($s) {
        foreach (unserialize($s) as $p=>$v) {
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

$s = serialize($c);
printf("%s\n", bin2hex($s));

$d = unserialize($s);

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
82c003ae4d6573736167655061636b695c63da004b81a16182c003ae4d6573736167655061636b695c61da003383a16282c003ae4d6573736167655061636b695c62af82a16382c0020001a16182c0020003a16382c0020001a16182c0020003
bool(true)
bool(true)
bool(true)
bool(true)
MessagePacki\c Object
(
    [a] => MessagePacki\a Object
        (
            [b] => MessagePacki\b Object
                (
                    [c] => MessagePacki\c Object
 *RECURSION*
                    [a] => MessagePacki\a Object
 *RECURSION*
                )

            [c] => MessagePacki\c Object
 *RECURSION*
            [a] => MessagePacki\a Object
 *RECURSION*
        )

)
Done
