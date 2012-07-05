#ifndef CONFIG_WIN_H
#define CONFIG_WIN_H

/* Use name space */
#define HAVE_MSGPACKI_NAMESPACE 1

#include <sys/locking.h>
#include <windows.h>
#include <math.h> /* Because of rint() */
#include <fcntl.h>
#include <io.h>
#include <malloc.h>

#include <win32/php_stdint.h>

#ifndef HAVE_INT8_T
#define HAVE_INT8_T
#endif
#ifndef HAVE_UINT8_T
#define HAVE_UINT8_T
#endif
#ifndef HAVE_INT16_T
#define HAVE_INT16_T
#endif
#ifndef HAVE_UINT16_T
#define HAVE_UINT16_T
#endif
#ifndef HAVE_INT32_T
#define HAVE_INT32_T
#endif
#ifndef HAVE_UINT32_T
#define HAVE_UINT32_T
#endif
#ifndef HAVE_INT64_T
#define HAVE_INT64_T
#endif
#ifndef HAVE_UINT64_T
#define HAVE_UINT64_T
#endif


#ifndef _WIN64
#ifndef _WIN32
#define _WIN32 /* Compatible with old source */
#endif
#ifndef __WIN32__
#define __WIN32__
#endif
#endif /* _WIN64 */
#ifndef __WIN__
#define __WIN__ /* To make it easier in VC++ */
#endif

/* Type information */

#define SIZEOF_CHAR      1
#define SIZEOF_LONG      4
#define SIZEOF_LONG_LONG 8

#endif /* CONFIG_WIN_H */
