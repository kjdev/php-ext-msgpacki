
#include "php.h"
#include "ext/standard/php_incomplete_class.h"
#include "ext/standard/php_var.h"
#include "ext/session/php_session.h"
#if ZEND_MODULE_API_NO >= 20090626
#include "zend_closures.h"
#endif

#include "php_verdep.h"
#include "php_msgpacki.h"
#include "msgpacki_function.h"
#include "msgpacki_filter.h"
/* #include "msgpacki_debug.h" */

#include "msgpack.h"

ZEND_EXTERN_MODULE_GLOBALS(msgpacki);

enum msgpacki_serialize_type
{
    MPI_TYPE_NONE =  0,
    MPI_TYPE_REFERENCE =  1,
    MPI_TYPE_OBJECT,
    MPI_TYPE_CUSTOM_OBJECT,
};

//------------------------------------------------------------------------------
// Serialize
//------------------------------------------------------------------------------
#define SERIALIZE_FUNC "MessagePack serialize"

static void
mpi_serialize_intern(msgpacki_buffer_t *buf, zval *struc,
                     HashTable *var_hash, long mode TSRMLS_DC);

static inline int
mpi_add_hash(HashTable *var_hash, zval *var, void *var_old TSRMLS_DC)
{
    ulong var_no;
    char id[32], *p;
    register int len;

    if ((Z_TYPE_P(var) == IS_OBJECT) && Z_OBJ_HT_P(var)->get_class_entry) {
        p = smart_str_print_long(
            id + sizeof(id) - 1,
            (((size_t)Z_OBJCE_P(var) << 5) |
             ((size_t)Z_OBJCE_P(var) >> (sizeof(long) * 8 - 5)))
            + (long)Z_OBJ_HANDLE_P(var));
        len = id + sizeof(id) - 1 - p;
    } else {
        p = smart_str_print_long(id + sizeof(id) - 1, (long)var);
        len = id + sizeof(id) - 1 - p;
    }

    if (var_old && zend_hash_find(var_hash, p, len, var_old) == SUCCESS) {
        if (!Z_ISREF_P(var)) {
            var_no = -1;
            zend_hash_next_index_insert(var_hash, &var_no, sizeof(var_no), NULL);
        }
        return FAILURE;
    }

    var_no = zend_hash_num_elements(var_hash) + 1;
    zend_hash_add(var_hash, p, len, &var_no, sizeof(var_no), NULL);
    return SUCCESS;
}

static inline void
mpi_buffer_append_nil(msgpacki_buffer_t *buf)
{
    smart_str_appendc(buf, 0xc0);
}

static inline void
mpi_buffer_append_true(msgpacki_buffer_t *buf)
{
    smart_str_appendc(buf, 0xc3);
}

static inline void
mpi_buffer_append_false(msgpacki_buffer_t *buf)
{
    smart_str_appendc(buf, 0xc2);
}

static inline void
mpi_buffer_append_fixint(msgpacki_buffer_t *buf, int8_t val)
{
    smart_str_appendc(buf, val);
}

static inline void
mpi_buffer_append_uint8(msgpacki_buffer_t *buf, uint8_t val)
{
    smart_str_appendc(buf, (char)0xcc);
    smart_str_appendc(buf, (char)val);
}

static inline void
mpi_buffer_append_uint16(msgpacki_buffer_t *buf, uint16_t val)
{
    uint16_t be = _msgpack_be16(val);
    smart_str_appendc(buf, (char)0xcd);
    smart_str_appendl(buf, (const void*)&be, 2);
}

static inline void
mpi_buffer_append_uint32(msgpacki_buffer_t *buf, uint32_t val)
{
    uint32_t be = _msgpack_be32(val);
    smart_str_appendc(buf, (char)0xce);
    smart_str_appendl(buf, (const void*)&be, 4);
}

static inline void
mpi_buffer_append_uint64(msgpacki_buffer_t *buf, uint64_t val)
{
    uint64_t be = _msgpack_be64(val);
    smart_str_appendc(buf, (char)0xcf);
    smart_str_appendl(buf, (const void*)&be, 8);
}

static inline void
mpi_buffer_append_int8(msgpacki_buffer_t *buf, int8_t val)
{
    smart_str_appendc(buf, (char)0xd0);
    smart_str_appendc(buf, (char)val);
}

static inline void
mpi_buffer_append_int16(msgpacki_buffer_t *buf, int16_t val)
{
    uint16_t be = _msgpack_be16(val);
    smart_str_appendc(buf, (char)0xd1);
    smart_str_appendl(buf, (const void*)&be, 2);
}

static inline void
mpi_buffer_append_int32(msgpacki_buffer_t *buf, int32_t val)
{
    uint32_t be = _msgpack_be32(val);
    smart_str_appendc(buf, (char)0xd2);
    smart_str_appendl(buf, (const void*)&be, 4);
}

static inline void
mpi_buffer_append_int64(msgpacki_buffer_t *buf, int64_t val)
{
    uint64_t be = _msgpack_be64(val);
    smart_str_appendc(buf, (char)0xd3);
    smart_str_appendl(buf, (const void*)&be, 8);
}

static inline void
mpi_buffer_append_raw_header(msgpacki_buffer_t *buf, unsigned int val)
{
    if (val < 32) {
        unsigned char h = 0xa0 | (uint8_t) val;
        smart_str_appendc(buf, h);
    } else if (val < 65536) {
        uint16_t be = _msgpack_be16(val);
        smart_str_appendc(buf, (char)0xda);
        smart_str_appendl(buf, (const void*)&be, 2);
    } else {
        uint32_t be = _msgpack_be32(val);
        smart_str_appendc(buf, (char)0xdb);
        smart_str_appendl(buf, (const void*)&be, 4);
    }
}

static inline void
mpi_buffer_append_array_header(msgpacki_buffer_t *buf, unsigned int val)
{
    if (val < 16) {
        unsigned char h = 0x90 | (uint8_t) val;
        smart_str_appendc(buf, h);
    } else if (val < 65536) {
        uint16_t be = _msgpack_be16(val);
        smart_str_appendc(buf, (char)0xdc);
        smart_str_appendl(buf, (const void*)&be, 2);
    } else {
        uint32_t be = _msgpack_be32(val);
        smart_str_appendc(buf, (char)0xdd);
        smart_str_appendl(buf, (const void*)&be, 4);
    }
}

static inline void
mpi_buffer_append_map_header(msgpacki_buffer_t *buf, unsigned int val)
{
    if (val < 16) {
        unsigned char h = 0x80 | (uint8_t) val;
        smart_str_appendc(buf, h);
    } else if (val < 65536) {
        uint16_t be = _msgpack_be16(val);
        smart_str_appendc(buf, (char)0xde);
        smart_str_appendl(buf, (const void*)&be, 2);
    } else {
        uint32_t be = _msgpack_be32(val);
        smart_str_appendc(buf, (char)0xdf);
        smart_str_appendl(buf, (const void*)&be, 4);
    }
}

static inline void
mpi_buffer_append_map_header_refecence(msgpacki_buffer_t *buf, zend_uint count)
{
    mpi_buffer_append_map_header(buf, count + 1);
    mpi_buffer_append_nil(buf);
    mpi_buffer_append_fixint(buf, (int8_t)MPI_TYPE_REFERENCE);
}

static inline void
mpi_buffer_append_map_header_object(msgpacki_buffer_t *buf, zend_uint count)
{
    mpi_buffer_append_map_header(buf, count + 1);
    mpi_buffer_append_nil(buf);
    mpi_buffer_append_fixint(buf, (int8_t)MPI_TYPE_OBJECT);
}

static inline void
mpi_buffer_append_map_header_custom_object(msgpacki_buffer_t *buf,
                                           zend_uint count)
{
    mpi_buffer_append_map_header(buf, count + 1);
    mpi_buffer_append_nil(buf);
    mpi_buffer_append_fixint(buf, (int8_t)MPI_TYPE_CUSTOM_OBJECT);
}

static inline void
mpi_buffer_append_map_header_class(msgpacki_buffer_t *buf, zend_uint count,
                                   char *name, zend_uint len)
{
    mpi_buffer_append_map_header(buf, count + 1);
    mpi_buffer_append_nil(buf);
    mpi_buffer_append_raw_header(buf, len);
    smart_str_appendl(buf, name, len);
}

static inline void
mpi_serialize_long(msgpacki_buffer_t *buf, long val)
{
    if (val < -0x20L) {
        if (val < -0x8000L) {
            if (val < -0x80000000L) {
                mpi_buffer_append_int64(buf, (int64_t)val);
            } else {
                mpi_buffer_append_int32(buf, (int32_t)val);
            }
        } else {
            if (val < -0x80L) {
                mpi_buffer_append_int16(buf, (int16_t)val);
            } else {
                mpi_buffer_append_int8(buf, (int8_t)val);
            }
        }
    } else if (val <= 0x7fL) {
        mpi_buffer_append_fixint(buf, (int8_t)val);
    } else {
        if (val <= 0xffffL) {
            if (val <= 0xffL) {
                mpi_buffer_append_uint8(buf, (uint8_t)val);
            } else {
                mpi_buffer_append_uint16(buf, (uint16_t)val);
            }
        } else {
            if (val <= 0xffffffffL) {
                mpi_buffer_append_uint32(buf, (uint32_t)val);
            } else {
                mpi_buffer_append_uint64(buf, (uint64_t)val);
            }
        }
    }
}

static inline void
mpi_serialize_float(msgpacki_buffer_t *buf, double val)
{
    union { float f; uint32_t i; } mem = { val };
    uint32_t be = _msgpack_be32(mem.i);
    smart_str_appendc(buf, (char)0xca);
    smart_str_appendl(buf, (const void*)&be, 4);
}

static inline void
mpi_serialize_double(msgpacki_buffer_t *buf, double val)
{
    union { double f; uint64_t i; } mem = { val };
    uint64_t be = _msgpack_be64(mem.i);
    smart_str_appendc(buf, (char)0xcb);
    smart_str_appendl(buf, (const void*)&be, 8);
}

static inline void
mpi_serialize_string(msgpacki_buffer_t *buf, char *str, int len)
{
    mpi_buffer_append_raw_header(buf, len);
    smart_str_appendl(buf, str, len);
}

static inline void
mpi_serialize_array(msgpacki_buffer_t *buf, zval *val,
                    HashTable *var_hash, long mode TSRMLS_DC)
{
    HashTable *ht = HASH_OF(val);
    size_t i = 0;
    int is_map = 0;

    if (ht) {
        i = zend_hash_num_elements(ht);
    }

    if (i <= 0) {
        mpi_buffer_append_array_header(buf, 0);
        return;
    } else if (mode & PHP_MSGPACKI_MODE_PHP) {
        is_map = 1;
    } else if (ht->nNumOfElements == ht->nNextFreeElement) {
        Bucket *p = ht->pListHead;
        while (p) {
            if (p->nKeyLength) {
                is_map = 1;
                break;
            }
            p = p->pListNext;
        }
    } else {
        is_map = 1;
    }

    if (is_map) {
        char *key;
        zval **data;
        ulong index;
        uint key_len;
        HashPosition pos;

        mpi_buffer_append_map_header(buf, i);

        zend_hash_internal_pointer_reset_ex(ht, &pos);
        for (;; zend_hash_move_forward_ex(ht, &pos)) {
            i = zend_hash_get_current_key_ex(ht, &key, &key_len,
                                             &index, 0, &pos);
            if (i == HASH_KEY_NON_EXISTANT) {
                break;
            }

            switch (i) {
                case HASH_KEY_IS_LONG:
                    mpi_serialize_long(buf, index);
                    break;
                case HASH_KEY_IS_STRING:
                    mpi_serialize_string(buf, key, key_len - 1);
                    break;
            }

            if (zend_hash_get_current_data_ex(
                    ht, (void **)&data, &pos) != SUCCESS
                || !data || data == &val
                || (Z_TYPE_PP(data) == IS_ARRAY
                    && Z_ARRVAL_PP(data)->nApplyCount > 1)) {
                mpi_buffer_append_nil(buf);
            } else {
                if (Z_TYPE_PP(data) == IS_ARRAY) {
                    Z_ARRVAL_PP(data)->nApplyCount++;
                }
                mpi_serialize_intern(buf, *data, var_hash, mode TSRMLS_CC);
                if (Z_TYPE_PP(data) == IS_ARRAY) {
                    Z_ARRVAL_PP(data)->nApplyCount--;
                }
            }
        }
    } else {
        zval **data;
        ulong index;

        mpi_buffer_append_array_header(buf, i);

        for (index = 0; index < i; index++) {
            if (zend_hash_index_find(ht, index, (void **)&data) != SUCCESS
                || !data || data == &val
                || (Z_TYPE_PP(data) == IS_ARRAY
                    && Z_ARRVAL_PP(data)->nApplyCount > 1)) {
                mpi_buffer_append_nil(buf);
            } else {
                if (Z_TYPE_PP(data) == IS_ARRAY) {
                    Z_ARRVAL_PP(data)->nApplyCount++;
                }
                mpi_serialize_intern(buf, *data, var_hash, mode TSRMLS_CC);
                if (Z_TYPE_PP(data) == IS_ARRAY) {
                    Z_ARRVAL_PP(data)->nApplyCount--;
                }
            }
        }
    }
}

static inline zend_bool
mpi_buffer_append_class_header(msgpacki_buffer_t *buf, zval *val,
                               size_t *count TSRMLS_DC)
{
    PHP_CLASS_ATTRIBUTES;
    PHP_SET_CLASS_ATTRIBUTES(val);

    if (incomplete_class && (*count) != 0) {
        --(*count);
    }

    mpi_buffer_append_map_header_class(buf, *count, class_name, name_len);

    PHP_CLEANUP_CLASS_ATTRIBUTES();

    return incomplete_class;
}

static inline void
mpi_serialize_class(msgpacki_buffer_t *buf, zval *val, zval *retval_ptr,
                    HashTable *var_hash, long mode TSRMLS_DC)
{
    zend_bool incomplete_class;
    size_t count;
    HashTable *ht = HASH_OF(retval_ptr);

    count = zend_hash_num_elements(ht);
    if (mode & PHP_MSGPACKI_MODE_PHP) {
        incomplete_class = mpi_buffer_append_class_header(buf, val,
                                                           &count TSRMLS_CC);
    } else {
        incomplete_class = 0;
        mpi_buffer_append_map_header(buf, count);
    }

    if (count > 0) {
        char *key;
        zval **d, **name;
        ulong index;
        HashPosition pos;
        int i;
        zval nval, *nvalp;

        ZVAL_NULL(&nval);
        nvalp = &nval;

        zend_hash_internal_pointer_reset_ex(ht, &pos);

        for (;; zend_hash_move_forward_ex(ht, &pos)) {
            i = zend_hash_get_current_key_ex(HASH_OF(retval_ptr), &key, NULL,
                                             &index, 0, &pos);

            if (i == HASH_KEY_NON_EXISTANT) {
                break;
            }

            if (incomplete_class && strcmp(key, MAGIC_MEMBER) == 0) {
                continue;
            }
            zend_hash_get_current_data_ex(ht, (void **)&name, &pos);

            if (Z_TYPE_PP(name) != IS_STRING) {
                zend_error(E_NOTICE, "%s: __sleep should return an array only "
                           "containing the names of instance-variables "
                           "to serialize.", SERIALIZE_FUNC);
                mpi_buffer_append_nil(buf);
                continue;
            }
            if (zend_hash_find(Z_OBJPROP_P(val), Z_STRVAL_PP(name),
                               Z_STRLEN_PP(name) + 1, (void *) &d) == SUCCESS) {
                mpi_serialize_string(buf, Z_STRVAL_PP(name), Z_STRLEN_PP(name));
                mpi_serialize_intern(buf, *d, var_hash, mode TSRMLS_CC);
            } else {
                zend_class_entry *ce;
                ce = zend_get_class_entry(val TSRMLS_CC);
                if (ce && (mode & PHP_MSGPACKI_MODE_PHP)) {
                    char *prot_name, *priv_name;
                    int prop_name_length;

                    do {
                        zend_mangle_property_name(&priv_name, &prop_name_length,
                                                  ce->name, ce->name_length,
                                                  Z_STRVAL_PP(name),
                                                  Z_STRLEN_PP(name),
                                                  ce->type&ZEND_INTERNAL_CLASS);
                        if (zend_hash_find(Z_OBJPROP_P(val),
                                           priv_name, prop_name_length + 1,
                                           (void *)&d) == SUCCESS) {
                            mpi_serialize_string(buf,
                                                  priv_name, prop_name_length);
                            pefree(priv_name, ce->type & ZEND_INTERNAL_CLASS);
                            mpi_serialize_intern(buf, *d, var_hash,
                                                  mode TSRMLS_CC);
                            break;
                        }
                        pefree(priv_name, ce->type & ZEND_INTERNAL_CLASS);
                        zend_mangle_property_name(&prot_name, &prop_name_length,
                                                  "*", 1, Z_STRVAL_PP(name),
                                                  Z_STRLEN_PP(name),
                                                  ce->type&ZEND_INTERNAL_CLASS);
                        if (zend_hash_find(Z_OBJPROP_P(val),
                                           prot_name, prop_name_length + 1,
                                           (void *) &d) == SUCCESS) {
                            mpi_serialize_string(buf,
                                                  prot_name, prop_name_length);
                            pefree(prot_name, ce->type & ZEND_INTERNAL_CLASS);
                            mpi_serialize_intern(buf, *d, var_hash,
                                                  mode TSRMLS_CC);
                            break;
                        }
                        pefree(prot_name, ce->type & ZEND_INTERNAL_CLASS);
                        mpi_serialize_string(buf, Z_STRVAL_PP(name),
                                              Z_STRLEN_PP(name));
                        mpi_serialize_intern(buf, nvalp, var_hash,
                                              mode TSRMLS_CC);
                        zend_error(E_NOTICE, "%s: \"%s\" returned as member "
                                   "variable from __sleep() but does not exist",
                                   SERIALIZE_FUNC, Z_STRVAL_PP(name));
                    } while (0);
                } else {
                    mpi_serialize_string(buf,
                                          Z_STRVAL_PP(name), Z_STRLEN_PP(name));
                    mpi_serialize_intern(buf, nvalp, var_hash,
                                          mode TSRMLS_CC);
                }
            }
        }
    }
}

static inline void
mpi_serialize_object(msgpacki_buffer_t *buf, zval *val,
                     HashTable *var_hash, long mode TSRMLS_DC)
{
    size_t i = 0;
    HashTable *ht = NULL;
    zend_class_entry *ce = NULL;
    zval *retval_ptr = NULL;
    zval fname;
    zend_bool incomplate_class;
    int res;

    if (Z_OBJ_HT_P(val)->get_class_entry) {
        ce = Z_OBJCE_P(val);
    }

    if ((mode & PHP_MSGPACKI_MODE_PHP) && ce && ce->serialize != NULL) {
        unsigned char *serialized_data = NULL;
        zend_uint serialized_length;
        php_serialize_data_t var_data;
        PHP_VAR_SERIALIZE_INIT(var_data);
        if (ce->serialize(
                val, &serialized_data, &serialized_length,
#if ZEND_MODULE_API_NO >= 20100525
                (zend_serialize_data *)var_data TSRMLS_CC
#else
                (zend_serialize_data *)&var_data TSRMLS_CC
#endif
                ) == SUCCESS && !EG(exception)) {
            mpi_buffer_append_map_header_custom_object(buf, 1);
            mpi_serialize_string(buf, (char *)ce->name, ce->name_length);
            mpi_serialize_string(buf, (char *)serialized_data,
                                  serialized_length);
        } else {
            mpi_buffer_append_nil(buf);
        }
        if (serialized_data) {
            efree(serialized_data);
        }
        PHP_VAR_SERIALIZE_DESTROY(var_data);
        return;
    }

    if (ce && ce != PHP_IC_ENTRY &&
        zend_hash_exists(&ce->function_table, "__sleep", sizeof("__sleep"))) {
        INIT_PZVAL(&fname);
        ZVAL_STRINGL(&fname, "__sleep", sizeof("__sleep") - 1, 0);
        MPIG(serialize_lock)++;
        res = call_user_function_ex(CG(function_table), &val, &fname,
                                    &retval_ptr, 0, 0, 1, NULL TSRMLS_CC);
        MPIG(serialize_lock)--;
        if (res == SUCCESS && !EG(exception)) {
            if (retval_ptr) {
                if (HASH_OF(retval_ptr)) {
                    mpi_serialize_class(buf, val, retval_ptr,
                                         var_hash, mode TSRMLS_CC);
                } else {
                    zend_error(E_NOTICE, "%s: __sleep should return an array "
                               "only containing the names of instance-variables "
                               "to serialize", SERIALIZE_FUNC);
                    mpi_buffer_append_nil(buf);
                }
                zval_ptr_dtor(&retval_ptr);
            }
            return;
        }
    }

    if (retval_ptr) {
        zval_ptr_dtor(&retval_ptr);
    }

    ht = Z_OBJPROP_P(val);

    if (ht) {
        i = zend_hash_num_elements(ht);
    }

    if (mode & PHP_MSGPACKI_MODE_PHP) {
        incomplate_class = mpi_buffer_append_class_header(buf, val,
                                                           &i TSRMLS_CC);
    } else {
        mpi_buffer_append_map_header(buf, i);
        incomplate_class = 0;
    }

    if (i > 0) {
        char *key;
        zval **data;
        ulong index;
        uint key_len;
        HashPosition pos;

        zend_hash_internal_pointer_reset_ex(ht, &pos);
        for (;; zend_hash_move_forward_ex(ht, &pos)) {
            i = zend_hash_get_current_key_ex(ht, &key, &key_len,
                                             &index, 0, &pos);
            if (i == HASH_KEY_NON_EXISTANT) {
                break;
            }

            if (incomplate_class && strcmp(key, MAGIC_MEMBER) == 0) {
                continue;
            }

            switch (i) {
                case HASH_KEY_IS_LONG:
                    mpi_serialize_long(buf, index);
                    break;
                case HASH_KEY_IS_STRING:
                    if (mode & PHP_MSGPACKI_MODE_ORIGIN) {
                        if (strlen(key) == key_len - 1) {
                            mpi_serialize_string(buf, key, key_len - 1);
                        } else {
                            uint l;
                            for (l = key_len - 2; l > 0; l--) {
                                if (key[l] == 0x00) {
                                    char *s = key + l + 1;
                                    mpi_serialize_string(buf, s, strlen(s));
                                    break;
                                }
                            }
                            if (l == 0) {
                                mpi_serialize_string(buf, key, key_len - 1);
                            }
                        }
                    } else {
                        mpi_serialize_string(buf, key, key_len - 1);
                    }
                    break;
            }

            if (zend_hash_get_current_data_ex(
                    ht, (void **)&data, &pos) != SUCCESS
                || !data || data == &val
                || (Z_TYPE_PP(data) == IS_ARRAY
                    && Z_ARRVAL_PP(data)->nApplyCount > 1)
                || (Z_TYPE_PP(data) == IS_OBJECT
                    && Z_OBJPROP_PP(data)->nApplyCount > 1
                    && (mode & PHP_MSGPACKI_MODE_ORIGIN))) {
                mpi_buffer_append_nil(buf);
            } else {
                HashTable *htc = NULL;
                if (Z_TYPE_PP(data) == IS_ARRAY) {
                    htc = Z_ARRVAL_PP(data);
                    htc->nApplyCount++;
                } else if (Z_TYPE_PP(data) == IS_OBJECT &&
                           (mode & PHP_MSGPACKI_MODE_ORIGIN)) {
                    htc = Z_OBJPROP_PP(data);
                    htc->nApplyCount++;
                }
                mpi_serialize_intern(buf, *data, var_hash, mode TSRMLS_CC);
                if (htc) {
                    htc->nApplyCount--;
                }
            }
        }
    }
}

static void
mpi_serialize_intern(msgpacki_buffer_t *buf, zval *struc,
                     HashTable *var_hash, long mode TSRMLS_DC)
{
    ulong *var_already;

    if ((mode & PHP_MSGPACKI_MODE_PHP) && var_hash &&
        mpi_add_hash(var_hash, struc,
                     (void *)&var_already TSRMLS_CC) == FAILURE) {
        if (Z_ISREF_P(struc)) {
            mpi_buffer_append_map_header_refecence(buf, 1);
            mpi_serialize_long(buf, 0);
            mpi_serialize_long(buf, (long)*var_already);
            return;
        } else if (Z_TYPE_P(struc) == IS_OBJECT) {
            mpi_buffer_append_map_header_object(buf, 1);
            mpi_serialize_long(buf, 0);
            mpi_serialize_long(buf, (long)*var_already);
            return;
        }
    }

    switch (Z_TYPE_P(struc)) {
        case IS_BOOL:
            if (Z_BVAL_P(struc)) {
                mpi_buffer_append_true(buf);
            } else {
                mpi_buffer_append_false(buf);
            }
            return;
        case IS_NULL:
            mpi_buffer_append_nil(buf);
            return;
        case IS_LONG:
            mpi_serialize_long(buf, Z_LVAL_P(struc));
            return;
        case IS_DOUBLE:
            mpi_serialize_double(buf, Z_DVAL_P(struc));
            /* mpi_serialize_float(buf, Z_DVAL_P(struc)); */
            return;
        case IS_STRING:
            mpi_serialize_string(buf, Z_STRVAL_P(struc), Z_STRLEN_P(struc));
            return;
        case IS_ARRAY:
            mpi_serialize_array(buf, struc, var_hash, mode TSRMLS_CC);
            return;
        case IS_OBJECT:
            mpi_serialize_object(buf, struc, var_hash, mode TSRMLS_CC);
            return;
        default:
            /*
              zend_error(E_WARNING, "type is unsupported, encoded as null");
              mpi_buffer_append_nil(buf);
            */
            mpi_serialize_long(buf, 0);
            return;
    }
}

PHP_MSGPACKI_API void
msgpacki_serialize_session(msgpacki_buffer_t *buf,
                           msgpacki_serialize_data_t *var_hash TSRMLS_DC)
{
    HashTable *ht = Z_ARRVAL_P(PS(http_session_vars));
    long mode = PHP_MSGPACKI_MODE_PHP, num = -1;
    char *key;
    zval **data;
    ulong index;
    uint key_len;
    HashPosition pos;
    int key_type;
    ulong *var_already;

    if ((mode & PHP_MSGPACKI_MODE_PHP) && var_hash) {
        mpi_add_hash(*var_hash, PS(http_session_vars),
                     (void *)&var_already TSRMLS_CC);
    }

    if (ht) {
        num = zend_hash_num_elements(ht);
    }

    if (num < 0) {
        mpi_buffer_append_false(buf);
        return;
    }

    mpi_buffer_append_map_header(buf, num);

    zend_hash_internal_pointer_reset_ex(ht, &pos);
    for (;; zend_hash_move_forward_ex(ht, &pos)) {
        key_type = zend_hash_get_current_key_ex(ht, &key, &key_len,
                                                &index, 0, &pos);
        if (key_type == HASH_KEY_NON_EXISTANT) {
            break;
        } else if (key_type == HASH_KEY_IS_LONG) {
            zend_error(E_NOTICE, "Skipping numeric key %ld", index);
            continue;
        }
        key_len--;
        if (php_get_session_var(key, key_len, &data TSRMLS_CC) == SUCCESS) {
            mpi_serialize_string(buf, key, key_len);
            mpi_serialize_intern(buf, *data, *var_hash, mode TSRMLS_CC);
        }
    }

    smart_str_0(buf);
}

PHP_MSGPACKI_API void
msgpacki_serialize(msgpacki_buffer_t *buf, zval **struc, long mode,
                   msgpacki_serialize_data_t *var_hash TSRMLS_DC)
{
    mpi_serialize_intern(buf, *struc, *var_hash, mode TSRMLS_CC);
    smart_str_0(buf);
}


ZEND_FUNCTION(msgpacki_serialize)
{
    zval **struc;
    msgpacki_serialize_data_t var_hash;
    msgpacki_buffer_t buf = {0};
    zend_bool filter = 0;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "Z", &struc) == FAILURE) {
        return;
    }

    MSGPACKI_FILTER_PRE_SERIALIZE(MPIG(filter).pre_serialize, struc, filter);

    Z_TYPE_P(return_value) = IS_STRING;
    Z_STRVAL_P(return_value) = NULL;
    Z_STRLEN_P(return_value) = 0;

    MSGPACKI_SERIALIZE_INIT(var_hash);
    msgpacki_serialize(&buf, struc, MPIG(mode), &var_hash TSRMLS_CC);
    MSGPACKI_SERIALIZE_DESTROY(var_hash);

    if (filter) {
        zval_ptr_dtor(struc);
    }

    MSGPACKI_FILTER_POST_SERIALIZE(MPIG(filter).post_serialize,
                                   buf, return_value);

    if (buf.c) {
        RETVAL_STRINGL(buf.c, buf.len, 0);
    } else {
        RETVAL_NULL();
    }
}

ZEND_FUNCTION(msgpacki_encode)
{
    zval **struc;
    msgpacki_serialize_data_t var_hash;
    msgpacki_buffer_t buf = {0};
    long options = PHP_MSGPACKI_MODE_ORIGIN;
    zend_bool filter = 0;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "Z|l", &struc, &options) == FAILURE) {
        return;
    }

    MSGPACKI_FILTER_PRE_SERIALIZE(MPIG(filter).pre_serialize, struc, filter);

    Z_TYPE_P(return_value) = IS_STRING;
    Z_STRVAL_P(return_value) = NULL;
    Z_STRLEN_P(return_value) = 0;

    MSGPACKI_SERIALIZE_INIT(var_hash);
    msgpacki_serialize(&buf, struc, options, &var_hash TSRMLS_CC);
    MSGPACKI_SERIALIZE_DESTROY(var_hash);

    if (filter) {
        zval_ptr_dtor(struc);
    }

    MSGPACKI_FILTER_POST_SERIALIZE(MPIG(filter).post_serialize,
                                   buf, return_value);

    if (buf.c) {
        RETVAL_STRINGL(buf.c, buf.len, 0);
    } else {
        RETVAL_NULL();
    }
}

//------------------------------------------------------------------------------
// Unserialize
//------------------------------------------------------------------------------
#define UNSERIALIZE_FUNC "MessagePack unserialize"

#define VAR_ENTRIES_MAX 1024

typedef struct {
    zval *data[VAR_ENTRIES_MAX];
    long used_slots;
    void *next;
} var_entries;

#define MPI_UNSERIALIZE_PARAMETER zval **rval, const unsigned char **p, const unsigned char *max, long mode, msgpacki_unserialize_data_t *var_hash TSRMLS_DC
#define MPI_UNSERIALIZE_PASSTHRU rval, p, max, mode, var_hash TSRMLS_CC

PHP_MSGPACKI_API void
msgpacki_unserialize_destroy(msgpacki_unserialize_data_t *var_hashx)
{
    void *next;
    long i;
    var_entries *var_hash = (*var_hashx)->first;

    while (var_hash) {
        next = var_hash->next;
        efree(var_hash);
        var_hash = next;
    }

    var_hash = (*var_hashx)->first_dtor;

    while (var_hash) {
        for (i = 0; i < var_hash->used_slots; i++) {
            zval_ptr_dtor(&var_hash->data[i]);
        }
        next = var_hash->next;
        efree(var_hash);
        var_hash = next;
    }
}

PHP_MSGPACKI_API void
msgpacki_unserialize_push(msgpacki_unserialize_data_t *var_hashx, zval **rval)
{
    var_entries *var_hash = (*var_hashx)->last;

    if (!var_hash || var_hash->used_slots == VAR_ENTRIES_MAX) {
        var_hash = emalloc(sizeof(var_entries));
        var_hash->used_slots = 0;
        var_hash->next = 0;

        if (!(*var_hashx)->first) {
            (*var_hashx)->first = var_hash;
        } else {
            ((var_entries *)(*var_hashx)->last)->next = var_hash;
        }

        (*var_hashx)->last = var_hash;
    }

    var_hash->data[var_hash->used_slots++] = *rval;
}

static inline int
mpi_unserialize_pop(msgpacki_unserialize_data_t *var_hashx)
{
    var_entries *var_hash = (*var_hashx)->last;
    long id;

    if (!var_hash) {
        return !SUCCESS;
    }

    id = var_hash->used_slots;
    if (id < 0 || !var_hash->data[id]) {
        return !SUCCESS;
    }

    zval_ptr_dtor(&var_hash->data[id]);
    var_hash->used_slots--;

    return SUCCESS;
}


static inline void
mpi_unserialize_push_dtor(msgpacki_unserialize_data_t *var_hashx, zval **rval)
{
    var_entries *var_hash = (*var_hashx)->last_dtor;

    if (!var_hash || var_hash->used_slots == VAR_ENTRIES_MAX) {
        var_hash = emalloc(sizeof(var_entries));
        var_hash->used_slots = 0;
        var_hash->next = 0;

        if (!(*var_hashx)->first_dtor) {
            (*var_hashx)->first_dtor = var_hash;
        } else {
            ((var_entries *) (*var_hashx)->last_dtor)->next = var_hash;
        }

        (*var_hashx)->last_dtor = var_hash;
    }

    Z_ADDREF_PP(rval);
    var_hash->data[var_hash->used_slots++] = *rval;
}

static int
mpi_unserialize_access(msgpacki_unserialize_data_t *var_hashx,
                       long id, zval ***store)
{
    var_entries *var_hash = (*var_hashx)->first;

    while (id >= VAR_ENTRIES_MAX && var_hash &&
           var_hash->used_slots == VAR_ENTRIES_MAX) {
        var_hash = var_hash->next;
        id -= VAR_ENTRIES_MAX;
    }

    if (!var_hash) {
        return !SUCCESS;
    }

    if (id < 0 || id >= var_hash->used_slots) {
        return !SUCCESS;
    }

    *store = &var_hash->data[id];

    return SUCCESS;
}

union mpi_unserialize_cast_block_t {
    char buffer[8];
    uint8_t u8;
    uint16_t u16;
    uint32_t u32;
    uint64_t u64;
    int8_t i8;
    int16_t i16;
    int32_t i32;
    int64_t i64;
    float f;
    double d;
};

#define MPI_ZVAL_ALLOC(zp) ALLOC_INIT_ZVAL(zp)
#define MPI_ZVAL_FREE(zp)  zval_dtor(zp); FREE_ZVAL(zp)

#define MPI_UNSERIALIZE_CAST_BLOCK(c, n, p, l) \
    union mpi_unserialize_cast_block_t cb;     \
    if ((l - c) < n) {                         \
        return 0;                              \
    }                                          \
    *p = c + n;                                \
    memcpy(cb.buffer, c, n)

#define MPI_UNSERIALIZE_ACCESS(k, v, r)                                 \
    zval *k, *v;                                                        \
    if (!var_hash) {                                                    \
        return 0;                                                       \
    }                                                                   \
    MPI_ZVAL_ALLOC(k);                                                  \
    if (!msgpacki_unserialize(&k, p, max, mode, NULL TSRMLS_CC)) {      \
        MPI_ZVAL_FREE(k);                                               \
        return 0;                                                       \
    }                                                                   \
    MPI_ZVAL_ALLOC(v);                                                  \
    if (!msgpacki_unserialize(&v, p, max, mode, NULL TSRMLS_CC)) {      \
        MPI_ZVAL_FREE(k);                                               \
        MPI_ZVAL_FREE(v);                                               \
        return 0;                                                       \
    }                                                                   \
    if (Z_LVAL_P(v) == -1 ||                                            \
        mpi_unserialize_access(var_hash, Z_LVAL_P(v), &r) != SUCCESS) { \
        MPI_ZVAL_FREE(k);                                               \
        MPI_ZVAL_FREE(v);                                               \
        return 0;                                                       \
    }                                                                   \
    MPI_ZVAL_FREE(k);                                                   \
    MPI_ZVAL_FREE(v)

#define MPI_UNSERIALIZE_PUSH()                     \
    if (var_hash) {                                \
        msgpacki_unserialize_push(var_hash, rval); \
    }

static inline int
mpi_unserialize_nested_array(MPI_UNSERIALIZE_PARAMETER,
                             HashTable *ht, long elements)
{
    while (elements-- > 0) {
        zval *data;

        MPI_ZVAL_ALLOC(data);

        if (var_hash) {
            msgpacki_unserialize_push(var_hash, &data);
        }

        if (!msgpacki_unserialize(&data, p, max, mode, var_hash TSRMLS_CC)) {
            MPI_ZVAL_FREE(data);
            return 0;
        }

        zend_hash_next_index_insert(ht, &data, sizeof(data), NULL);
    }

    return 1;
}

static inline zend_class_entry*
mpi_unserialize_class_entry(char *class_name, long class_name_len,
                            zend_bool *incomplete_class TSRMLS_DC)
{
    zend_class_entry *ce, **pce;
    zval *user_func;
    zval *retval_ptr;
    zval **args[1];
    zval *arg_func_name;

    do {
        /* Try to find class directly */
        if (zend_lookup_class(class_name, class_name_len,
                              &pce TSRMLS_CC) == SUCCESS) {
            ce = *pce;
            break;
        }

        /* ?: msgpacki.unserialize_callback_func */

        /* Check for unserialize callback */
        if ((PG(unserialize_callback_func) == NULL) ||
            (PG(unserialize_callback_func)[0] == '\0')) {
            *incomplete_class = 1;
            ce = PHP_IC_ENTRY;
            break;
        }

        /* Call unserialize callback */
        MAKE_STD_ZVAL(user_func);
        ZVAL_STRING(user_func, PG(unserialize_callback_func), 1);
        args[0] = &arg_func_name;
        MAKE_STD_ZVAL(arg_func_name);
        ZVAL_STRING(arg_func_name, class_name, 1);
        if (call_user_function_ex(CG(function_table), NULL,
                                  user_func, &retval_ptr, 1, args, 0,
                                  NULL TSRMLS_CC) != SUCCESS) {
            zend_error(E_WARNING, "%s: defined (%s) but not found",
                       UNSERIALIZE_FUNC, user_func->value.str.val);
            *incomplete_class = 1;
            ce = PHP_IC_ENTRY;
            zval_ptr_dtor(&user_func);
            zval_ptr_dtor(&arg_func_name);
            break;
        }
        if (retval_ptr) {
            zval_ptr_dtor(&retval_ptr);
        }

        /* The callback function may have defined the class */
        if (zend_lookup_class(class_name, class_name_len,
                              &pce TSRMLS_CC) == SUCCESS) {
            ce = *pce;
        } else {
            zend_error(E_WARNING, "%s: Function %s() hasn't defined "
                       "the class it was called for", UNSERIALIZE_FUNC,
                       user_func->value.str.val);
            *incomplete_class = 1;
            ce = PHP_IC_ENTRY;
        }

        zval_ptr_dtor(&user_func);
        zval_ptr_dtor(&arg_func_name);
        break;
    } while (1);

    return ce;
}

static inline int
mpi_unserialize_nested_map(MPI_UNSERIALIZE_PARAMETER, long elements)
{
    zend_class_entry *ce = NULL;
    zend_bool php_object = 0;
    zend_bool class_object = 0;
    zend_bool incomplete_class = 0;
    zval *key, *data;
    HashTable *ht;
    long ref;

    /* key type check */
    const unsigned char *cursor = *p;
    unsigned char yych = *cursor;

    if (yych == 0xc0) {
        php_object = 1;
    }

    if ((mode & PHP_MSGPACKI_MODE_PHP) && php_object) {
        /* PHP objects */
        MPI_ZVAL_ALLOC(key);
        if (!msgpacki_unserialize(&key, p, max, mode, NULL TSRMLS_CC) ||
            Z_TYPE_P(key) != IS_NULL) {
            MPI_ZVAL_FREE(key);
            return 0;
        }
        MPI_ZVAL_FREE(key);

        MPI_ZVAL_ALLOC(data);
        if (!msgpacki_unserialize(&data, p, max, mode, NULL TSRMLS_CC)) {
            MPI_ZVAL_FREE(data);
            return 0;
        }

        if (Z_TYPE_P(data) == IS_STRING) {
            /* PHP class */
            class_object = 1;

            ce = mpi_unserialize_class_entry(Z_STRVAL_P(data), Z_STRLEN_P(data),
                                              &incomplete_class TSRMLS_CC);

            MPI_UNSERIALIZE_PUSH();
            object_init_ex(*rval, ce);
            if (incomplete_class) {
                php_store_class_name(*rval, Z_STRVAL_P(data), Z_STRLEN_P(data));
            }

            MPI_ZVAL_FREE(data);

            ht = Z_OBJPROP_PP(rval);
        } else if (Z_TYPE_P(data) == IS_LONG) {
            ref = Z_LVAL_P(data);
            MPI_ZVAL_FREE(data);

            switch (ref) {
                case MPI_TYPE_REFERENCE:
                {
                    /* reference [R] */
                    zval **retval;
                    MPI_UNSERIALIZE_ACCESS(dummy, id, retval);

                    if (*rval != NULL) {
                        zval_ptr_dtor(rval);
                    }

                    *rval = *retval;
                    Z_ADDREF_PP(rval);
                    Z_SET_ISREF_PP(rval);
                    return 1;
                }
                case MPI_TYPE_OBJECT:
                {
                    /* object [r] */
                    zval **retval;
                    MPI_UNSERIALIZE_ACCESS(dummy, id, retval);
                    MPI_UNSERIALIZE_PUSH();

                    if (*rval != NULL) {
                        zval_ptr_dtor(rval);
                    }

                    *rval = *retval;
                    Z_ADDREF_PP(rval);
                    Z_UNSET_ISREF_PP(rval);
                    return 1;
                }
                case MPI_TYPE_CUSTOM_OBJECT:
                {
                    /* object_custom [C] */
                    zval *class_name, *class_data;
                    zend_class_entry *ce;
                    zend_bool incomplete_class = 0;

                    MPI_UNSERIALIZE_PUSH();

                    MPI_ZVAL_ALLOC(class_name);
                    if (!msgpacki_unserialize(&class_name, p, max, mode,
                                               NULL TSRMLS_CC)) {
                        MPI_ZVAL_FREE(class_name);
                        return 0;
                    }

                    MPI_ZVAL_ALLOC(class_data);
                    if (!msgpacki_unserialize(&class_data, p, max, mode,
                                               NULL TSRMLS_CC)) {
                        MPI_ZVAL_FREE(class_name);
                        MPI_ZVAL_FREE(class_data);
                        return 0;
                    }

                    if (!Z_TYPE_P(class_name) == IS_STRING ||
                        !Z_TYPE_P(class_data) == IS_STRING) {
                        MPI_ZVAL_FREE(class_name);
                        MPI_ZVAL_FREE(class_data);
                        return 0;
                    }

                    ce = mpi_unserialize_class_entry(Z_STRVAL_P(class_name),
                                                     Z_STRLEN_P(class_name),
                                                     &incomplete_class
                                                     TSRMLS_CC);
                    if (ce->unserialize == NULL) {
                        zend_error(E_WARNING, "%s: Class %s has no unserializer",
                                   UNSERIALIZE_FUNC, ce->name);
                        object_init_ex(*rval, ce);
                    } else {
                        php_unserialize_data_t var_data;
                        PHP_VAR_UNSERIALIZE_INIT(var_data);
                        if (ce->unserialize(
                                rval, ce, (const unsigned char*)
                                Z_STRVAL_P(class_data), Z_STRLEN_P(class_data),
#if ZEND_MODULE_API_NO >= 20100525
                                (zend_unserialize_data *)var_data TSRMLS_CC
#else
                                (zend_unserialize_data *)&var_data TSRMLS_CC
#endif
                                ) != SUCCESS) {
                            PHP_VAR_UNSERIALIZE_DESTROY(var_data);
                            return 0;
                        }
                        PHP_VAR_UNSERIALIZE_DESTROY(var_data);
                    }
                    if (incomplete_class) {
                        php_store_class_name(*rval, Z_STRVAL_P(class_name),
                                             Z_STRLEN_P(class_name));
                    }

                    MPI_ZVAL_FREE(class_name);
                    MPI_ZVAL_FREE(class_data);

                    return 1;
                }
                default:
                    return 0;
            }
        } else {
            MPI_ZVAL_FREE(data);
            return 0;
        }
        elements--;
    } else if (php_object) {
        MPI_ZVAL_ALLOC(key);
        if (!msgpacki_unserialize(&key, p, max, mode, NULL TSRMLS_CC) ||
            Z_TYPE_P(key) != IS_NULL) {
            MPI_ZVAL_FREE(key);
            return 0;
        }
        MPI_ZVAL_FREE(key);

        MPI_ZVAL_ALLOC(data);
        if (!msgpacki_unserialize(&data, p, max, mode, NULL TSRMLS_CC)) {
            MPI_ZVAL_FREE(data);
            return 0;
        }
        MPI_ZVAL_FREE(data);

        MPI_UNSERIALIZE_PUSH();
        INIT_PZVAL(*rval);
        object_init(*rval);
        ht = Z_OBJPROP_PP(rval);

        elements--;
    } else {
        MPI_UNSERIALIZE_PUSH();
        INIT_PZVAL(*rval);
        /*
        if (mode & PHP_MSGPACKI_MODE_PHP) {
            array_init_size(*rval, elements);
            ht = Z_ARRVAL_PP(rval);
        } else {
            object_init(*rval);
            ht = Z_OBJPROP_PP(rval);
        }
        */
        array_init_size(*rval, elements);
        ht = Z_ARRVAL_PP(rval);
    }

    while (elements-- > 0) {
        zval **old_data;

        MPI_ZVAL_ALLOC(key);
        if (!msgpacki_unserialize(&key, p, max, mode, NULL TSRMLS_CC)) {
            MPI_ZVAL_FREE(key);
            return 0;
        }

        if (Z_TYPE_P(key) != IS_LONG && Z_TYPE_P(key) != IS_STRING) {
            MPI_ZVAL_FREE(key);
            return 0;
        }

        MPI_ZVAL_ALLOC(data);
        if (!msgpacki_unserialize(&data, p, max, mode, var_hash TSRMLS_CC)) {
            MPI_ZVAL_FREE(key);
            MPI_ZVAL_FREE(data);
            return 0;
        }

        if (!class_object) {
            switch (Z_TYPE_P(key)) {
                case IS_LONG:
                    if (zend_hash_index_find(ht, Z_LVAL_P(key),
                                             (void **)&old_data) == SUCCESS) {
                        mpi_unserialize_push_dtor(var_hash, old_data);
                    }
                    zend_hash_index_update(ht, Z_LVAL_P(key),
                                           &data, sizeof(data), NULL);
                    break;
                case IS_STRING:
                    if (zend_symtable_find(ht,
                                           Z_STRVAL_P(key), Z_STRLEN_P(key) + 1,
                                           (void **)&old_data)==SUCCESS) {
                        mpi_unserialize_push_dtor(var_hash, old_data);
                    }
                    zend_symtable_update(ht,
                                         Z_STRVAL_P(key), Z_STRLEN_P(key) + 1,
                                         &data, sizeof(data), NULL);
                    break;
            }
        } else {
            convert_to_string(key);
            zend_hash_update(ht, Z_STRVAL_P(key), Z_STRLEN_P(key) + 1, &data,
                             sizeof data, NULL);
        }

        MPI_ZVAL_FREE(key);
    }

    if (class_object) {
        zval *retval_ptr = NULL;
        zval fname;

        if (Z_OBJCE_PP(rval) != PHP_IC_ENTRY &&
            zend_hash_exists(&Z_OBJCE_PP(rval)->function_table,
                             "__wakeup", sizeof("__wakeup"))) {
            INIT_PZVAL(&fname);
            ZVAL_STRINGL(&fname, "__wakeup", sizeof("__wakeup") - 1, 0);
            MPIG(serialize_lock)++;
            call_user_function_ex(CG(function_table), rval, &fname,
                                  &retval_ptr, 0, 0, 1, NULL TSRMLS_CC);
            MPIG(serialize_lock)--;
        }

        if (retval_ptr) {
            zval_ptr_dtor(&retval_ptr);
        }
    }

    return 1;
}

#if defined(_MSC_VER)
#define SWITCH_RANGE_BEGIN(byte)     if (0) {}
#define SWITCH_RANGE(byte, from, to) else if (from <= byte && byte <= to)
#define SWITCH_RANGE_DEFAULT()       else
#define SWITCH_RANGE_END()
#else
#define SWITCH_RANGE_BEGIN(byte)     switch (byte) {
#define SWITCH_RANGE(byte, from, to) case from ... to:
#define SWITCH_RANGE_DEFAULT()       default:
#define SWITCH_RANGE_END()           }
#endif

PHP_MSGPACKI_API int
msgpacki_unserialize(MPI_UNSERIALIZE_PARAMETER)
{
    const unsigned char *cursor, *limit;
    unsigned char yych;
    long elements;

    limit = max;
    cursor = *p;

    if (cursor >= limit) {
        return 0;
    }

    yych = *cursor;

    SWITCH_RANGE_BEGIN(yych)

    /* Positive Fixnum */
    SWITCH_RANGE(yych, 0x00, 0x7f)
    {
        MPI_UNSERIALIZE_PUSH();
        INIT_PZVAL(*rval);
        ZVAL_LONG(*rval, *(uint8_t*)cursor);
        *p = (++cursor);
        return 1;
    }
    /* Negative Fixnum */
    SWITCH_RANGE(yych, 0xe0, 0xff)
    {
        MPI_UNSERIALIZE_PUSH();
        INIT_PZVAL(*rval);
        ZVAL_LONG(*rval, *(int8_t*)cursor);
        *p = (++cursor);
        return 1;
    }
    /* FixRaw */
    SWITCH_RANGE(yych, 0xa0, 0xbf)
    {
        MPI_UNSERIALIZE_PUSH();
        elements = yych & 0x1f;
        *p = (++cursor);
        INIT_PZVAL(*rval);
        if (elements == 0) {
            ZVAL_EMPTY_STRING(*rval);
            return 1;
        }
        *p += elements;
        if (*p > max) {
            ZVAL_EMPTY_STRING(*rval);
            return 1;
        }
        ZVAL_STRINGL(*rval, (char *)cursor, elements, 1);
        return 1;
    }
    /* FixArray */
    SWITCH_RANGE(yych, 0x90, 0x9f)
    {
        MPI_UNSERIALIZE_PUSH();
        elements = yych & 0x0f;
        *p = (++cursor);
        if (elements < 0) {
            return 0;
        }
        INIT_PZVAL(*rval);
        array_init_size(*rval, elements);
        return mpi_unserialize_nested_array(MPI_UNSERIALIZE_PASSTHRU,
                                             Z_ARRVAL_PP(rval), elements);
    }
    /* FixMap */
    SWITCH_RANGE(yych, 0x80, 0x8f)
    {
        elements = yych & 0x0f;
        *p = (++cursor);
        if (elements < 0) {
            MPI_UNSERIALIZE_PUSH();
            return 0;
        }
        if (elements == 0) {
            MPI_UNSERIALIZE_PUSH();
            INIT_PZVAL(*rval);
            /* object_init(*rval); */
            array_init(*rval);
            return 1;
        }
        return mpi_unserialize_nested_map(MPI_UNSERIALIZE_PASSTHRU, elements);
    }
    /* Variable */
    SWITCH_RANGE(yych, 0xc0, 0xdf)
    {
        cursor++;
        switch (yych) {
            case 0xc0: // nil
                MPI_UNSERIALIZE_PUSH();
                *p = cursor;
                INIT_PZVAL(*rval);
                ZVAL_NULL(*rval);
                return 1;
            //case 0xc1: // string
            case 0xc2: // false
                MPI_UNSERIALIZE_PUSH();
                *p = cursor;
                INIT_PZVAL(*rval);
                ZVAL_BOOL(*rval, 0);
                return 1;
            case 0xc3: // true
                MPI_UNSERIALIZE_PUSH();
                *p = cursor;
                INIT_PZVAL(*rval);
                ZVAL_BOOL(*rval, 1);
                return 1;
            //case 0xc4:
            //case 0xc5:
            //case 0xc6:
            //case 0xc7:
            //case 0xc8:
            //case 0xc9:
            /* int n = 1 << (((unsigned int)*p) & 0x03) */
            case 0xca: // float : 5byte
            {
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 4, p, limit);
                MPI_UNSERIALIZE_PUSH();
                cb.u32 = _msgpack_be32(cb.u32);
                INIT_PZVAL(*rval);
                ZVAL_DOUBLE(*rval, cb.f);
                return 1;
            }
            case 0xcb: // double : 9byte
            {
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 8, p, limit);
                MPI_UNSERIALIZE_PUSH();
                cb.u64 = _msgpack_be64(cb.u64);
                INIT_PZVAL(*rval);
                ZVAL_DOUBLE(*rval, cb.d);
                return 1;
            }
            case 0xcc: // unsigned int  8
            {
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 1, p, limit);
                MPI_UNSERIALIZE_PUSH();
                INIT_PZVAL(*rval);
                ZVAL_LONG(*rval, cb.u8);
                return 1;
            }
            case 0xcd: // unsigned int 16
            {
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 2, p, limit);
                MPI_UNSERIALIZE_PUSH();
                INIT_PZVAL(*rval);
                ZVAL_LONG(*rval, _msgpack_be16(cb.u16));
                return 1;
            }
            case 0xce: // unsigned int 32
            {
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 4, p, limit);
                MPI_UNSERIALIZE_PUSH();
                INIT_PZVAL(*rval);
                ZVAL_LONG(*rval, _msgpack_be32(cb.u32));
                return 1;
            }
            case 0xcf: // unsigned int 64
            {
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 8, p, limit);
                MPI_UNSERIALIZE_PUSH();
                INIT_PZVAL(*rval);
                ZVAL_LONG(*rval, _msgpack_be64(cb.u64));
                return 1;
            }
            case 0xd0: // signed int  8
            {
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 1, p, limit);
                MPI_UNSERIALIZE_PUSH();
                INIT_PZVAL(*rval);
                ZVAL_LONG(*rval, cb.i8);
                return 1;
            }
            case 0xd1: // signed int 16
            {
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 2, p, limit);
                MPI_UNSERIALIZE_PUSH();
                INIT_PZVAL(*rval);
                ZVAL_LONG(*rval, (int16_t)_msgpack_be16(cb.i16));
                return 1;
            }
            case 0xd2: // signed int 32
            {
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 4, p, limit);
                MPI_UNSERIALIZE_PUSH();
                INIT_PZVAL(*rval);
                ZVAL_LONG(*rval, (int32_t)_msgpack_be32(cb.i32));
                return 1;
            }
            case 0xd3: // signed int 64
            {
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 8, p, limit);
                MPI_UNSERIALIZE_PUSH();
                INIT_PZVAL(*rval);
                ZVAL_LONG(*rval, (int64_t)_msgpack_be64(cb.i64));
                return 1;
            }
            //case 0xd4:
            //case 0xd5:
            //case 0xd6: // big integer 16
            //case 0xd7: // big integer 32
            //case 0xd8: // big float 16
            //case 0xd9: // big float 32
            /* int n = 2 << (((unsigned int)*p) & 0x01); */
            case 0xda: // raw 16
            {
                uint16_t items;
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 2, p, limit);
                MPI_UNSERIALIZE_PUSH();
                items = _msgpack_be16(cb.u16);
                if (items < 0) {
                    return 0;
                }
                INIT_PZVAL(*rval);
                if (items == 0) {
                    ZVAL_EMPTY_STRING(*rval);
                    return 1;
                }
                cursor = *p;
                *p += items;
                if (*p > max) {
                    ZVAL_EMPTY_STRING(*rval);
                    return 1;
                }
                ZVAL_STRINGL(*rval, (char *)cursor, items, 1);
                return 1;
            }
            case 0xdb: // raw 32
            {
                uint32_t items;
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 4, p, limit);
                MPI_UNSERIALIZE_PUSH();
                items = _msgpack_be32(cb.u32);
                if (items < 0) {
                    return 0;
                }
                INIT_PZVAL(*rval);
                if (items == 0) {
                    ZVAL_EMPTY_STRING(*rval);
                    return 1;
                }
                cursor = *p;
                *p += items;
                if (*p > max) {
                    ZVAL_EMPTY_STRING(*rval);
                    return 1;
                }
                ZVAL_STRINGL(*rval, (char *)cursor, items, 1);
                return 1;
            }
            case 0xdc: // array 16
            {
                uint16_t items;
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 2, p, limit);
                MPI_UNSERIALIZE_PUSH();
                items = _msgpack_be16(cb.u16);
                if (items < 0) {
                    return 0;
                }
                INIT_PZVAL(*rval);
                array_init_size(*rval, items);
                return mpi_unserialize_nested_array(MPI_UNSERIALIZE_PASSTHRU,
                                                     Z_ARRVAL_PP(rval), items);
            }
            case 0xdd: // array 32
            {
                uint32_t items;
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 4, p, limit);
                MPI_UNSERIALIZE_PUSH();
                items = _msgpack_be32(cb.u32);
                if (items < 0) {
                    return 0;
                }
                INIT_PZVAL(*rval);
                array_init_size(*rval, items);
                return mpi_unserialize_nested_array(MPI_UNSERIALIZE_PASSTHRU,
                                                     Z_ARRVAL_PP(rval), items);
            }
            case 0xde: // map 16
            {
                uint16_t items;
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 2, p, limit);
                items = _msgpack_be16(cb.u16);
                if (items < 0) {
                    MPI_UNSERIALIZE_PUSH();
                    return 0;
                }
                if (items == 0) {
                    MPI_UNSERIALIZE_PUSH();
                    INIT_PZVAL(*rval);
                    /* object_init(*rval); */
                    array_init(*rval);
                    return 1;
                }
                return mpi_unserialize_nested_map(MPI_UNSERIALIZE_PASSTHRU,
                                                   items);
            }
            case 0xdf: // map 32
            {
                uint32_t items;
                MPI_UNSERIALIZE_CAST_BLOCK(cursor, 4, p, limit);
                items = _msgpack_be32(cb.u32);
                if (items < 0) {
                    MPI_UNSERIALIZE_PUSH();
                    return 0;
                }
                if (items == 0) {
                    MPI_UNSERIALIZE_PUSH();
                    INIT_PZVAL(*rval);
                    /* object_init(*rval); */
                    array_init(*rval);
                    return 1;
                }
                return mpi_unserialize_nested_map(MPI_UNSERIALIZE_PASSTHRU,
                                                   items);
            }
            default:
                MPI_UNSERIALIZE_PUSH();
                zend_error(E_WARNING, "%s: Invalid byte: %x",
                           UNSERIALIZE_FUNC, yych);
                return 0;
        }
    }
    /* default */
    SWITCH_RANGE_DEFAULT()
    {
        MPI_UNSERIALIZE_PUSH();
        zend_error(E_WARNING, "%s: Invalid byte: %x", UNSERIALIZE_FUNC, yych);
        return 0;
    }

    SWITCH_RANGE_END()

    return 0;
}


ZEND_FUNCTION(msgpacki_unserialize)
{
    char *buf = NULL;
    int buf_len;
    const unsigned char *p;
    msgpacki_unserialize_data_t var_hash;
    zval *filter = NULL;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "s", &buf, &buf_len) == FAILURE) {
        RETURN_FALSE;
    }

    if (buf_len == 0) {
        RETURN_FALSE;
    }

    MSGPACKI_FILTER_PRE_UNSERIALIZE(MPIG(filter).pre_unserialize,
                                    filter, buf, buf_len);

    p = (const unsigned char*)buf;
    MSGPACKI_UNSERIALIZE_INIT(var_hash);

    if (MPIG(unserialize).level == 1) {
        msgpacki_unserialize_push(&var_hash, &return_value);
    }

    if (!msgpacki_unserialize(&return_value, &p, p + buf_len,
                              MPIG(mode), &var_hash TSRMLS_CC) ||
        ((char*)p - buf) != buf_len) {
        MSGPACKI_UNSERIALIZE_DESTROY(var_hash);
        if (filter) {
            zval_ptr_dtor(&filter);
        }
        zval_dtor(return_value);
        zend_error(E_NOTICE, "%s: Error at offset %ld of %d bytes",
                   UNSERIALIZE_FUNC, (long)((char*)p - buf), buf_len);
        RETURN_FALSE;
    }

    MSGPACKI_UNSERIALIZE_DESTROY(var_hash);

    if (filter) {
        zval_ptr_dtor(&filter);
    }

    MSGPACKI_FILTER_POST_UNSERIALIZE(MPIG(filter).post_unserialize,
                                     return_value);
}

ZEND_FUNCTION(msgpacki_decode)
{
    char *buf = NULL;
    int buf_len;
    const unsigned char *p;
    msgpacki_unserialize_data_t var_hash;
    long options = PHP_MSGPACKI_MODE_ORIGIN;
    zval *filter = NULL;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "s|l", &buf, &buf_len, &options) == FAILURE) {
        RETURN_FALSE;
    }

    if (buf_len == 0) {
        RETURN_FALSE;
    }

    MSGPACKI_FILTER_PRE_UNSERIALIZE(MPIG(filter).pre_unserialize,
                                    filter, buf, buf_len);

    p = (const unsigned char*)buf;
    MSGPACKI_UNSERIALIZE_INIT(var_hash);

    if (MPIG(unserialize).level == 1) {
        msgpacki_unserialize_push(&var_hash, &return_value);
    }

    if (!msgpacki_unserialize(&return_value, &p, p + buf_len,
                              options, &var_hash TSRMLS_CC) ||
        ((char*)p - buf) != buf_len) {
        MSGPACKI_UNSERIALIZE_DESTROY(var_hash);
        if (filter) {
            zval_ptr_dtor(&filter);
        }
        zval_dtor(return_value);
        zend_error(E_NOTICE, "%s: Error at offset %ld of %d bytes",
                   UNSERIALIZE_FUNC, (long)((char*)p - buf), buf_len);
        RETURN_FALSE;
    }

    MSGPACKI_UNSERIALIZE_DESTROY(var_hash);

    if (filter) {
        zval_ptr_dtor(&filter);
    }

    MSGPACKI_FILTER_POST_UNSERIALIZE(MPIG(filter).post_unserialize,
                                     return_value);
}
