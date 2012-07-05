#ifndef MSGPACKI_DEBUG_H
#define MSGPACKI_DEBUG_H

#include "php_verdep.h"

//------------------------------------------------------------------------------
// debug
//------------------------------------------------------------------------------

#define COMMON (Z_ISREF_PP(struc) ? "&" : "")
static inline void _var_dump(zval **struc, int level TSRMLS_DC);

static int php_array_element_dump(zval **zv TSRMLS_DC, int num_args, va_list args, zend_hash_key *hash_key)
{
    int level;

    level = va_arg(args, int);

    if (hash_key->nKeyLength == 0) {
        php_printf("%*c[%ld]=>\n", level + 1, ' ', hash_key->h);
    } else {
        php_printf("%*c[\"", level + 1, ' ');
        PHPWRITE(hash_key->arKey, hash_key->nKeyLength - 1);
        php_printf("\"]=>\n");
    }
    _var_dump(zv, level + 2 TSRMLS_CC);
    return 0;
}

static int php_object_property_dump(zval **zv TSRMLS_DC, int num_args, va_list args, zend_hash_key *hash_key)
{
    int level;
#if ZEND_MODULE_API_NO >= 20100525
    const char *prop_name, *class_name;
#else
    char *prop_name, *class_name;
#endif

    level = va_arg(args, int);

    if (hash_key->nKeyLength == 0) { /* numeric key */
        php_printf("%*c[%ld]=>\n", level + 1, ' ', hash_key->h);
    } else {
        int unmangle = zend_unmangle_property_name(hash_key->arKey, hash_key->nKeyLength - 1, &class_name, &prop_name);
        php_printf("%*c[", level + 1, ' ');

        if (class_name && unmangle == SUCCESS) {
            if (class_name[0] == '*') {
                php_printf("\"%s\":protected", prop_name);
            } else {
                php_printf("\"%s\":\"%s\":private", prop_name, class_name);
            }
        } else {
            php_printf("\"");
            PHPWRITE(hash_key->arKey, hash_key->nKeyLength - 1);
            php_printf("\"");
        }
        ZEND_PUTS("]=>\n");
    }
    _var_dump(zv, level + 2 TSRMLS_CC);
    return 0;
}
static inline void _var_dump(zval **struc, int level TSRMLS_DC)
{
    HashTable *myht;
#if ZEND_MODULE_API_NO >= 20100525
    const char *class_name;
#else
    char *class_name;
#endif
    zend_uint class_name_len;
    int (*php_element_dump_func)(zval** TSRMLS_DC, int, va_list, zend_hash_key*);
    int is_temp;

    if (level > 1) {
        php_printf("%*c", level - 1, ' ');
    }
    switch (Z_TYPE_PP(struc)) {
        case IS_BOOL:
            php_printf("%sbool(%s)\n", COMMON, Z_LVAL_PP(struc) ? "true" : "false");
            break;
        case IS_NULL:
            php_printf("%sNULL\n", COMMON);
            break;
        case IS_LONG:
            php_printf("%sint(%ld)\n", COMMON, Z_LVAL_PP(struc));
            break;
        case IS_DOUBLE:
            php_printf("%sfloat(%.*G)\n", COMMON, (int) EG(precision), Z_DVAL_PP(struc));
            break;
        case IS_STRING:
            php_printf("%sstring(%d) \"", COMMON, Z_STRLEN_PP(struc));
            PHPWRITE(Z_STRVAL_PP(struc), Z_STRLEN_PP(struc));
            PUTS("\"\n");
            break;
        case IS_ARRAY:
            myht = Z_ARRVAL_PP(struc);
            if (++myht->nApplyCount > 1) {
                PUTS("*RECURSION*\n");
                --myht->nApplyCount;
                return;
            }
            php_printf("%sarray(%d) {\n", COMMON, zend_hash_num_elements(myht));
            php_element_dump_func = php_array_element_dump;
            is_temp = 0;
            goto head_done;
        case IS_OBJECT:
#if ZEND_MODULE_API_NO >= 20090626
            myht = Z_OBJDEBUG_PP(struc, is_temp);
#else
            myht = Z_OBJPROP_PP(struc);
#endif
            if (myht && ++myht->nApplyCount > 1) {
                PUTS("*RECURSION*\n");
                --myht->nApplyCount;
                return;
            }
            if (Z_OBJ_HANDLER(**struc, get_class_name)) {
                Z_OBJ_HANDLER(**struc, get_class_name)(*struc, &class_name, &class_name_len, 0 TSRMLS_CC);
                php_printf("%sobject(%s)#%d (%d) {\n", COMMON, class_name, Z_OBJ_HANDLE_PP(struc), myht ? zend_hash_num_elements(myht) : 0);
                efree((char*)class_name);
            } else {
                php_printf("%sobject(unknown class)#%d (%d) {\n", COMMON, Z_OBJ_HANDLE_PP(struc), myht ? zend_hash_num_elements(myht) : 0);
            }
            php_element_dump_func = php_object_property_dump;
        head_done:
            if (myht) {
#if ZEND_MODULE_API_NO >= 20090626
                zend_hash_apply_with_arguments(myht TSRMLS_CC, (apply_func_args_t) php_element_dump_func, 1, level);
#else
                zend_hash_apply_with_arguments(myht, (apply_func_args_t) php_element_dump_func, 1, level);
#endif
                --myht->nApplyCount;
                if (is_temp) {
                    zend_hash_destroy(myht);
                    efree(myht);
                }
            }
            if (level > 1) {
                php_printf("%*c", level-1, ' ');
            }
            PUTS("}\n");
            break;
        case IS_RESOURCE: {
            const char *type_name = zend_rsrc_list_get_rsrc_type(Z_LVAL_PP(struc) TSRMLS_CC);
            php_printf("%sresource(%ld) of type (%s)\n", COMMON, Z_LVAL_PP(struc), type_name ? type_name : "Unknown");
            break;
        }
        default:
            php_printf("%sUNKNOWN:0\n", COMMON);
            break;
    }
}

#define MPI_VAR_DUMP(v) php_printf("LINE:%d\n", __LINE__); _var_dump(v, 1 TSRMLS_CC)

#define _debug(str) php_printf("[DEBUG] => %s\n", str)
#define _debug_printf(...) php_printf(__VA_ARGS__)

#define _debug_alert_ex(format, ...) \
    php_printf("\033[1;4;35m"format"\033[0m", __VA_ARGS__)
#define _debug_crit_ex(format, ...) \
    php_printf("\033[1;4;34m"format"\033[0m", __VA_ARGS__)
#define _debug_err_ex(format, ...) \
    php_printf("\033[31m"format"\033[0m", __VA_ARGS__)
#define _debug_warn_ex(format, ...) \
    php_printf("\033[33m"format"\033[0m", __VA_ARGS__)
#define _debug_notice_ex(format, ...) \
    php_printf("\033[32m"format"\033[0m", __VA_ARGS__)
#define _debug_info_ex(format, ...) \
    php_printf("\033[36m"format"\033[0m", __VA_ARGS__)
#define _debug_msg_ex(format, ...) \
    php_printf("\033[37m"format"\033[0m", __VA_ARGS__)

#define _debug_alert(str) _debug_alert_ex("%s\n", str)
#define _debug_crit(str) _debug_crit_ex("%s\n", str)
#define _debug_err(str) _debug_err_ex("%s\n", str)
#define _debug_warn(str) _debug_warn_ex("%s\n", str)
#define _debug_notice(str) _debug_notice_ex("%s\n", str)
#define _debug_info(str) _debug_info_ex("%s\n", str)
#define _debug_msg(str) _debug_msg_ex("%s\n", str)

#endif  /* MSGPACKI_DEBUG_H */
