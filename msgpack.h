#ifndef MSGPACK_H
#define MSGPACK_H

#ifdef HAVE_SYS_TYPES_H
#include <sys/types.h>
#endif

#ifdef HAVE_STDINT_H
#include <stdint.h>
#endif

#if HAVE_ARPA_INET_H
#include <arpa/inet.h>
#endif

/* Typdefs */
#ifndef HAVE_INT8_T
#ifndef HAVE_INT8
typedef signed char int8_t; /* Signed integer >= 8    bits */
#else
typedef int8 int8_t;        /* Signed integer >= 8    bits */
#endif
#endif

#ifndef HAVE_UINT8_T
#ifndef HAVE_UINT8
typedef unsigned char uint8_t; /* Unsigned integer >= 8    bits */
#else
typedef uint8 uint8_t;         /* Signed integer >= 8    bits */
#endif
#endif

#ifndef HAVE_INT16_T
#ifndef HAVE_INT16
typedef signed short int16_t; /* Signed integer >= 16 bits */
#else
typedef int16 int16_t;        /* Signed integer >= 16 bits */
#endif
#endif

#ifndef HAVE_UINT16_T
#ifndef HAVE_UINT16
typedef unsigned short uint16_t; /* Signed integer >= 16 bits */
#else
typedef uint16 uint16_t;         /* Signed integer >= 16 bits */
#endif
#endif

#ifndef HAVE_INT32_T
#ifdef HAVE_INT32
typedef int32 int32_t;
#elif SIZEOF_INT == 4
typedef signed int int32_t;
#elif SIZEOF_LONG == 4
typedef signed long int32_t;
#else
error "Neither int nor long is of 4 bytes width"
#endif
#endif /* HAVE_INT32_T */

#ifndef HAVE_UINT32_T
#ifdef HAVE_UINT32
typedef uint32 uint32_t;
#elif SIZEOF_INT == 4
typedef unsigned int uint32_t;
#elif SIZEOF_LONG == 4
typedef unsigned long uint32_t;
#else
#error "Neither int nor long is of 4 bytes width"
#endif
#endif /* HAVE_UINT32_T */

#ifndef HAVE_INT64_T
#ifdef HAVE_INT64
typedef int64 int64_t;
#elif SIZEOF_INT == 8
typedef signed int int64_t;
#elif SIZEOF_LONG == 8
typedef signed long int64_t;
#elif SIZEOF_LONG_LONG == 8
#ifdef PHP_WIN32
typedef __int64 int64_t;
#else
typedef signed long long int64_t;
#endif
#else
#error "Neither int nor long nor long long is of 8 bytes width"
#endif
#endif /* HAVE_INT64_T */

#ifndef HAVE_UINT64_T
#ifdef HAVE_UINT64
typedef uint64 uint64_t;
#elif SIZEOF_INT == 8
typedef unsigned int uint64_t;
#elif SIZEOF_LONG == 8
typedef unsigned long uint64_t;
#elif SIZEOF_LONG_LONG == 8
#ifdef PHP_WIN32
typedef unsigned __int64 uint64_t;
#else
typedef unsigned long long uint64_t;
#endif
#else
#error "Neither int nor long nor long long is of 8 bytes width"
#endif
#endif /* HAVE_INT64_T */


#ifndef WORDS_BIGENDIAN
/* LETTLEENDIAN */

/* _msgpack_be16 */
#ifdef PHP_WIN32
#  if defined(ntohs)
#    define _msgpack_be16(x) ntohs(x)
#  elif defined(_byteswap_ushort) || (defined(_MSC_VER) && _MSC_VER >= 1400)
#    define _msgpack_be16(x) ((uint16_t)_byteswap_ushort((unsigned short)x))
#  else
#    define _msgpack_be16(x) ( \
        ((((uint16_t)x) <<  8) ) | \
        ((((uint16_t)x) >>  8) ) )
#  endif
#else
#  define _msgpack_be16(x) ntohs(x)
#endif

/* _msgpack_be32 */
#ifdef PHP_WIN32
#  if defined(ntohl)
#    define _msgpack_be32(x) ntohl(x)
#  elif defined(_byteswap_ulong) || (defined(_MSC_VER) && _MSC_VER >= 1400)
#    define _msgpack_be32(x) ((uint32_t)_byteswap_ulong((unsigned long)x))
#  else
#    define _msgpack_be32(x)                     \
        ( ((((uint32_t)x) << 24)               ) | \
          ((((uint32_t)x) <<  8) & 0x00ff0000U ) | \
          ((((uint32_t)x) >>  8) & 0x0000ff00U ) | \
          ((((uint32_t)x) >> 24)               ) )
#  endif
#else
#  define _msgpack_be32(x) ntohl(x)
#endif

/* _msgpack_be64 */
#if defined(_byteswap_uint64) || (defined(_MSC_VER) && _MSC_VER >= 1400)
#  define _msgpack_be64(x) (_byteswap_uint64(x))
#elif defined(bswap_64)
#  define _msgpack_be64(x) bswap_64(x)
#elif defined(__DARWIN_OSSwapInt64)
#  define _msgpack_be64(x) __DARWIN_OSSwapInt64(x)
#else
#define _msgpack_be64(x) \
    ( ((((uint64_t)x) << 56)                         ) | \
      ((((uint64_t)x) << 40) & 0x00ff000000000000ULL ) | \
      ((((uint64_t)x) << 24) & 0x0000ff0000000000ULL ) | \
      ((((uint64_t)x) <<  8) & 0x000000ff00000000ULL ) | \
      ((((uint64_t)x) >>  8) & 0x00000000ff000000ULL ) | \
      ((((uint64_t)x) >> 24) & 0x0000000000ff0000ULL ) | \
      ((((uint64_t)x) >> 40) & 0x000000000000ff00ULL ) | \
      ((((uint64_t)x) >> 56)                         ) )
#endif

#else
/* BIGENDIAN */
#define _msgpack_be16(x) (x)
#define _msgpack_be32(x) (x)
#define _msgpack_be64(x) (x)

#endif

#endif /* MSGPACK_H */
