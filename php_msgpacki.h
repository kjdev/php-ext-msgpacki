#ifndef PHP_MSGPACKI_H
#define PHP_MSGPACKI_H

#define MSGPACKI_EXT_VERSION "1.0.0"

extern zend_module_entry msgpacki_module_entry;
#define phpext_msgpacki_ptr &msgpacki_module_entry

#ifdef PHP_WIN32
#   define PHP_MSGPACKI_API __declspec(dllexport)
#elif defined(__GNUC__) && __GNUC__ >= 4
#   define PHP_MSGPACKI_API __attribute__ ((visibility("default")))
#else
#   define PHP_MSGPACKI_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

/* NameSpace */
#define PHP_MSGPACKI_NS "MessagePacki"

/* mode */
#define PHP_MSGPACKI_MODE_ORIGIN  (1<<0)
#define PHP_MSGPACKI_MODE_PHP     (1<<1)

/* filter */
#define PHP_MSGPACKI_FILTER_REGISTER         "registers"
#define PHP_MSGPACKI_FILTER_PRE_SERIALIZE    "pre_serialize"
#define PHP_MSGPACKI_FILTER_POST_SERIALIZE   "post_serialize"
#define PHP_MSGPACKI_FILTER_PRE_UNSERIALIZE  "pre_unserialize"
#define PHP_MSGPACKI_FILTER_POST_UNSERIALIZE "post_unserialize"

ZEND_BEGIN_MODULE_GLOBALS(msgpacki)
    unsigned serialize_lock;
    struct {
        void *var_hash;
        unsigned level;
    } serialize;
    struct {
        void *var_hash;
        unsigned level;
    } unserialize;
    struct {
        HashTable *registers;
        HashTable *pre_serialize;
        HashTable *post_serialize;
        HashTable *pre_unserialize;
        HashTable *post_unserialize;
    } filter;
    long mode;
ZEND_END_MODULE_GLOBALS(msgpacki)

#ifdef ZTS
#define MPIG(v) TSRMG(msgpacki_globals_id, zend_msgpacki_globals *, v)
#else
#define MPIG(v) (msgpacki_globals.v)
#endif

/* PHP version define */
#if ZEND_MODULE_API_NO >= 20090626
#define MSGPACKI_ZEND_FUNCTION_ENTRY const zend_function_entry
#else
#define MSGPACKI_ZEND_FUNCTION_ENTRY zend_function_entry
#endif

/* Windows config */
#ifdef PHP_WIN32
#  include "config-win.h"
#endif

#endif  /* PHP_MSGPACKI_H */
