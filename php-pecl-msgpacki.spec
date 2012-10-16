%define php_apiver %((echo 0; php -i 2>/dev/null | sed -n 's/^PHP API => //p') | tail -1)
%{!?php_extdir: %{expand: %%define php_extdir %(php-config --extension-dir)}}

%global pecl_name msgpacki
%global git_tag xxxxxxx

Summary: PHP MessagePack Improved Extension
Name: php-pecl-%{pecl_name}
Version: 1.0.2
Release: 1%{?dist}
Source: kjdev-php-ext-%{pecl_name}-v%{version}-0-g%{git_tag}.tar.gz
License: PHP License version 3.01
Group: Development/Libraries
BuildRoot: %{_tmppath}/%{name}-%{version}-root
BuildRequires: php-devel
%if 0%{?php_zend_api:1}
Requires: php(zend-abi) = %{php_zend_api}
Requires: php(api) = %{php_core_api}
%else
Requires: php-api = %{php_apiver}
%endif

Obsoletes: php-ninja_session
Provides: php-pecl(%{pecl_name}) = %{version}-%{release}

%description
PHP MessagePack Improved Extension

%prep
%setup -qn kjdev-php-ext-%{pecl_name}-%{git_tag}

%build
phpize
%configure
%{__make} %{?_smp_mflags}

%install
%makeinstall INSTALL_ROOT=%{buildroot}

%{__install} -d %{buildroot}%{_sysconfdir}/php.d
%{__cat} > %{buildroot}%{_sysconfdir}/php.d/msgpacki.ini <<EOF
; Enable msgpacki extension module
extension=msgpacki.so
EOF

# %{__install} -d %{buildroot}%{pecl_xmldir}
# %{__install} -pm 644 %{SOURCE1} %{buildroot}%{pecl_xmldir}/%{pecl_name}.xml

%check
export NO_INTERACTION=1 REPORT_EXIST_STATUS=1
%{__make} test
unset NO_INTERACTION REPORT_EXIST_STATUS
if [ -n "`find tests -name \*.diff -type f -print`" ]; then
   exit 1
fi

# %post
# %{pecl_install} %{pecl_xmldir}/%{pecl_name}.xml >/dev/null || :

# %postun
# if [ $1 -eq 0 ] ; then
#     %{pecl_uninstall} %{pecl_name} >/dev/null || :
# fi

%clean
%{__rm} -rf %{buildroot}

%files
%attr(-, root, root)
%{php_extdir}/msgpacki.so
%config(noreplace) %{_sysconfdir}/php.d/msgpacki.ini
%{_includedir}/php/ext/msgpacki/php_msgpacki.h
%{_includedir}/php/ext/msgpacki/msgpacki_class.h
%{_includedir}/php/ext/msgpacki/msgpacki_filter.h
%{_includedir}/php/ext/msgpacki/msgpacki_function.h
