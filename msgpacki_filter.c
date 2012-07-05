
#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"

#include "php_verdep.h"
#include "php_msgpacki.h"
#include "msgpacki_filter.h"
/* #include "msgpacki_debug.h" */

#define FILTER_FUNC "MessagePack filter"

ZEND_EXTERN_MODULE_GLOBALS(msgpacki);

static zend_class_entry *msgpacki_ce_filter;

ZEND_METHOD(MSGPACKI_FILTER_CLASS_NAME, nop) {}

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_filter, 0, 0, 1)
    ZEND_ARG_INFO(0, in)
ZEND_END_ARG_INFO()

MSGPACKI_ZEND_FUNCTION_ENTRY msgpacki_filter_methods[] = {
    ZEND_FENTRY(pre_serialize, ZEND_MN(MSGPACKI_FILTER_CLASS_NAME_nop),
                arginfo_msgpacki_filter, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(post_serialize, ZEND_MN(MSGPACKI_FILTER_CLASS_NAME_nop),
                arginfo_msgpacki_filter, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(pre_unserialize, ZEND_MN(MSGPACKI_FILTER_CLASS_NAME_nop),
                arginfo_msgpacki_filter, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(post_unserialize, ZEND_MN(MSGPACKI_FILTER_CLASS_NAME_nop),
                arginfo_msgpacki_filter, ZEND_ACC_PUBLIC)
    ZEND_FE_END
};

PHP_MSGPACKI_API void
msgpacki_filter_data_dtor(msgpacki_filter_data_t *data)
{
    if (data) {
        if (data->object) {
            zval_ptr_dtor(&(data->object));
        }
    }
}

PHP_MSGPACKI_API int
msgpacki_register_filters(TSRMLS_D)
{
    zend_class_entry ce;

    INIT_CLASS_ENTRY(ce, MSGPACKI_FILTER_CLASS_NAME, msgpacki_filter_methods);

    msgpacki_ce_filter = zend_register_internal_class(&ce TSRMLS_CC);
    if (msgpacki_ce_filter == NULL) {
        return FAILURE;
    }

    zend_declare_property_string(msgpacki_ce_filter,
                                 "filtername", sizeof("filtername")-1, "",
                                 ZEND_ACC_PUBLIC TSRMLS_CC);

#ifdef HAVE_MSGPACKI_NAMESPACE
    zend_register_class_alias_ex(
        ZEND_NS_NAME(PHP_MSGPACKI_NS, "Filter"),
        sizeof(ZEND_NS_NAME(PHP_MSGPACKI_NS, "Filter"))-1,
        msgpacki_ce_filter TSRMLS_CC);
#endif

    return SUCCESS;
}

PHP_MSGPACKI_API zend_class_entry
*msgpacki_filter_get_ce(void) {
    return msgpacki_ce_filter;
}

ZEND_FUNCTION(msgpacki_filter_register)
{
    char *filtername, *classname;
    int filtername_len, classname_len;
    msgpacki_filter_data_t *data;
    zval fname, *obj, *retval = NULL;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss",
                              &filtername, &filtername_len,
                              &classname, &classname_len) == FAILURE) {
        RETURN_FALSE;
    }

    RETVAL_FALSE;

    if (!filtername_len) {
        zend_error(E_WARNING, "%s_register: Filter name cannot be empty",
                   FILTER_FUNC);
        return;
    }

    if (!classname_len) {
        zend_error(E_WARNING, "%s_register: Class name cannot be empty",
                   FILTER_FUNC);
        return;
    }

    if (!MPIG(filter).registers) {
        ALLOC_HASHTABLE(MPIG(filter).registers);
        zend_hash_init(MPIG(filter).registers, 5, NULL,
                       (dtor_func_t)msgpacki_filter_data_dtor, 0);
    }

    data = ecalloc(1, sizeof(msgpacki_filter_data_t)+classname_len);
    memcpy(data->classname, classname, classname_len);

    /* bind the classname to the actual class */
    if (zend_lookup_class(classname, classname_len,
                          (zend_class_entry ***)&data->ce
                          TSRMLS_CC) != SUCCESS) {
        zend_error(E_WARNING, "%s_register: MessagePack filter \"%s\" "
                   "requires class \"%s\", but that class is not defined",
                   FILTER_FUNC, filtername, data->classname);
        efree(data);
        return;
    }
    data->ce = *(zend_class_entry**)data->ce;

    /* instance check */
    if (!instanceof_function(data->ce, msgpacki_ce_filter TSRMLS_CC)) {
        zend_error(E_WARNING, "%s_register: MessagePack filter not "
                   "implement class", FILTER_FUNC);
        efree(data);
        return;
    }

    /* create the object */
    ALLOC_ZVAL(obj);
    object_init_ex(obj, data->ce);
    Z_SET_REFCOUNT_P(obj, 1);
    Z_SET_ISREF_P(obj);

    /* filtername */
    add_property_string(obj, "filtername", (char*)filtername, 1);

    /* invoke the constructor */
    ZVAL_STRINGL(&fname, "__construct", sizeof("__construct")-1, 0);
    call_user_function_ex(NULL, &obj, &fname, &retval,
                          0, NULL, 0, NULL TSRMLS_CC);
    if (retval) {
        zval_ptr_dtor(&retval);
    }

    data->object = obj;

    if (zend_hash_add(MPIG(filter).registers,
                      filtername, filtername_len+1, (void*)data,
                      sizeof(*data)+classname_len, NULL) == SUCCESS) {
        RETVAL_TRUE;
    } else {
        zend_error(E_WARNING, "%s_register: \"%s\" filter already exsists",
                   FILTER_FUNC, filtername);
        zval_ptr_dtor(&obj);
    }

    efree(data);
}

#if ZEND_MODULE_API_NO >= 20100525
#define MPI_FILTER_GET_METHOD(obj, type)                \
    func = Z_OBJ_HT_P(obj)->get_method(                 \
        &(obj), #type, sizeof(#type)-1, NULL TSRMLS_CC)
#else
#define MPI_FILTER_GET_METHOD(obj, type)            \
    func = Z_OBJ_HT_P(obj)->get_method(             \
        &(obj), #type, sizeof(#type)-1 TSRMLS_CC)
#endif

#define MPI_FILTER_APPEND(type, ht, filter, filter_len)                 \
    MPI_FILTER_GET_METHOD(data->object, type);                          \
    if (func != NULL &&                                                 \
        strcmp(MSGPACKI_FILTER_CLASS_NAME,                              \
               func->common.scope->name) != 0) {                        \
        if (!(ht)) {                                                    \
            ALLOC_HASHTABLE(ht);                                        \
            zend_hash_init(ht, 5, NULL,                                 \
                           (dtor_func_t)msgpacki_filter_data_dtor, 0);  \
        }                                                               \
        if (zend_hash_add(ht, filter, filter_len+1,                     \
                          (void*)data->object, sizeof(*(data->object)), \
                          NULL) == FAILURE) {                           \
            zend_error(E_WARNING,                                       \
                       "%s_append: \"%s\" filter already exsists",      \
                       FILTER_FUNC, filter);                            \
            return;                                                     \
        }                                                               \
    }

ZEND_FUNCTION(msgpacki_filter_append)
{
    char *filtername;
    int filtername_len;
    msgpacki_filter_data_t *data;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s",
                              &filtername, &filtername_len) == FAILURE) {
        RETURN_FALSE;
    }

    RETVAL_FALSE;

    if (!filtername_len) {
        zend_error(E_WARNING, "%s_append: Filter name cannot be empty",
                   FILTER_FUNC);
        return;
    }

    if (!MPIG(filter).registers) {
        zend_error(E_WARNING, "%s_append: Enable filter empty", FILTER_FUNC);
        return;
    }

    if (zend_hash_find(MPIG(filter).registers, filtername, filtername_len+1,
                       (void **)&data) == FAILURE) {
        zend_error(E_WARNING, "%s_append: No such find filter: \"%s\"",
                   FILTER_FUNC, filtername);
        return;
    }

    if (Z_TYPE_P(data->object) == IS_OBJECT &&
        Z_OBJ_HT_P(data->object)->get_method != NULL) {
        union _zend_function *func = NULL;
        MPI_FILTER_APPEND(pre_serialize, MPIG(filter).pre_serialize,
                          filtername, filtername_len);
        MPI_FILTER_APPEND(post_serialize, MPIG(filter).post_serialize,
                          filtername, filtername_len);
        MPI_FILTER_APPEND(pre_unserialize, MPIG(filter).pre_unserialize,
                          filtername, filtername_len);
        MPI_FILTER_APPEND(post_unserialize, MPIG(filter).post_unserialize,
                          filtername, filtername_len);
    }

    RETVAL_TRUE;
}

#define MPI_FILTER_PREPEND(type, ht, filter, filter_len)                \
    MPI_FILTER_GET_METHOD(data->object, type);                          \
    if (func != NULL &&                                                 \
        strcmp(MSGPACKI_FILTER_CLASS_NAME,                              \
               func->common.scope->name) != 0) {                        \
        if (!(ht)) {                                                    \
            ALLOC_HASHTABLE(ht);                                        \
            zend_hash_init(ht, 5, NULL,                                 \
                           (dtor_func_t)msgpacki_filter_data_dtor, 0);  \
            if (zend_hash_add(                                          \
                    ht, filter, filter_len+1, (void*)data->object,      \
                    sizeof(*(data->object)), NULL) == FAILURE) {        \
                zend_error(E_WARNING,                                   \
                           "%s_prepend: \"%s\" filter already exsists", \
                           FILTER_FUNC, filter);                        \
            }                                                           \
        } else if (zend_hash_exists(ht, filter, filter_len+1)) {        \
            zend_error(E_WARNING,                                       \
                       "%s_prepend: \"%s\" filter already exsists",     \
                       FILTER_FUNC, filter);                            \
        } else {                                                        \
            HashTable *hash;                                            \
            zval *obj;                                                  \
            char *string_key;                                           \
            uint string_key_len;                                        \
            ulong num_key;                                              \
            HashPosition pos;                                           \
            size_t n = zend_hash_num_elements(ht);                      \
            ALLOC_HASHTABLE(hash);                                      \
            zend_hash_init(hash, n > 5 ? n : 5, NULL,                   \
                           (dtor_func_t)msgpacki_filter_data_dtor, 0);  \
            if (zend_hash_add(hash, filter, filter_len+1,               \
                              (void*)data->object,                      \
                              sizeof(*(data->object)),                  \
                              NULL) != SUCCESS) {                       \
                zend_error(E_WARNING,                                   \
                           "%s_prepend: \"%s\" filter already exsists", \
                           FILTER_FUNC, filter);                        \
            }                                                           \
            zend_hash_internal_pointer_reset_ex(ht, &pos);              \
            while (zend_hash_get_current_data_ex(                       \
                       ht, (void**)&obj, &pos) == SUCCESS) {            \
                if (zend_hash_get_current_key_ex(                       \
                        ht, &string_key, &string_key_len,               \
                        &num_key, 1, &pos) == HASH_KEY_IS_STRING) {     \
                    zend_hash_add(hash, string_key, string_key_len,     \
                                  (void*)obj, sizeof(*obj), NULL);      \
                    efree(string_key);                                  \
                }                                                       \
                zend_hash_move_forward_ex(ht, &pos);                    \
            }                                                           \
            zend_hash_destroy(ht);                                      \
            efree(ht);                                                  \
            ht = NULL;                                                  \
            ht = hash;                                                  \
        }                                                               \
    }

ZEND_FUNCTION(msgpacki_filter_prepend)
{
    char *filtername;
    int filtername_len;
    msgpacki_filter_data_t *data;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s",
                              &filtername, &filtername_len) == FAILURE) {
        RETURN_FALSE;
    }

    RETVAL_FALSE;

    if (!filtername_len) {
        zend_error(E_WARNING, "%s_prepend: Filter name cannot be empty",
                   FILTER_FUNC);
        return;
    }

    if (!MPIG(filter).registers) {
        zend_error(E_WARNING, "%s_prepend: Enable filter empty", FILTER_FUNC);
        return;
    }

    if (zend_hash_find(MPIG(filter).registers, filtername, filtername_len+1,
                       (void **)&data) == FAILURE) {
        zend_error(E_WARNING, "%s_prepend: No such find filter: \"%s\"",
                   FILTER_FUNC, filtername);
        return;
    }

    if (Z_TYPE_P(data->object) == IS_OBJECT &&
        Z_OBJ_HT_P(data->object)->get_method != NULL) {
        union _zend_function *func = NULL;
        MPI_FILTER_PREPEND(pre_serialize, MPIG(filter).pre_serialize,
                           filtername, filtername_len);
        MPI_FILTER_PREPEND(post_serialize, MPIG(filter).post_serialize,
                           filtername, filtername_len);
        MPI_FILTER_PREPEND(pre_unserialize, MPIG(filter).pre_unserialize,
                           filtername, filtername_len);
        MPI_FILTER_PREPEND(post_unserialize, MPIG(filter).post_unserialize,
                           filtername, filtername_len);
    }

    RETVAL_TRUE;
}

#define MPI_FILTER_REMOVE(type, ht, filter, filter_len) \
    if (ht) {                                           \
        zend_hash_del(ht, filter, filter_len+1);        \
    }

ZEND_FUNCTION(msgpacki_filter_remove)
{
    char *filtername;
    int filtername_len;
    /* msgpacki_filter_data_t *data; */

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s",
                              &filtername, &filtername_len) == FAILURE) {
        RETURN_FALSE;
    }

    RETVAL_FALSE;

    if (!filtername_len) {
        zend_error(E_WARNING, "%s_remove: Filter name cannot be empty",
                   FILTER_FUNC);
        return;
    }

    /*
    if (!MPIG(filter).registers) {
        zend_error(E_WARNING, "%s_remove: Enable filter empty", FILTER_FUNC);
        return;
    }

    if (zend_hash_find(MPIG(filter).registers, filtername, filtername_len+1,
                       (void **)&data) == FAILURE) {
        zend_error(E_WARNING, "%s_remove: No such find filter: \"%s\"",
                   FILTER_FUNC, filtername);
        return;
    }
    */

    MPI_FILTER_REMOVE(pre_serialize, MPIG(filter).pre_serialize,
                      filtername, filtername_len);
    MPI_FILTER_REMOVE(post_serialize, MPIG(filter).post_serialize,
                      filtername, filtername_len);
    MPI_FILTER_REMOVE(pre_unserialize, MPIG(filter).pre_unserialize,
                      filtername, filtername_len);
    MPI_FILTER_REMOVE(post_unserialize, MPIG(filter).post_unserialize,
                      filtername, filtername_len);

    /* register remove */
    /* zend_hash_del(MPIG(filter).registers, filtername, filtername_len+1); */

    RETVAL_TRUE;
}

#define MPI_FILTER_GET(type, ht)                                        \
    if (ht && (filter == NULL || strcmp(filter, #type) == 0)) {         \
        if (filter == NULL) {                                           \
            MAKE_STD_ZVAL(arr);                                         \
            array_init(arr);                                            \
        } else {                                                        \
            arr = return_value;                                         \
        }                                                               \
        zend_hash_internal_pointer_reset_ex(ht, &pos);                  \
        while (zend_hash_get_current_data_ex(ht, (void**)&entry,        \
                                             &pos) == SUCCESS) {        \
            MAKE_STD_ZVAL(new_val);                                     \
            switch (zend_hash_get_current_key_ex(ht, &string_key,       \
                                                 &string_key_len,       \
                                                 &num_key, 1, &pos)) {  \
                case HASH_KEY_IS_STRING:                                \
                    ZVAL_STRINGL(new_val, string_key,                   \
                                 string_key_len - 1, 0);                \
                    add_next_index_zval(arr, new_val);                  \
                    break;                                              \
                case HASH_KEY_IS_LONG:                                  \
                    Z_TYPE_P(new_val) = IS_LONG;                        \
                    Z_LVAL_P(new_val) = num_key;                        \
                    add_next_index_zval(arr, new_val);                  \
                    break;                                              \
                default:                                                \
                    zval_ptr_dtor(&new_val);                            \
                    break;                                              \
            }                                                           \
            zend_hash_move_forward_ex(ht, &pos);                        \
        }                                                               \
        if (filter == NULL) {                                           \
            add_assoc_zval_ex(return_value, #type, sizeof(#type), arr); \
        }                                                               \
    }

ZEND_FUNCTION(msgpacki_get_filters)
{
    char *filter = NULL;
    long filter_len = 0;
    zval *entry, *new_val, *arr;
    char *string_key;
    uint string_key_len;
    ulong num_key;
    HashPosition pos;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "|s",
                              &filter, &filter_len) == FAILURE) {
        return;
    }

    array_init(return_value);

    MPI_FILTER_GET(registers, MPIG(filter).registers);
    MPI_FILTER_GET(pre_serialize, MPIG(filter).pre_serialize);
    MPI_FILTER_GET(post_serialize, MPIG(filter).post_serialize);
    MPI_FILTER_GET(pre_unserialize, MPIG(filter).pre_unserialize);
    MPI_FILTER_GET(post_unserialize, MPIG(filter).post_unserialize);
}
