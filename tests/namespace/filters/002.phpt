--TEST--
class MessagePack Filter
--FILE--
<?php
namespace MessagePacki;

class foo extends Filter {
    function pre_serialize($in) {}
    function post_serialize($in) {}
    function pre_unserialize($in) {}
    function post_unserialize($in) {}
}
class bar extends Filter {
    function pre_serialize() {}
    function post_serialize() {}
    function pre_unserialize() {}
    function post_unserialize() {}
}
?>
--EXPECTF--
Strict Standards: Declaration of MessagePacki\bar::pre_serialize() should be compatible %s MessagePacki_Filter::pre_serialize%s in %s on line %d

Strict Standards: Declaration of MessagePacki\bar::post_serialize() should be compatible %s MessagePacki_Filter::post_serialize%s in %s on line %d

Strict Standards: Declaration of MessagePacki\bar::pre_unserialize() should be compatible %s MessagePacki_Filter::pre_unserialize%s in %s on line %d

Strict Standards: Declaration of MessagePacki\bar::post_unserialize() should be compatible %s MessagePacki_Filter::post_unserialize%s in %s on line %d
