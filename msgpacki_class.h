#ifndef MSGPACKI_CLASS_H
#define MSGPACKI_CLASS_H

#define MSGPACKI_CLASS_NAME "MessagePacki"

PHP_MSGPACKI_API int msgpacki_register_classes(TSRMLS_D);
PHP_MSGPACKI_API zend_class_entry *msgpacki_get_ce(void);

#endif  /* MSGPACKI_CLASS_H */
