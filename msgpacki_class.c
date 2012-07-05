
#include "php.h"
#include "php_ini.h"

#include "php_verdep.h"
#include "php_msgpacki.h"
#include "msgpacki_function.h"
#include "msgpacki_filter.h"
#include "msgpacki_class.h"
/* #include "msgpacki_debug.h" */

ZEND_EXTERN_MODULE_GLOBALS(msgpacki);

static zend_class_entry *msgpacki_ce;

static zend_object_handlers msgpacki_object_handlers;

typedef struct msgpacki_obj {
    zend_object std;
    long mode;
    HashTable *filters;
    HashTable *pre_serialize;
    HashTable *post_serialize;
    HashTable *pre_unserialize;
    HashTable *post_unserialize;
} msgpacki_obj_t;

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_method__construct, 0, 0, 0)
    ZEND_ARG_INFO(0, mode)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_method_pack, 0, 0, 1)
    ZEND_ARG_INFO(0, value)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_method_unpack, 0, 0, 1)
    ZEND_ARG_INFO(0, str)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_method_get_mode, 0, 0, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_method_set_mode, 0, 0, 1)
    ZEND_ARG_INFO(0, mode)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_method_append_filter, 0, 0, 1)
    ZEND_ARG_INFO(0, name)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_method_prepend_filter, 0, 0, 1)
    ZEND_ARG_INFO(0, name)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_method_remove_filter, 0, 0, 1)
    ZEND_ARG_INFO(0, name)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_method_get_filters, 0, 0, 0)
    ZEND_ARG_INFO(0, filter)
ZEND_END_ARG_INFO()

ZEND_METHOD(MessagePacki, __construct);
ZEND_METHOD(MessagePacki, pack);
ZEND_METHOD(MessagePacki, unpack);
ZEND_METHOD(MessagePacki, get_mode);
ZEND_METHOD(MessagePacki, set_mode);
ZEND_METHOD(MessagePacki, append_filter);
ZEND_METHOD(MessagePacki, prepend_filter);
ZEND_METHOD(MessagePacki, remove_filter);
ZEND_METHOD(MessagePacki, get_filters);

MSGPACKI_ZEND_FUNCTION_ENTRY msgpacki_methods[] = {
    ZEND_ME(MessagePacki, __construct,
            arginfo_msgpacki_method__construct, ZEND_ACC_PUBLIC|ZEND_ACC_CTOR)
    ZEND_ME(MessagePacki, pack,
            arginfo_msgpacki_method_pack, ZEND_ACC_PUBLIC)
    ZEND_ME(MessagePacki, unpack,
            arginfo_msgpacki_method_unpack, ZEND_ACC_PUBLIC)
    ZEND_ME(MessagePacki, get_mode,
            arginfo_msgpacki_method_get_mode, ZEND_ACC_PUBLIC)
    ZEND_ME(MessagePacki, set_mode,
            arginfo_msgpacki_method_set_mode, ZEND_ACC_PUBLIC)
    ZEND_ME(MessagePacki, append_filter,
            arginfo_msgpacki_method_append_filter, ZEND_ACC_PUBLIC)
    ZEND_ME(MessagePacki, prepend_filter,
            arginfo_msgpacki_method_prepend_filter, ZEND_ACC_PUBLIC)
    ZEND_ME(MessagePacki, remove_filter,
            arginfo_msgpacki_method_remove_filter, ZEND_ACC_PUBLIC)
    ZEND_ME(MessagePacki, get_filters,
            arginfo_msgpacki_method_get_filters, ZEND_ACC_PUBLIC)
    ZEND_MALIAS(MessagePacki, getMode, get_mode,
                arginfo_msgpacki_method_get_mode, ZEND_ACC_PUBLIC)
    ZEND_MALIAS(MessagePacki, setMode, set_mode,
                arginfo_msgpacki_method_set_mode, ZEND_ACC_PUBLIC)
    ZEND_MALIAS(MessagePacki, appendFilter, append_filter,
                arginfo_msgpacki_method_append_filter, ZEND_ACC_PUBLIC)
    ZEND_MALIAS(MessagePacki, prependFilter, prepend_filter,
                arginfo_msgpacki_method_prepend_filter, ZEND_ACC_PUBLIC)
    ZEND_MALIAS(MessagePacki, removeFilter, remove_filter,
                arginfo_msgpacki_method_remove_filter, ZEND_ACC_PUBLIC)
    ZEND_MALIAS(MessagePacki, getFilters, get_filters,
                arginfo_msgpacki_method_get_filters, ZEND_ACC_PUBLIC)
    ZEND_FE_END
};

#define MPI_CLASS_HASH_FREE(ht) \
    if (ht) {                   \
        zend_hash_destroy(ht);  \
        efree(ht);              \
        ht = NULL;              \
    }

static void
msgpacki_object_free_storage(void *object TSRMLS_DC)
{
    msgpacki_obj_t *intern = (msgpacki_obj_t *)object;
    zend_object_std_dtor(&intern->std TSRMLS_CC);
    MPI_CLASS_HASH_FREE(intern->filters);
    MPI_CLASS_HASH_FREE(intern->pre_serialize);
    MPI_CLASS_HASH_FREE(intern->post_serialize);
    MPI_CLASS_HASH_FREE(intern->pre_unserialize);
    MPI_CLASS_HASH_FREE(intern->post_unserialize);
    efree(object);
}

static inline zend_object_value
msgpacki_object_new_ex(zend_class_entry *ce, msgpacki_obj_t **ptr TSRMLS_DC)
{
    msgpacki_obj_t *intern;
    zend_object_value retval;
#if ZEND_MODULE_API_NO < 20100525
    zval *tmp;
#endif

    intern = emalloc(sizeof(msgpacki_obj_t));
    memset(intern, 0, sizeof(msgpacki_obj_t));
    if (ptr) {
        *ptr = intern;
    }

    zend_object_std_init(&intern->std, ce TSRMLS_CC);

#if ZEND_MODULE_API_NO >= 20100525
    object_properties_init(&intern->std, ce);
#else
    zend_hash_copy(intern->std.properties, &ce->default_properties,
                   (copy_ctor_func_t)zval_add_ref, (void *)&tmp, sizeof(zval *));
#endif

    retval.handle = zend_objects_store_put(
        intern, (zend_objects_store_dtor_t)zend_objects_destroy_object,
        (zend_objects_free_object_storage_t)msgpacki_object_free_storage,
        NULL TSRMLS_CC);
    retval.handlers = &msgpacki_object_handlers;

    intern->mode = -1;
    intern->filters = NULL;
    intern->pre_serialize = NULL;
    intern->post_serialize = NULL;
    intern->pre_unserialize = NULL;
    intern->post_unserialize = NULL;

    return retval;
}

static inline zend_object_value
msgpacki_object_new(zend_class_entry *ce TSRMLS_DC)
{
    return msgpacki_object_new_ex(ce, NULL TSRMLS_CC);
}

#define MPI_CLASS_CLONE_FILTER(type)                                    \
    if (old_obj->type) {                                                \
        uint size = sizeof(zval);                                       \
        zend_bool setTargetPointer;                                     \
        Bucket *p;                                                      \
        void *new_entry;                                                \
        ALLOC_HASHTABLE(new_obj->type);                                 \
        zend_hash_init(new_obj->type, 5, NULL,                          \
                       (dtor_func_t)msgpacki_filter_data_dtor, 0);      \
        setTargetPointer = !(new_obj->type->pInternalPointer);          \
        p = old_obj->type->pListHead;                                   \
        while (p) {                                                     \
            if (setTargetPointer                                        \
                && old_obj->type->pInternalPointer == p) {              \
                new_obj->type->pInternalPointer = NULL;                 \
            }                                                           \
            if (p->nKeyLength) {                                        \
                zend_hash_quick_update(new_obj->type, p->arKey,         \
                                       p->nKeyLength, p->h, p->pData,   \
                                       size, &new_entry);               \
            } else {                                                    \
                zend_hash_index_update(new_obj->type, p->h, p->pData,   \
                                       size, &new_entry);               \
            }                                                           \
            p = p->pListNext;                                           \
        }                                                               \
        if (!new_obj->type->pInternalPointer) {                         \
            new_obj->type->pInternalPointer = new_obj->type->pListHead; \
        }                                                               \
    }

static inline zend_object_value
msgpacki_object_clone(zval *this_ptr TSRMLS_DC)
{
    msgpacki_obj_t *new_obj = NULL;
    msgpacki_obj_t *old_obj =
        (msgpacki_obj_t *)zend_object_store_get_object(this_ptr TSRMLS_CC);
    zend_object_value new_ov = msgpacki_object_new_ex(old_obj->std.ce,
                                                      &new_obj TSRMLS_CC);

    zend_objects_clone_members(&new_obj->std, new_ov, &old_obj->std,
                               Z_OBJ_HANDLE_P(this_ptr) TSRMLS_CC);

    new_obj->mode = old_obj->mode;

    if (old_obj->filters) {
        uint size;
        zend_bool setTargetPointer;
        Bucket *p;
        void *new_entry;
        ALLOC_HASHTABLE(new_obj->filters);
        zend_hash_init(new_obj->filters, 5, NULL,
                       (dtor_func_t)msgpacki_filter_data_dtor, 0);
        setTargetPointer = !new_obj->filters->pInternalPointer;
        p = old_obj->filters->pListHead;
        while (p) {
            if (setTargetPointer && old_obj->filters->pInternalPointer == p) {
                new_obj->filters->pInternalPointer = NULL;
            }
            size = sizeof(msgpacki_filter_data_t)
                + strlen(((msgpacki_filter_data_t *)p->pData)->classname);
            if (p->nKeyLength) {
                zend_hash_quick_update(new_obj->filters, p->arKey, p->nKeyLength,
                                       p->h, p->pData, size, &new_entry);
            } else {
                zend_hash_index_update(new_obj->filters, p->h, p->pData,
                                       size, &new_entry);
            }
            Z_ADDREF_P(((msgpacki_filter_data_t *)new_entry)->object);
            p = p->pListNext;
        }
        if (!new_obj->filters->pInternalPointer) {
            new_obj->filters->pInternalPointer = new_obj->filters->pListHead;
        }
    }

    MPI_CLASS_CLONE_FILTER(pre_serialize);
    MPI_CLASS_CLONE_FILTER(post_serialize);
    MPI_CLASS_CLONE_FILTER(pre_unserialize);
    MPI_CLASS_CLONE_FILTER(post_unserialize);

    return new_ov;
}

PHP_MSGPACKI_API int
msgpacki_register_classes(TSRMLS_D)
{
    zend_class_entry ce;

    INIT_CLASS_ENTRY(ce, MSGPACKI_CLASS_NAME, msgpacki_methods);

    ce.create_object = msgpacki_object_new;

    msgpacki_ce = zend_register_internal_class(&ce TSRMLS_CC);
    if (msgpacki_ce == NULL) {
        return FAILURE;
    }

    memcpy(&msgpacki_object_handlers, zend_get_std_object_handlers(),
           sizeof(zend_object_handlers));

    msgpacki_object_handlers.clone_obj = msgpacki_object_clone;

    return SUCCESS;
}

PHP_MSGPACKI_API zend_class_entry
*msgpacki_get_ce(void) {
    return msgpacki_ce;
}

static inline msgpacki_filter_data_t
*mpi_get_filter_data(msgpacki_obj_t *intern, zend_bool *is_release,
                     char *name, int name_len, char *func TSRMLS_DC)
{
    zval fname, *obj, *retval = NULL;
    msgpacki_filter_data_t *data;

    if (!MPIG(filter).registers ||
        zend_hash_find(MPIG(filter).registers, name, name_len+1,
                       (void **)&data) == FAILURE) {
        if (!(intern->filters)) {
            ALLOC_HASHTABLE(intern->filters);
            zend_hash_init(intern->filters, 5, NULL,
                           (dtor_func_t)msgpacki_filter_data_dtor, 0);
        }

        data = ecalloc(1, sizeof(msgpacki_filter_data_t)+name_len);
        memcpy(data->classname, name, name_len);

        if (zend_lookup_class(name, name_len, (zend_class_entry ***)&data->ce
                              TSRMLS_CC) != SUCCESS) {
            zend_error(E_WARNING, "%s: MessagePack filter \"%s\" "
                       "requires class \"%s\", but that class is not defined",
                       func, name, data->classname);
            efree(data);
            return NULL;
        }
        data->ce = *(zend_class_entry**)data->ce;

        if (!instanceof_function(data->ce, msgpacki_filter_get_ce() TSRMLS_CC)) {
            zend_error(E_WARNING, "%s: MessagePack filter not implement class",
                       func);
            efree(data);
            return NULL;
        }

        /* create the object */
        ALLOC_ZVAL(obj);
        object_init_ex(obj, data->ce);
        Z_SET_REFCOUNT_P(obj, 1);
        Z_SET_ISREF_P(obj);

        /* filtername */
        add_property_string(obj, "filtername", (char*)name, 1);

        /* invoke the constructor */
        ZVAL_STRINGL(&fname, "__construct", sizeof("__construct")-1, 0);
        call_user_function_ex(NULL, &obj, &fname, &retval,
                              0, NULL, 0, NULL TSRMLS_CC);
        if (retval) {
            zval_ptr_dtor(&retval);
        }

        data->object = obj;

        if (zend_hash_add(intern->filters, name, name_len+1, (void*)data,
                          sizeof(*data)+name_len, NULL) != SUCCESS) {
            zend_error(E_WARNING, "%s: \"%s\" filter already exsists",
                       func, name);
            zval_ptr_dtor(&obj);
            efree(data);
            return NULL;
        }
        *is_release = 1;
    }
    return data;
}

#define MPI_OBJECT                                           \
    msgpacki_obj_t *intern;                                  \
    intern = (msgpacki_obj_t *)zend_object_store_get_object( \
        getThis() TSRMLS_CC)

ZEND_METHOD(MessagePacki, __construct)
{
    long mode = MPIG(mode);
#if ZEND_MODULE_API_NO >= 20090626
    zend_error_handling error_handling;
    zend_replace_error_handling(EH_THROW, NULL, &error_handling TSRMLS_CC);
#else
    php_set_error_handling(EH_THROW, NULL TSRMLS_CC);
#endif

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "|l", &mode) == SUCCESS) {
        MPI_OBJECT;
        intern->mode = mode;
    }

#if ZEND_MODULE_API_NO >= 20090626
    zend_restore_error_handling(&error_handling TSRMLS_CC);
#else
    php_set_error_handling(EH_NORMAL, NULL TSRMLS_CC);
#endif
}

ZEND_METHOD(MessagePacki, pack)
{
    zval **struc;
    msgpacki_serialize_data_t var_hash;
    msgpacki_buffer_t buf = {0};
    zend_bool filter = 0;
    MPI_OBJECT;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "Z", &struc) == FAILURE) {
        return;
    }

    MSGPACKI_FILTER_PRE_SERIALIZE(intern->pre_serialize, struc, filter);

    Z_TYPE_P(return_value) = IS_STRING;
    Z_STRVAL_P(return_value) = NULL;
    Z_STRLEN_P(return_value) = 0;

    MSGPACKI_SERIALIZE_INIT(var_hash);
    msgpacki_serialize(&buf, struc, intern->mode, &var_hash TSRMLS_CC);
    MSGPACKI_SERIALIZE_DESTROY(var_hash);

    if (filter) {
        zval_ptr_dtor(struc);
    }

    MSGPACKI_FILTER_POST_SERIALIZE(intern->post_serialize, buf, return_value);

    if (buf.c) {
        RETVAL_STRINGL(buf.c, buf.len, 0);
    } else {
        RETVAL_NULL();
    }
}

ZEND_METHOD(MessagePacki, unpack)
{
    char *buf = NULL;
    int buf_len;
    const unsigned char *p;
    msgpacki_unserialize_data_t var_hash;
    zval *filter = NULL;
    MPI_OBJECT;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "s", &buf, &buf_len) == FAILURE) {
        RETURN_FALSE;
    }

    if (buf_len == 0) {
        RETURN_FALSE;
    }

    MSGPACKI_FILTER_PRE_UNSERIALIZE(intern->pre_unserialize,
                                    filter, buf, buf_len);

    p = (const unsigned char*)buf;
    MSGPACKI_UNSERIALIZE_INIT(var_hash);

    if (MPIG(unserialize).level == 1) {
        msgpacki_unserialize_push(&var_hash, &return_value);
    }

    if (!msgpacki_unserialize(&return_value, &p, p + buf_len,
                              intern->mode, &var_hash TSRMLS_CC) ||
        ((char*)p - buf) != buf_len) {
        MSGPACKI_UNSERIALIZE_DESTROY(var_hash);
        if (filter) {
            zval_ptr_dtor(&filter);
        }
        zval_dtor(return_value);
        zend_error(E_NOTICE, "MessagePacki::unpack(): "
                   "Error at offset %ld of %d bytes",
                   (long)((char*)p - buf), buf_len);
        RETURN_FALSE;
    }

    MSGPACKI_UNSERIALIZE_DESTROY(var_hash);

    if (filter) {
        zval_ptr_dtor(&filter);
    }

    MSGPACKI_FILTER_POST_UNSERIALIZE(intern->post_unserialize, return_value);
}

ZEND_METHOD(MessagePacki, set_mode)
{
    long mode;
    MPI_OBJECT;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "l", &mode) == FAILURE) {
        RETURN_FALSE;
    }

    intern->mode = mode;

    RETURN_TRUE;
}

ZEND_METHOD(MessagePacki, get_mode)
{
    MPI_OBJECT;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    RETURN_LONG(intern->mode);
}

#if ZEND_MODULE_API_NO >= 20100525
#define MPI_CLASS_FILTER_GET_METHOD(obj, type)          \
    func = Z_OBJ_HT_P(obj)->get_method(                 \
        &(obj), #type, sizeof(#type)-1, NULL TSRMLS_CC)
#else
#define MPI_CLASS_FILTER_GET_METHOD(obj, type)      \
    func = Z_OBJ_HT_P(obj)->get_method(             \
        &(obj), #type, sizeof(#type)-1 TSRMLS_CC)
#endif

#define MPI_CLASS_FILTER_APPEND(type, filter, filter_len)               \
    MPI_CLASS_FILTER_GET_METHOD(data->object, type);                    \
    if (func != NULL &&                                                 \
        strcmp(func->common.scope->name,                                \
               MSGPACKI_FILTER_CLASS_NAME) != 0) {                      \
        if (!(intern->type)) {                                          \
            ALLOC_HASHTABLE(intern->type);                              \
            zend_hash_init(intern->type, 5, NULL,                       \
                           (dtor_func_t)msgpacki_filter_data_dtor, 0);  \
        }                                                               \
        if (zend_hash_add(intern->type, filter, filter_len+1,           \
                          (void*)data->object, sizeof(*(data->object)), \
                          NULL) != SUCCESS) {                           \
            zend_error(E_WARNING, "MessagePacki::append_filter(): "     \
                       "\"%s\" filter already exsists", filter);        \
        }                                                               \
    }

ZEND_METHOD(MessagePacki, append_filter)
{
    char *name;
    int name_len;
    msgpacki_filter_data_t *data;
    zend_bool is_release = 0;
    MPI_OBJECT;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s",
                              &name, &name_len) == FAILURE) {
        RETURN_FALSE;
    }

    RETVAL_FALSE;

    if (!name_len) {
        zend_error(E_WARNING, "MessagePacki::append_filter(): "
                   "Filter/Class name cannot be empty");
        return;
    }

    data = mpi_get_filter_data(intern, &is_release, name, name_len,
                               "MessagePacki::append_filter()" TSRMLS_CC);
    if (data == NULL) {
        return;
    }

    if (Z_TYPE_P(data->object) == IS_OBJECT &&
        Z_OBJ_HT_P(data->object)->get_method != NULL) {
        union _zend_function *func = NULL;
        MPI_CLASS_FILTER_APPEND(pre_serialize, name, name_len);
        MPI_CLASS_FILTER_APPEND(post_serialize, name, name_len);
        MPI_CLASS_FILTER_APPEND(pre_unserialize, name, name_len);
        MPI_CLASS_FILTER_APPEND(post_unserialize, name, name_len);
    }

    RETVAL_TRUE;

    if (is_release) {
        efree(data);
    }
}

#define MPI_CLASS_FILTER_PREPEND(type, filter, filter_len)              \
    MPI_CLASS_FILTER_GET_METHOD(data->object, type);                    \
    if (func != NULL &&                                                 \
        strcmp(func->common.scope->name,                                \
               MSGPACKI_FILTER_CLASS_NAME) != 0) {                      \
        if (!(intern->type)) {                                          \
            ALLOC_HASHTABLE(intern->type);                              \
            zend_hash_init(intern->type, 5, NULL,                       \
                           (dtor_func_t)msgpacki_filter_data_dtor, 0);  \
            if (zend_hash_add(                                          \
                    intern->type, filter, filter_len+1,                 \
                    (void*)data->object, sizeof(*(data->object)),       \
                    NULL) != SUCCESS) {                                 \
                zend_error(E_WARNING,                                   \
                           "MessagePacki::prepend_filter(): "           \
                           "\"%s\" filter already exsists", filter);    \
            }                                                           \
        } else if (zend_hash_exists(                                    \
                       intern->type, filter, filter_len+1)) {           \
            zend_error(E_WARNING,                                       \
                       "MessagePacki::prepend_filter(): "               \
                       "\"%s\" filter already exsists", filter);        \
        } else {                                                        \
            HashTable *hash;                                            \
            zval *obj;                                                  \
            char *key;                                                  \
            ulong index;                                                \
            uint key_len;                                               \
            HashPosition pos;                                           \
            size_t n = zend_hash_num_elements(intern->type);            \
            ALLOC_HASHTABLE(hash);                                      \
            zend_hash_init(hash, n > 5 ? n : 5, NULL,                   \
                           (dtor_func_t)msgpacki_filter_data_dtor, 0);  \
            if (zend_hash_add(                                          \
                    hash, filter, filter_len+1, (void*)data->object,    \
                    sizeof(*(data->object)), NULL) != SUCCESS) {        \
                zend_error(E_WARNING,                                   \
                           "MessagePacki:::prepend_filter(): "          \
                           "\"%s\" filter already exsists", filter);    \
            }                                                           \
            zend_hash_internal_pointer_reset_ex(intern->type, &pos);    \
            while (zend_hash_get_current_data_ex(                       \
                       intern->type, (void**)&obj, &pos) == SUCCESS) {  \
                if (zend_hash_get_current_key_ex(                       \
                        intern->type, &key, &key_len, &index,           \
                        1, &pos) == HASH_KEY_IS_STRING) {               \
                    zend_hash_add(hash, key, key_len,                   \
                                  (void*)obj, sizeof(*obj), NULL);      \
                    efree(key);                                         \
                }                                                       \
                zend_hash_move_forward_ex(intern->type, &pos);          \
            }                                                           \
            zend_hash_destroy(intern->type);                            \
            efree(intern->type);                                        \
            intern->type = NULL;                                        \
            intern->type = hash;                                        \
        }                                                               \
    }

ZEND_METHOD(MessagePacki, prepend_filter)
{
    char *name;
    int name_len;
    msgpacki_filter_data_t *data;
    zend_bool is_release = 0;
    MPI_OBJECT;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s",
                              &name, &name_len) == FAILURE) {
        RETURN_FALSE;
    }

    RETVAL_FALSE;

    if (!name_len) {
        zend_error(E_WARNING, "MessagePacki::prepend_filter(): "
                   "Filter/Class name cannot be empty");
        return;
    }

    data = mpi_get_filter_data(intern, &is_release, name, name_len,
                               "MessagePacki::prepend_filter()" TSRMLS_CC);
    if (data == NULL) {
        return;
    }

    if (Z_TYPE_P(data->object) == IS_OBJECT &&
        Z_OBJ_HT_P(data->object)->get_method != NULL) {
        union _zend_function *func = NULL;
        MPI_CLASS_FILTER_PREPEND(pre_serialize, name, name_len);
        MPI_CLASS_FILTER_PREPEND(post_serialize, name, name_len);
        MPI_CLASS_FILTER_PREPEND(pre_unserialize, name, name_len);
        MPI_CLASS_FILTER_PREPEND(post_unserialize, name, name_len);
    }

    RETVAL_TRUE;

    if (is_release) {
        efree(data);
    }
}

#define MPI_CLASS_FILTER_REMOVE(type, filter, filter_len)   \
    if (intern->type) {                                     \
        zend_hash_del(intern->type, filter, filter_len+1);  \
    }

ZEND_METHOD(MessagePacki, remove_filter)
{
    char *name;
    int name_len;
    MPI_OBJECT;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s",
                              &name, &name_len) == FAILURE) {
        RETURN_FALSE;
    }

    RETVAL_FALSE;

    if (!name_len) {
        zend_error(E_WARNING, "MessagePacki::remove_filter(): "
                   "Filter/Class name cannot be empty");
        return;
    }

    MPI_CLASS_FILTER_REMOVE(pre_serialize, name, name_len);
    MPI_CLASS_FILTER_REMOVE(post_serialize, name, name_len);
    MPI_CLASS_FILTER_REMOVE(pre_unserialize, name, name_len);
    MPI_CLASS_FILTER_REMOVE(post_unserialize, name, name_len);
    MPI_CLASS_FILTER_REMOVE(filters, name, name_len);

    RETVAL_TRUE;
}

#define MPI_CLASS_FILTER_GET(type)                                      \
    if (intern->type &&                                                 \
        (filter == NULL || strcmp(filter, #type) == 0)) {               \
        if (filter == NULL) {                                           \
            MAKE_STD_ZVAL(arr);                                         \
            array_init(arr);                                            \
        } else {                                                        \
            arr = return_value;                                         \
        }                                                               \
        zend_hash_internal_pointer_reset_ex(intern->type, &pos);        \
        while (zend_hash_get_current_data_ex(                           \
                   intern->type, (void**)&entry, &pos) == SUCCESS) {    \
            MAKE_STD_ZVAL(new_val);                                     \
            switch (zend_hash_get_current_key_ex(                       \
                        intern->type, &key, &key_len,                   \
                        &index, 1, &pos)) {                             \
                case HASH_KEY_IS_STRING:                                \
                    ZVAL_STRINGL(new_val, key, key_len - 1, 0);         \
                    add_next_index_zval(arr, new_val);                  \
                    break;                                              \
                case HASH_KEY_IS_LONG:                                  \
                    Z_TYPE_P(new_val) = IS_LONG;                        \
                    Z_LVAL_P(new_val) = index;                          \
                    add_next_index_zval(arr, new_val);                  \
                    break;                                              \
                default:                                                \
                    zval_ptr_dtor(&new_val);                            \
                    break;                                              \
            }                                                           \
            zend_hash_move_forward_ex(intern->pre_serialize, &pos);     \
        }                                                               \
        if (filter == NULL) {                                           \
            add_assoc_zval_ex(return_value, #type, sizeof(#type), arr); \
        }                                                               \
    }

ZEND_METHOD(MessagePacki, get_filters)
{
    char *filter = NULL;
    long filter_len = 0;
    zval *entry, *new_val, *arr;
    char *key;
    uint key_len;
    ulong index;
    HashPosition pos;
    MPI_OBJECT;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "|s",
                              &filter, &filter_len) == FAILURE) {
        return;
    }

    array_init(return_value);

    MPI_CLASS_FILTER_GET(pre_serialize);
    MPI_CLASS_FILTER_GET(post_serialize);
    MPI_CLASS_FILTER_GET(pre_unserialize);
    MPI_CLASS_FILTER_GET(post_unserialize);
}
