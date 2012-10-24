
#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"

#include "php_verdep.h"
#include "php_msgpacki.h"
#include "msgpacki_function.h"
#include "msgpacki_class.h"
#include "msgpacki_filter.h"
#include "msgpacki_session.h"

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_serialize, 0, 0, 1)
    ZEND_ARG_INFO(0, value)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_unserialize, 0, 0, 1)
    ZEND_ARG_INFO(0, str)
    ZEND_ARG_INFO(1, status)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_encode, 0, 0, 1)
    ZEND_ARG_INFO(0, value)
    ZEND_ARG_INFO(0, options)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_decode, 0, 0, 1)
    ZEND_ARG_INFO(0, str)
    ZEND_ARG_INFO(0, options)
    ZEND_ARG_INFO(1, status)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_filter_register, 0, 0, 2)
    ZEND_ARG_INFO(0, filtername)
    ZEND_ARG_INFO(0, classname)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_filter_append, 0, 0, 1)
    ZEND_ARG_INFO(0, filtername)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_filter_prepend, 0, 0, 1)
    ZEND_ARG_INFO(0, filtername)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_filter_remove, 0, 0, 1)
    ZEND_ARG_INFO(0, filtername)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_msgpacki_get_filters, 0, 0, 0)
    ZEND_ARG_INFO(0, filter)
ZEND_END_ARG_INFO()

MSGPACKI_ZEND_FUNCTION_ENTRY msgpacki_functions[] = {
    ZEND_FE(msgpacki_serialize, arginfo_msgpacki_serialize)
    ZEND_FE(msgpacki_unserialize, arginfo_msgpacki_unserialize)
    ZEND_FE(msgpacki_encode, arginfo_msgpacki_encode)
    ZEND_FE(msgpacki_decode, arginfo_msgpacki_decode)
    ZEND_FE(msgpacki_filter_register, arginfo_msgpacki_filter_register)
    ZEND_FE(msgpacki_filter_append, arginfo_msgpacki_filter_append)
    ZEND_FE(msgpacki_filter_prepend, arginfo_msgpacki_filter_prepend)
    ZEND_FE(msgpacki_filter_remove, arginfo_msgpacki_filter_remove)
    ZEND_FE(msgpacki_get_filters, arginfo_msgpacki_get_filters)
#ifdef HAVE_MSGPACKI_NAMESPACE
    ZEND_NS_FALIAS(PHP_MSGPACKI_NS, serialize,
                   msgpacki_serialize, arginfo_msgpacki_serialize)
    ZEND_NS_FALIAS(PHP_MSGPACKI_NS, unserialize,
                   msgpacki_unserialize, arginfo_msgpacki_unserialize)
    ZEND_NS_FALIAS(PHP_MSGPACKI_NS, encode,
                   msgpacki_encode, arginfo_msgpacki_encode)
    ZEND_NS_FALIAS(PHP_MSGPACKI_NS, decode,
                   msgpacki_decode, arginfo_msgpacki_decode)
    ZEND_NS_FALIAS(PHP_MSGPACKI_NS, filter_register,
                   msgpacki_filter_register, arginfo_msgpacki_filter_register)
    ZEND_NS_FALIAS(PHP_MSGPACKI_NS, filter_append,
                   msgpacki_filter_append, arginfo_msgpacki_filter_append)
    ZEND_NS_FALIAS(PHP_MSGPACKI_NS, filter_prepend,
                   msgpacki_filter_prepend, arginfo_msgpacki_filter_prepend)
    ZEND_NS_FALIAS(PHP_MSGPACKI_NS, filter_remove,
                   msgpacki_filter_remove, arginfo_msgpacki_filter_remove)
    ZEND_NS_FALIAS(PHP_MSGPACKI_NS, get_filters,
                   msgpacki_get_filters, arginfo_msgpacki_get_filters)
#endif
    ZEND_FE_END
};

ZEND_DECLARE_MODULE_GLOBALS(msgpacki)

PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("msgpacki.mode", "2", PHP_INI_ALL,
                      OnUpdateLong, mode, zend_msgpacki_globals,
                      msgpacki_globals)
    /* default: PHP_MSGPACKI_MODE_PHP */
PHP_INI_END()

static void php_msgpacki_init_globals(zend_msgpacki_globals *mpi_globals)
{
    mpi_globals->serialize_lock = 0;
    memset(&mpi_globals->serialize, 0, sizeof(mpi_globals->serialize));
    memset(&mpi_globals->unserialize, 0, sizeof(mpi_globals->unserialize));

    memset(&mpi_globals->filter, 0, sizeof(mpi_globals->filter));

    mpi_globals->mode = PHP_MSGPACKI_MODE_PHP;
}

ZEND_MINIT_FUNCTION(msgpacki)
{
    ZEND_INIT_MODULE_GLOBALS(msgpacki, php_msgpacki_init_globals, NULL);

    REGISTER_INI_ENTRIES();

    msgpacki_register_filters(TSRMLS_C);
    msgpacki_register_classes(TSRMLS_C);
#if HAVE_PHP_SESSION
    msgpacki_register_session(TSRMLS_C);
#endif

    REGISTER_LONG_CONSTANT("MSGPACKI_MODE_ORIGIN", PHP_MSGPACKI_MODE_ORIGIN,
                           CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("MSGPACKI_MODE_PHP", PHP_MSGPACKI_MODE_PHP,
                           CONST_CS | CONST_PERSISTENT);

    REGISTER_STRING_CONSTANT("MSGPACKI_FILTER_REGISTER",
                             PHP_MSGPACKI_FILTER_REGISTER,
                             CONST_CS | CONST_PERSISTENT);
    REGISTER_STRING_CONSTANT("MSGPACKI_FILTER_PRE_SERIALIZE",
                             PHP_MSGPACKI_FILTER_PRE_SERIALIZE,
                             CONST_CS | CONST_PERSISTENT);
    REGISTER_STRING_CONSTANT("MSGPACKI_FILTER_POST_SERIALIZE",
                             PHP_MSGPACKI_FILTER_POST_SERIALIZE,
                             CONST_CS | CONST_PERSISTENT);
    REGISTER_STRING_CONSTANT("MSGPACKI_FILTER_PRE_UNSERIALIZE",
                             PHP_MSGPACKI_FILTER_PRE_UNSERIALIZE,
                             CONST_CS | CONST_PERSISTENT);
    REGISTER_STRING_CONSTANT("MSGPACKI_FILTER_POST_UNSERIALIZE",
                             PHP_MSGPACKI_FILTER_POST_UNSERIALIZE,
                             CONST_CS | CONST_PERSISTENT);

    return SUCCESS;
}

ZEND_MSHUTDOWN_FUNCTION(msgpacki)
{
    UNREGISTER_INI_ENTRIES();

    return SUCCESS;
}

ZEND_RINIT_FUNCTION(msgpacki)
{
    return SUCCESS;
}

#define MPI_HASH_FREE(ht)      \
    if (ht) {                  \
        zend_hash_destroy(ht); \
        efree(ht);             \
        ht = NULL;             \
    }

ZEND_RSHUTDOWN_FUNCTION(msgpacki)
{
    MPI_HASH_FREE(MSGPACKI_G(filter).registers);
    MPI_HASH_FREE(MSGPACKI_G(filter).pre_serialize);
    MPI_HASH_FREE(MSGPACKI_G(filter).post_serialize);
    MPI_HASH_FREE(MSGPACKI_G(filter).pre_unserialize);
    MPI_HASH_FREE(MSGPACKI_G(filter).post_unserialize);
    return SUCCESS;
}

ZEND_MINFO_FUNCTION(msgpacki)
{
    php_info_print_table_start();
    php_info_print_table_row(2, "msgpacki support", "enabled");
    php_info_print_table_row(2, "extension version", MSGPACKI_EXT_VERSION);
#if HAVE_PHP_SESSION
    php_info_print_table_row(2, "session support", "enabled" );
#endif
    php_info_print_table_end();

    DISPLAY_INI_ENTRIES();
}

zend_module_entry msgpacki_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
    STANDARD_MODULE_HEADER,
#endif
    "msgpacki",
    msgpacki_functions,
    ZEND_MINIT(msgpacki),
    ZEND_MSHUTDOWN(msgpacki),
    ZEND_RINIT(msgpacki),
    ZEND_RSHUTDOWN(msgpacki),
    ZEND_MINFO(msgpacki),
#if ZEND_MODULE_API_NO >= 20010901
    MSGPACKI_EXT_VERSION,
#endif
    STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_MSGPACKI
ZEND_GET_MODULE(msgpacki)
#endif
