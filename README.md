# MessagePack 改良版拡張モジュール #

[![Build Status](https://secure.travis-ci.org/kjdev/php-ext-msgpacki.png?branch=master)](http://travis-ci.org/kjdev/php-ext-msgpacki)

msgpacki 拡張サポートによって MessagePack による変数のシリアライズ処理を行うこと
ができるようになります。

MessagePack に関する詳細は [» http://msgpack.org/](http://msgpack.org/) をご覧く
ださい。

## 開発環境 ##

* PHP 5.4.4 / 5.4.7
* Fedora 16 / 17 (x86_64)

### 検証 ##

検証した環境およびバージョンは次の通りです。

#### バージョン: 1.0.0 ####

* PHP 5.2.17 on Fedora 16 (x86_64)
* PHP 5.2.17 [ZTS] on Fedora 16 (x86_64)
* PHP 5.3.14 on Fedora 16 (x86_64)
* PHP 5.3.14 [ZTS] on Fedora 16 (x86_64)
* PHP 5.4.4 on Fedora 16 (x86_64)
* PHP 5.4.4 [ZTS] on Fedora 16 (x86_64)
* PHP 5.4.4 on Fedora 17 (i686) [KVM]
* PHP 5.4.4 on Windows Vista Home Premium SP2 (32 bit)
* PHP 5.4.4 [nts] on Windows Vista Home Premium SP2 (32 bit)

#### バージョン: 1.0.1 ####

* PHP 5.4.7 on Fedora 17 (x86_64)
* PHP 5.4.7 [ZTS] on Fedora 16 (x86_64)
* PHP 5.4.7 on Windows Vista Home Premium SP2 (32 bit)
* PHP 5.4.7 [nts] on Windows Vista Home Premium SP2 (32 bit)

## 本家との主な差異 ##

* 本家とは [» Github](https://github.com/msgpack/msgpack/tree/master/php) にあるものを示します。
* 配列は Map として処理します。(動作モードにより異なります)
* convert に関しては未サポートです。
* シリアライズ/アンシリアライズ処理の前後にフィルタ処理を付与できます。
* 名前空間によるエイリアス関数を定義しています。(使用しないことも可能)

## インストール ##

### コンパイルオプション ###

通常は MessagePacki という名前空間でエイリアス関数が定義されますが、
「--disable-msgpacki-namespace」をビルドオプションに追加することで
名前空間のエイリアス関数を定義しないようにすることができます。

### インストール ###

[» Github](https://github.com/kjdev/php-ext-msgpacki) にある最新のソースコー
ドからコンパイルします。
Github に行き、"download" ボタンをクリックしましょう。
そして以下のようにします。

````
$ tar zxvf msgpacki-<commit_id>.tar.gz
$ cd msgpacki-<commit_id>
$ phpize
$ ./configure
$ sudo make install
````

コンパイルオプションを指定する場合は次のようにします。

```
./configure --disable-msgpacki-namespace
```

php.ini を次のように変更します。

* extension_dir 変数が msgpacki.so の場所を指すようにします。
  ビルド中に、PHP ドライバをどこにインストールするのかがこのように表示されます。

  ````
  Installing '/usr/lib/php/extensions/no-debug-zts-20060613/msgpacki.so'
  ````

  この場所が PHP の拡張モジュール用ディレクトリと一致するかどうかは、
  次のようにして確認します。

  ````
  $ php -i | grep extension_dir
    extension_dir => /usr/lib/php/extensions/no-debug-zts-20060613 =>
  /usr/lib/php/extensions/no-debug-zts-20060613
  ````

  もし一致しない場合は、php.ini の extension_dir を変更するか、
  あるいは msgpacki.so を移動させます。

* PHP の起動時に拡張モジュールを読み込むために、次の行を追加します。

  ````
  extension=msgpacki.so
  ````

Fedora 17 および CentOS 6.2 のパッケージがあります。

* [» Fedora 17](https://github.com/downloads/kjdev/php-ext-msgpacki/php-pecl-msgpacki-1.0.0-1.fc17.kjdev.x86_64.rpm)
* [» CentOS 6.2](https://github.com/downloads/kjdev/php-ext-msgpacki/php-pecl-msgpacki-1.0.0-1.el6.x86_64.rpm)

### Windows へのインストール ###

リリースごとのコンパイル済みバイナリがにあります。
アーカイブを Unzip して、php_msgpacki.dll を PHP 拡張モジュールのディレクトリ
(デフォルトは "ext") に置きます。

* [» PHP 5.4.4 VC9 Thread-Safe extension](https://github.com/downloads/kjdev/php-ext-msgpacki/php-5.4.4-Win32-VC9-x86-msgpacki-1.0.0.zip)
* [» PHP 5.4.4 VC9 Non-Thread-Safe extension](https://github.com/downloads/kjdev/php-ext-msgpacki/php-5.4.4-nts-Win32-VC9-x86-msgpacki-1.0.0.zip)

そして、php.ini に次の行を追加します。

````
extension=php_msgpacki.dll
````

## php.ini オプション ##

php.ini の設定により動作が変化します。

### msgpacki.mode _int_ ###

* デフォルト: 2 (MSGPACKI\_MODE\_PHP)
* 変更可能: PHP\_INI\_ALL

シリアライズ処理の動作モードを設定します。

## 定義済み定数 ##

以下の定数が定義されています。

* MSGPACKI\_MODE\_PHP

  標準の動作モードに指定します。
  msgpacki\_serialize()、msgpacki\_unserialize() の動作は
  serialize()/unserialize() とほぼ同じように動作します。

  クラスや参照値のシリアル化は Map で処理して、1 つ目のキーに NULL を指定して
  バリュー値にクラス名や参照値を示すフラグを挿入しています。

* MSGPACKI\_MODE\_ORIGIN

  MSGPACKI\_MODE\_PHP と違いは次のものになります。

  * 参照値やシリアライズクラスの処理はサポートしません。
  * クラスオブジェクトはメンバ値を Map で処理します。
    * 内部処理的にオブジェクトの private および protected メンバのに付与されてい
      るクラス名や '*' は削除されます。
  * 配列は 0 からはじまる連続した数値でなければ Map で処理します。

  PHP 以外との言語でデータの受け渡しを行う時に使用してください。

* MSGPACKI\_FILTER\_REGISTER

  登録されているフィルタを指定します。

* MSGPACKI\_FILTER\_PRE\_SERIALIZE

  msgpacki\_serialize() の前に処理するフィルタを指定します。

* MSGPACKI\_FILTER\_POST\_SERIALIZE

  msgpacki\_serialize() の後に処理するフィルタを指定します。

* MSGPACKI\_FILTER\_PRE\_UNSERIALIZE

  msgpacki\_unserialize() の前に処理するフィルタを指定します。

* MSGPACKI\_FILTER\_POST\_UNSERIALIZE

  msgpacki\_unserialize() の後に処理するフィルタを指定します。

## 関数 ##

### 一覧 ###

* msgpacki\_serialize — 値の保存可能な表現を生成する
* msgpacki\_unserialize — 保存用表現から PHP の値を生成する
* msgpacki\_encode — 値の保存可能な表現を生成する
* msgpacki\_decode — 保存用表現から PHP の値を生成する

* msgpacki\_filter\_register — フィルタを登録する
* msgpacki\_filter\_append — フィルタをリストの末尾に付与する
* msgpacki\_filter\_prepend — フィルタをリストの先頭に付与する
* msgpacki\_filter\_remove — フィルタを取り除く
* msgpacki\_get\_filters — フィルタのリストを取得する

### msgpacki\_serialize — 値の保存可能な表現を生成する ###

#### 説明 ####

string **msgpacki\_serialize** ( mixed _$value_ )

値の保存可能な表現を生成します。

シリアル化された文字列を PHP の値に戻すには、msgpacki\_unserialize() を使用し
てください。

#### パラメータ ####

* _value_

  シリアル化する値。
  msgpacki\_serialize() は、resource 以外の全ての型を処理します。

  PHP は、シリアル化の前にまずメンバ関数 __sleep() のコールをします。ここで、シ
  リアル化の前のオブジェクトの後始末処理などを行います。

#### 返り値 ####

value の保存可能なバイトストリーム表現を含む文字列を返します。

### msgpacki\_unserialize — 保存用表現から PHP の値を生成する ###

#### 説明 ####

mixed **msgpacki\_unserialize** ( string _$str_ )

シリアル化された変数を PHP 変数値に戻す変換を行います。

#### パラメータ ####

* _str_

  シリアル化された文字列。

  もしアンシリアライズする変数がオブジェクトの場合、オブジェクトが無事再作成され
  た後、PHP は自動的にメンバ関数 __wakeup() (存在していれば) をコールしようとし
  ます。

#### 返り値 ####

変換された値が返されます。

渡された文字列が復元できなかった場合、FALSE を返して E_NOTICE を発生します。

### msgpacki\_encode — 値の保存可能な表現を生成する ###

基本的に msgpacki\_serialize() と同じですが、
この関数は動作モードが MSGPACKI\_MODE\_ORIGIN 固定となっています。

#### 説明 ####

string **msgpacki\_encode** ( mixed _$value_ [, int options = MSGPACKI\_MODE\_ORIGIN ] )

値の保存可能な表現を生成します。

#### パラメータ ####

* _value_

  シリアル化する値。

* _options_

  動作モード。

#### 返り値 ####

value の保存可能なバイトストリーム表現を含む文字列を返します。

### msgpacki\_decode — 保存用表現から PHP の値を生成する ###

基本的に msgpacki\_unserialize() と同じですが、
この関数は動作モードが MSGPACKI\_MODE\_ORIGIN 固定となっています。

#### 説明 ####

mixed **msgpacki\_decode** ( string _$str_ [, int options = MSGPACKI\_MODE\_ORIGIN ] )

シリアル化された変数を PHP 変数値に戻す変換を行います。

#### パラメータ ####

* _str_

  シリアル化された文字列。

* _options_

  動作モード。

#### 返り値 ####

変換された値が返されます。

渡された文字列が復元できなかった場合、FALSE を返して E_NOTICE を発生します。

### msgpacki\_filter\_register — フィルタを登録する ###

#### 説明 ####

bool **msgpacki\_filter\_register** ( string _$filtername_ , string _$classname_ )

シリアライズ/アンシリアライズの関数で使うことのできるカスタムフィルタを登録します。

#### パラメータ ####

* _filtername_

  登録するフィルタ名。

* _classname_

  カスタムフィルタのクラス名。

  フィルタを実装するには、MessagePacki\_Filter を継承したクラスのメンバ関数を実
  装しなくてはなりません。

  メソッドの実装は、MessagePacki\_Filter の説明の通りにしなければなりません。
  さもないと、定義されていない動作をします。

#### 返り値 ####

成功した場合に *TRUE* を、失敗した場合に *FALSE* を返します。

### msgpacki\_filter\_append — フィルタをリストの末尾に付与する ###

#### 説明 ####

bool **msgpacki\_filter\_append** ( string _$filtername_ )

filtername で指定されたフィルタをフィルタのリストの末尾に加えます。

#### パラメータ ####

* _filtername_

  フィルタ名。

#### 返り値 ####

成功した場合に *TRUE* を、失敗した場合に *FALSE* を返します。

### msgpacki\_filter\_prepend — フィルタをリストの先頭に付与する ###

#### 説明 ####

bool **msgpacki\_filter\_prepend** ( string _$filtername_ )

filtername で指定されたフィルタをフィルタのリストの先頭に加えます。

#### パラメータ ####

* _filtername_

  フィルタ名。

#### 返り値 ####

成功した場合に *TRUE* を、失敗した場合に *FALSE* を返します。

### msgpacki\_filter\_remove — フィルタを取り除く ###

#### 説明 ####

bool **msgpacki\_filter\_remove** ( string _$filtername_ )

事前に msgpacki\_filter\_append() あるいは msgpacki\_filter_prepend() で追
加したフィルタを削除します。

#### パラメータ ####

* _filtername_

  フィルタ名。

#### 返り値 ####

成功した場合に *TRUE* を、失敗した場合に *FALSE* を返します。

### msgpacki\_get\_filters — フィルタのリストを取得する ###

#### 説明 ####

array msgpacki\_get\_filters ( [ string _$type_ ] )

登録されているフィルタの一覧を取得します。

#### パラメータ ####

* _type_

  フィルタタイプ。

  MSGPACKI\_FILTER\_REGISTER, MSGPACKI\_FILTER\_PRE\_SERIALIZE,
  MSGPACKI\_FILTER\_POST\_SERIALIZE, MSGPACKI\_FILTER\_PRE\_UNSERIALIZE,
  MSGPACKI\_FILTER\_POST\_UNSERIALIZE。

#### 返り値 ####

使用可能フィルタの名前を含む配列を返します。

type を指定しない場合は type を配列のキーにしたものを返します。

## クラス ##

### 一覧 ###

* MessagePacki — シリアライズ/アンシリアライズクラス
* MessagePacki\_Filter — フィルタクラス

### MessagePacki — シリアライズ/アンシリアライズクラス ###

#### クラス概要 ####

```
MessagePacki {
    /* メソッド */
    public __construct( [ int $mode = MSGPACKI_MODE_PHP ] )
    public string pack( mixed $value )
    public mixed unpack( string $str )
    public int get_mode()
    public bool set_mode( int $mode )
    public bool append_filter( string $name )
    public bool prepend_filter( string $name )
    public bool remove_filter( string $name )
    public array get_filters( string $type )

    /* エイリアス */
    alias int getMode()
    alias bool setMode( int $mode )
    alias appendFilter( string $name )
    alias prependFilter( string $name )
    alias removeFilter( string $name )
    alias getFilters( string $type )
}
```

#### メソッド一覧 ####

* MessagePacki::__construct — 新しいシリアライズ/アンシリアライズオブジェクトを作成する
* MessagePacki::pack — 値の保存可能な表現を生成する
* MessagePacki::unpack — 保存用表現から PHP の値を生成する
* MessagePacki::get\_mode — 動作モードを取得する
* MessagePacki::set\_mode — 動作モードを設定する
* MessagePacki::append\_filter — フィルタをリストの末尾に付与する
* MessagePacki::prepend\_filter — フィルタをリストの先頭に付与する
* MessagePacki::remove\_filter — フィルタを取り除く
* MessagePacki::get\_filters — フィルタのリストを取得する

### MessagePacki::__construct — 新しいシリアライズ/アンシリアライズオブジェクトを作成する ###

#### 説明 ####

public **MessagePacki::__construct** ( [ int _$mode_ = MSGPACKI\_MODE\_PHP ] )

#### パラメータ ####

* _mode_

  MessagePack シリアライズ処理の動作モード。

  既定値は MSGPACKI\_MODE\_PHP。

#### 返り値 ####

新しいシリアライズ/アンシリアライズオブジェクトを返します。

### MessagePacki::pack — 値の保存可能な表現を生成する ###

#### 説明 ####

public string **MessagePacki::pack** ( mixed _$value_ )

値の保存可能な表現を生成します。

msgpacki\_serialize() と同様の処理を行います。

#### パラメータ ####

* _value_

  シリアル化する値。

#### 返り値 ####

value の保存可能な文字列を返します。

### MessagePacki::unpack — 保存用表現から PHP の値を生成する ###

#### 説明 ####

public mixed **MessagePacki::unpack** ( string _$str_ )

シリアル化された変数を PHP 変数値に戻す変換を行います。

msgpacki\_unserialize() と同様の処理を行います。

#### パラメータ ####

* _str_

  シリアル化された文字列。

#### 返り値 ####

変換された値が返されます。

### MessagePacki::get\_mode — 動作モードを取得する ###

#### 説明 ####

public int **MessagePacki::get\_mode** ()

動作モードを取得します。

#### 返り値 ####

動作モード値を返します。

### MessagePacki::set\_mode — 動作モードを設定する ###

#### 説明 ####

public bool **MessagePacki::set\_mode** ( int _$mode_ )

動作モードを設定します。

ここで設定した動作モードに処理 MessagePacki::pack() 、 MessagePacki::unpack() の
処理が変わります。

#### パラメータ ####

* _mode_

  動作モード値。

#### 返り値 ####

成功した場合に *TRUE* を、失敗した場合に *FALSE* を返します。

### MessagePacki::append\_filter — フィルタをリストの末尾に付与する ###

#### 説明 ####

public bool **MessagePacki::append\_filter** ( string _$name_ )

name で指定されたフィルタをフィルタリストの末尾に加えます。

#### パラメータ ####

* _name_

  フィルタ名またはクラス名。

  msgpacki\_filter\_register() により登録されているフィルタと同名のものがある場合
  はそのフィルタをフィルタリストに加えます。
  同名のフィルタがない場合はその名前のクラスを登録します。

  フィルタクラスは MessagePacki\_Filter を継承する必要があります。

#### 返り値 ####

成功した場合に *TRUE* を、失敗した場合に *FALSE* を返します。

### MessagePacki::prepend\_filter — フィルタをリストの先頭に付与する ###

#### 説明 ####

public bool **MessagePacki::prepend\_filter** ( string _$name_ )

name で指定されたフィルタをフィルタリストの先頭に加えます。

#### パラメータ ####

* _name_

  フィルタ名またはクラス名。

  msgpacki\_filter\_register() により登録されているフィルタと同名のものがある場合
  はそのフィルタをフィルタリストに加えます。
  同名のフィルタがない場合はその名前のクラスを登録します。

  フィルタクラスは MessagePacki\_Filter を継承する必要があります。

#### 返り値 ####

成功した場合に *TRUE* を、失敗した場合に *FALSE* を返します。

### MessagePacki::remove\_filter — フィルタを取り除く ###

#### 説明 ####

public bool **MessagePacki::remove\_filter** ( string _$name_ )

MessagePacki::append\_filter() あるいは MessagePacki::prepend\_filter() で追加し
たフィルタを削除します。

#### パラメータ ####

* _name_

  フィルタ名またはクラス名。

#### 返り値 ####

成功した場合に *TRUE* を、失敗した場合に *FALSE* を返します。

### MessagePacki::get\_filters — フィルタのリストを取得する ###

#### 説明 ####

public array **MessagePacki::get\_filters** ( [ string _$type_ ] )

登録されているフィルタの一覧を取得します。

#### パラメータ ####

* _type_

  フィルタタイプ。

#### 返り値 ####

使用可能フィルタの名前を含む配列を返します。

type を指定しない場合は type を配列のキーにしたものを返します。

### MessagePacki_Filter — フィルタクラス ###

このクラスの子クラスを msgpacki\_filter\_register() に渡します。

#### クラス概要 ####

```
MessagePacki\_Filter {
    /* プロパティ */
    public $filterename;

    /* メソッド */
    public mixed pre_serialize ( mixed $in )
    public string post_serialize ( string $in )
    public string pre_unserialize ( string $in )
    public mixed post_unserialize ( mixed $in )
}
```

#### プロパティ ####

* filtername

  msgpacki\_filter\_register() で登録するフィルタの名前。

#### メソッド一覧 ####

* MessagePacki\_Filter::pre\_serialize — シリアライズ処理前に実行される
* MessagePacki\_Filter::post\_serialize — シリアライズ処理後に実行される
* MessagePacki\_Filter::pre\_unserialize — アンシリアライズ処理前に実行される
* MessagePacki\_Filter::post\_unserialize — アンシリアライズ処理後に実行される

### MessagePacki\_Filter::pre\_serialize — シリアライズ処理前にコールされる ###

#### 説明 ####

public mixed **MessagePacki\_Filter::pre\_serialize** ( mixed _$in_ )

このメソッドがコールされるのは、シリアライズ処理が実行される前です。

#### パラメータ ####

* _in_

  シリアライズ化する値。

#### 返り値 ####

フィルタ処理したシリアライズ処理に渡す値。

### MessagePacki\_Filter::post\_serialize — シリアライズ処理後にコールされる ###

#### 説明 ####

public string **MessagePacki\_Filter::pre\_serialize** ( string _$in_ )

このメソッドがコールされるのは、シリアライズ処理が実行された後です。

#### パラメータ ####

* _in_

  シリアル化された文字列。

#### 返り値 ####

フィルタ処理したシリアル化された文字列。

### MessagePacki\_Filter::pre\_unserialize — アンシリアライズ処理前にコールされる ###

#### 説明 ####

public string **MessagePacki\_Filter::pre\_unserialize** ( string _$in_ )

このメソッドがコールされるのは、アンシリアライズ処理が実行される前です。

#### パラメータ ####

* _in_

  シリアル化された文字列。

#### 返り値 ####

フィルタ処理したシリアル化された文字列。

### MessagePacki\_Filter::post\_unserialize — アンシリアライズ処理後にコールされる ###

#### 説明 ####

public mixed **MessagePacki\_Filter::post\_unserialize** ( mixed _$in_ )

このメソッドがコールされるのは、アンシリアライズ処理が実行た後です。

#### パラメータ ####

* _in_

  アンシリアライズ処理された値。

#### 返り値 ####

フィルタ処理したアンシリアライズ処理された値。

## 名前空間関数 ##

名前空間は「**MessagePacki**」になります。

### 一覧 ###

* MessagePacki\\serialize

  msgpacki\_serialize() 関数のエイリアス

* MessagePacki\\unserialize

  msgpacki\_unserialize() 関数のエイリアス

* MessagePacki\\encode

  msgpacki\_encode() 関数のエイリアス

* MessagePacki\\decode

  msgpacki\_decode() 関数のエイリアス

* MessagePacki\\filter\_register

  msgpacki\_filter\_register() 関数のエイリアス

* MessagePacki\\filter\_append

  msgpacki\_filter\_append() 関数のエイリアス

* MessagePacki\\filter\_prepend

  msgpacki\_filter\_prepend() 関数のエイリアス

* MessagePacki\\filter\_remove

  msgpacki\_filter\_remove() 関数のエイリアス

* MessagePacki\\msgpacki\_filter\_get\_filters

  msgpacki\_filter\_get\_filters() 関数のエイリアス

## 名前空間クラス関数 ##

名前空間は「**MessagePacki**」になります。

### 一覧 ###

* MessagePacki\\Filter

  MessagePacki\_Filter クラスのエイリアス

## セッションシリアライズ ##

session.serialize\_handler として MessagePack フォーマットをサポートします。

### session.serialize\_handler _string_ ###

msgpacki を指定することで MessagePack フォーマットでセッション値のシリアライズ処
理します。

この時の動作モード MSGPACKI\_MODE\_PHP になります。

フィルターはサポートしておりません。


## 関連ページ ##

* [code coverage report](http://gcov.at-ninja.jp/php-ext-msgpacki/)
* [api document](http://api.at-ninja.jp/php-ext-msgpacki/)
