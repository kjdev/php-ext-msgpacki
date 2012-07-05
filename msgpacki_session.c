
#include "php.h"
#include "php_ini.h"
#include "ext/session/php_session.h"

#include "php_verdep.h"
#include "php_msgpacki.h"
#include "msgpacki_session.h"
#include "msgpacki_function.h"
/* #include "msgpacki_debug.h" */

ZEND_EXTERN_MODULE_GLOBALS(msgpacki);

PS_SERIALIZER_FUNCS(msgpacki);

PHP_MSGPACKI_API int
msgpacki_register_session(TSRMLS_D)
{
#if HAVE_PHP_SESSION
    php_session_register_serializer("msgpacki",
                                    PS_SERIALIZER_ENCODE_NAME(msgpacki),
                                    PS_SERIALIZER_DECODE_NAME(msgpacki));
#endif
    return SUCCESS;
}

PS_SERIALIZER_ENCODE_FUNC(msgpacki)
{
    msgpacki_serialize_data_t var_hash;
    msgpacki_buffer_t buf = {0};

    MSGPACKI_SERIALIZE_INIT(var_hash);

    msgpacki_serialize_session(&buf, &var_hash TSRMLS_CC);

    if (newlen) {
        *newlen = buf.len;
    }
    *newstr = buf.c;


    MSGPACKI_SERIALIZE_DESTROY(var_hash);

    return SUCCESS;
}

PS_SERIALIZER_DECODE_FUNC(msgpacki)
{
    zval *retval;
    const unsigned char *p;
    msgpacki_unserialize_data_t var_hash;

    ALLOC_INIT_ZVAL(retval);

    p = (const unsigned char*)val;
    MSGPACKI_UNSERIALIZE_INIT(var_hash);

    if (MPIG(unserialize).level == 1) {
        msgpacki_unserialize_push(&var_hash, &retval);
    }

    if (msgpacki_unserialize(&retval, &p, p + vallen,
                             PHP_MSGPACKI_MODE_PHP, &var_hash TSRMLS_CC) &&
        ((char*)p - val) == vallen && Z_TYPE_P(retval) == IS_ARRAY) {
        zval **value;
        char *key;
        ulong index;
        uint key_len;
        HashPosition pos;
        char tmp[128];
        zend_hash_internal_pointer_reset_ex(HASH_OF(retval), &pos);
        while (zend_hash_get_current_data_ex(HASH_OF(retval), (void**)&value,
                                             &pos) == SUCCESS) {
            switch (zend_hash_get_current_key_ex(HASH_OF(retval), &key,
                                                 &key_len, &index, 0, &pos)) {
                case HASH_KEY_IS_LONG:
                    key_len = slprintf(tmp, sizeof(tmp), "%ld", index) + 1;
                    key = tmp;
                case HASH_KEY_IS_STRING:
                    php_set_session_var(key, key_len-1, *value, NULL TSRMLS_CC);
                    php_add_session_var(key, key_len-1 TSRMLS_CC);
                    break;
            }
            zend_hash_move_forward_ex(HASH_OF(retval), &pos);
        }
    }

    MSGPACKI_UNSERIALIZE_DESTROY(var_hash);

    zval_ptr_dtor(&retval);

    return SUCCESS;
}
