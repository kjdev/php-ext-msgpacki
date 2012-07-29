#ifndef MSGPACKI_FUNCTION_H
#define MSGPACKI_FUNCTION_H

#include "ext/standard/php_smart_str.h"

typedef HashTable* msgpacki_serialize_data_t;

struct msgpacki_unserialize_data {
    void *first;
    void *last;
    void *first_dtor;
    void *last_dtor;
};

typedef struct msgpacki_unserialize_data* msgpacki_unserialize_data_t;

typedef smart_str msgpacki_buffer_t;

PHP_MSGPACKI_API void
msgpacki_serialize(msgpacki_buffer_t *buf, zval **struc, long mode,
                   msgpacki_serialize_data_t *var_hash TSRMLS_DC);
PHP_MSGPACKI_API void
msgpacki_serialize_session(msgpacki_buffer_t *buf,
                           msgpacki_serialize_data_t *var_hash TSRMLS_DC);

PHP_MSGPACKI_API int
msgpacki_unserialize(zval **rval, const unsigned char **p,
                     const unsigned char *max, long mode,
                     msgpacki_unserialize_data_t *var_hash TSRMLS_DC);
PHP_MSGPACKI_API void
msgpacki_unserialize_destroy(msgpacki_unserialize_data_t *var_hash);
PHP_MSGPACKI_API void
msgpacki_unserialize_push(msgpacki_unserialize_data_t *var_hashx, zval **rval);

ZEND_FUNCTION(msgpacki_serialize);
ZEND_FUNCTION(msgpacki_unserialize);
ZEND_FUNCTION(msgpacki_encode);
ZEND_FUNCTION(msgpacki_decode);

#define MSGPACKI_SERIALIZE_INIT(var_hash_ptr) \
do  { \
    if (MSGPACKI_G(serialize_lock) || !MSGPACKI_G(serialize).level) { \
        ALLOC_HASHTABLE(var_hash_ptr); \
        zend_hash_init((var_hash_ptr), 10, NULL, NULL, 0); \
        if (!MSGPACKI_G(serialize_lock)) { \
            MSGPACKI_G(serialize).var_hash = (void *)(var_hash_ptr); \
            MSGPACKI_G(serialize).level = 1; \
        } \
    } else { \
        (var_hash_ptr) = (msgpacki_serialize_data_t)MSGPACKI_G(serialize).var_hash; \
        ++MSGPACKI_G(serialize).level; \
    } \
} while(0)

#define MSGPACKI_SERIALIZE_DESTROY(var_hash_ptr) \
do { \
    if (MSGPACKI_G(serialize_lock) || !MSGPACKI_G(serialize).level) { \
        zend_hash_destroy((var_hash_ptr)); \
        FREE_HASHTABLE(var_hash_ptr); \
    } else { \
        if (!--MSGPACKI_G(serialize).level) { \
            zend_hash_destroy((msgpacki_serialize_data_t)MSGPACKI_G(serialize).var_hash); \
            FREE_HASHTABLE((msgpacki_serialize_data_t)MSGPACKI_G(serialize).var_hash); \
            MSGPACKI_G(serialize).var_hash = NULL; \
        } \
    } \
} while (0)

#define MSGPACKI_UNSERIALIZE_INIT(var_hash_ptr) \
do { \
    if (MSGPACKI_G(serialize_lock) || !MSGPACKI_G(unserialize).level) { \
        (var_hash_ptr) = (msgpacki_unserialize_data_t)ecalloc(1, sizeof(struct msgpacki_unserialize_data)); \
        if (!MSGPACKI_G(serialize_lock)) { \
            MSGPACKI_G(unserialize).var_hash = (void *)(var_hash_ptr); \
            MSGPACKI_G(unserialize).level = 1; \
        } \
    } else { \
        (var_hash_ptr) = (msgpacki_unserialize_data_t)MSGPACKI_G(unserialize).var_hash; \
        ++MSGPACKI_G(unserialize).level; \
    } \
} while (0)

#define MSGPACKI_UNSERIALIZE_DESTROY(var_hash_ptr) \
do { \
    if (MSGPACKI_G(serialize_lock) || !MSGPACKI_G(unserialize).level) { \
        msgpacki_unserialize_destroy(&(var_hash_ptr)); \
        efree(var_hash_ptr); \
    } else { \
        if (!--MSGPACKI_G(unserialize).level) { \
            msgpacki_unserialize_destroy(&(var_hash_ptr)); \
            efree((var_hash_ptr)); \
            MSGPACKI_G(unserialize).var_hash = NULL; \
        } \
    } \
} while (0)

#endif  /* MSGPACKI_FUNCTION_H */
