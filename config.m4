dnl config.m4 for extension msgpacki

dnl Check PHP version:
AC_MSG_CHECKING(PHP version)
if test ! -z "$phpincludedir"; then
    PHP_VERSION=`grep 'PHP_VERSION ' $phpincludedir/main/php_version.h | sed -e 's/.*"\([[0-9\.]]*\)".*/\1/g' 2>/dev/null`
elif test ! -z "$PHP_CONFIG"; then
    PHP_VERSION=`$PHP_CONFIG --version 2>/dev/null`
fi

if test x"$PHP_VERSION" = "x"; then
    AC_MSG_WARN([none])
else
    PHP_MAJOR_VERSION=`echo $PHP_VERSION | sed -e 's/\([[0-9]]*\)\.\([[0-9]]*\)\.\([[0-9]]*\).*/\1/g' 2>/dev/null`
    PHP_MINOR_VERSION=`echo $PHP_VERSION | sed -e 's/\([[0-9]]*\)\.\([[0-9]]*\)\.\([[0-9]]*\).*/\2/g' 2>/dev/null`
    PHP_RELEASE_VERSION=`echo $PHP_VERSION | sed -e 's/\([[0-9]]*\)\.\([[0-9]]*\)\.\([[0-9]]*\).*/\3/g' 2>/dev/null`
    AC_MSG_RESULT([$PHP_VERSION])
fi

if test $PHP_MAJOR_VERSION -lt 5; then
    AC_MSG_ERROR([need at least PHP 5 or newer])
fi

PHP_ARG_ENABLE(msgpacki, whether to enable msgpacki support,
[  --enable-msgpacki           Enable msgpacki support])

if test "$PHP_MSGPACKI" != "no"; then
  PHP_NEW_EXTENSION(msgpacki, msgpacki.c msgpacki_function.c msgpacki_class.c msgpacki_filter.c msgpacki_session.c, $ext_shared)

  ifdef([PHP_INSTALL_HEADERS],
  [
    PHP_INSTALL_HEADERS([ext/msgpacki/], [php_msgpacki.h msgpacki_function.h msgpacki_class.h msgpacki_filter.h])
  ], [
    PHP_ADD_MAKEFILE_FRAGMENT
  ])

  PHP_C_BIGENDIAN

dnl dnl This creates a file so it has to be after above macros
dnl   PHP_CHECK_TYPES([int8 uint8 int16 uint16 int32 uint32 uchar ulong int8_t uint8_t int16_t uint16_t int32_t uint32_t int64_t uint64_t], [
dnl     ext/msgpacki/php_msgpacki_config.h
dnl   ],[
dnl #ifdef HAVE_SYS_TYPES_H
dnl #include <sys/types.h>
dnl #endif
dnl #ifdef HAVE_STDINT_H
dnl #include <stdint.h>
dnl #endif
dnl   ])
fi

dnl NameSpace
AC_ARG_ENABLE(msgpacki-namespace,
  AC_HELP_STRING([--enable-msgpacki-namespace],
    [enable msgpacki namespace [default=yes]]),
  [ENABLE_MSGPACKI_NAMESPACE="${enableval:-no}"],
  [ENABLE_MSGPACKI_NAMESPACE=yes]
)
AS_IF([test "x${ENABLE_MSGPACKI_NAMESPACE}" = xyes -a $PHP_MINOR_VERSION -ge 3],
    PHP_DEF_HAVE(MSGPACKI_NAMESPACE)
)

dnl Tests
TESTS="tests/common"
if test "x${ENABLE_MSGPACKI_NAMESPACE}" = "xyes" -a $PHP_MINOR_VERSION -ge 3; then
   TESTS="${TESTS} tests/namespace"
fi

PHP_SUBST([TESTS])
