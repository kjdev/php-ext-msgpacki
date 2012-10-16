#ifndef MSGPACKI_FILTER_H
#define MSGPACKI_FILTER_H

typedef struct msgpacki_filter_data {
    zend_class_entry *ce;
    zval *object;
    char classname[1];
} msgpacki_filter_data_t;

#define MSGPACKI_FILTER_CLASS_NAME "MessagePacki_Filter"

PHP_MSGPACKI_API int msgpacki_register_filters(TSRMLS_D);
PHP_MSGPACKI_API void msgpacki_filter_data_dtor(msgpacki_filter_data_t *data);
PHP_MSGPACKI_API zend_class_entry *msgpacki_filter_get_ce(void);

ZEND_FUNCTION(msgpacki_filter_register);
ZEND_FUNCTION(msgpacki_filter_append);
ZEND_FUNCTION(msgpacki_filter_prepend);
ZEND_FUNCTION(msgpacki_filter_remove);
ZEND_FUNCTION(msgpacki_get_filters);

#define MSGPACKI_FILTER_CALLBACK_FORWARD(type, ht, retval) \
retval = arg; \
zend_hash_internal_pointer_reset_ex(ht, &pos); \
while (zend_hash_get_current_data_ex(ht, (void**)&obj, &pos) == SUCCESS) { \
    if (obj && Z_TYPE_P(obj) == IS_OBJECT) { \
        zval fname, *rval, **args[1]; \
        ZVAL_STRINGL(&fname, #type, sizeof(#type)-1, 0); \
        args[0] = &arg; \
        if (call_user_function_ex(NULL, &obj, &fname, &rval, 1, args, 0, NULL TSRMLS_CC) == SUCCESS && rval) { \
            retval = rval; \
            zval_ptr_dtor(&arg); \
            arg = retval; \
        } \
    } \
    zend_hash_move_forward_ex(ht, &pos); \
}

#define MSGPACKI_FILTER_CALLBACK_BACKWARD(type, ht, retval) \
retval = arg; \
zend_hash_internal_pointer_end_ex(ht, &pos); \
while (zend_hash_get_current_data_ex(ht, (void**)&obj, &pos) == SUCCESS) { \
    if (obj && Z_TYPE_P(obj) == IS_OBJECT) { \
        zval fname, *rval, **args[1]; \
        ZVAL_STRINGL(&fname, #type, sizeof(#type)-1, 0); \
        args[0] = &arg; \
        if (call_user_function_ex(NULL, &obj, &fname, &rval, 1, args, 0, NULL TSRMLS_CC) == SUCCESS && rval) { \
            retval = rval; \
            zval_ptr_dtor(&arg); \
            arg = retval; \
        } \
    } \
    zend_hash_move_backwards_ex(ht, &pos); \
}

#define MSGPACKI_FILTER_PRE_SERIALIZE(ht, struc, filter) \
if (ht) { \
    zval *obj, *arg, *retval; \
    HashPosition pos; \
    arg = *struc; \
    Z_ADDREF_P(arg); \
    MSGPACKI_FILTER_CALLBACK_FORWARD(pre_serialize, ht, retval); \
    struc = &retval; \
    filter = 1; \
} else { \
    filter = 0; \
}

#define MSGPACKI_FILTER_POST_SERIALIZE(ht, buf, return_value) \
if (ht) { \
    zval *obj, *arg, *retval; \
    HashPosition pos; \
    MAKE_STD_ZVAL(arg); \
    if (buf.c) { \
        ZVAL_STRINGL(arg, buf.c, buf.len, 1); \
        smart_str_free(&buf); \
    } else { \
        ZVAL_NULL(arg); \
    } \
    MSGPACKI_FILTER_CALLBACK_FORWARD(post_serialize, ht, retval); \
    if (Z_TYPE_P(retval) != IS_STRING) { \
        convert_to_string(retval); \
    } \
    COPY_PZVAL_TO_ZVAL(*return_value, retval); \
    return; \
}

#define MSGPACKI_FILTER_PRE_UNSERIALIZE(ht, filter, buf, buf_len) \
if (ht) { \
    zval *obj, *arg; \
    HashPosition pos; \
    MAKE_STD_ZVAL(arg); \
    ZVAL_STRINGL(arg, buf, buf_len, 1); \
    MSGPACKI_FILTER_CALLBACK_BACKWARD(pre_unserialize, ht, filter); \
    if (Z_TYPE_P(filter) != IS_STRING) { \
        convert_to_string(filter); \
    } \
    buf = Z_STRVAL_P(filter); \
    buf_len = Z_STRLEN_P(filter); \
}

#define MSGPACKI_FILTER_POST_UNSERIALIZE(ht, return_value) \
if (ht) { \
    zval *obj, *arg, *retval; \
    HashPosition pos; \
    MAKE_STD_ZVAL(arg); \
    *arg = *return_value; \
    zval_copy_ctor(arg); \
    MSGPACKI_FILTER_CALLBACK_BACKWARD(post_unserialize, ht, retval); \
    zval_dtor(return_value); \
    COPY_PZVAL_TO_ZVAL(*return_value, retval); \
}

#endif  /* MSGPACKI_FILTER_H */
