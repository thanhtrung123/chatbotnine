## About ChatBot

 ChatBot Readme.md

## Install

DBを作成する。

```sql
CREATE DATABASE IF NOT EXISTS `qnamaker` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

- `/.env.example` を `/.env` にリネーム
- `/public/.htaccess.orig` を `/public/.htaccess` にリネーム

上記ファイルの内容を動かす環境用に変更。


前提条件: composer, npm(yarn)

プロジェクトのルートディレクトリに移動し、以下を実行。

### ・開発環境にインストールする場合

※windowsのファイルシステム上にプロジェクトがある場合は `yarn install` → `yarn install --no-bin-links`

```
$ composer install
$ yarn install
$ yarn run dev
$ php artisan migrate
$ php artisan db:seed
```

※composer intallで失敗する場合。

ext-zipが何とか → `$ yum install php-pecl-zip`（環境によって違うかも）

※yarn installで失敗する場合。

closs-envがない → `$ yarn global add closs-env`

pngquantが何とか → `$ yum install libpng-devel`（環境によって違うかも）

### ・本番環境にインストールする場合

TODO:本番環境用の.envを用意する

```
$ composer install --no-dev
$ yarn install --production
$ yarn run prod
$ php artisan migrate
$ php artisan db:seed
```

## MEMO

- 初期設置した場合に実行（APP_KEYの作成）
```
$ php artisan key:generate
```

- メンテナンスモード(downでメンテナンスモード有効、upで通常モード)
```
$ php artisan down
$ php artisan up
```

- ファサードとかをIDEで補完したい(NetBeansだとconfigとかbladeの補完は不可)
```
$ php artisan ide-helper:generate
```

- routesを変更したら
```
$ php artisan route:clear
```
- configを変更したら
```
$ php artisan config:clear
```
- laravelのキャッシュを消す
```
$ php artisan clear-compiled
$ php artisan cache:clear
```

## その他開発メモ

#### Laravel-Mix

- laravel-mix ポーリング（JavaScript等のリアルタイム変換）
```
$ yarn run wp
```

※注 `resources/assets/plain` にあるようなコンパイルしないでコピーしているファイルはリアルタイムでコピーされない  
もう一回ポーリングするか`$ yarn run dev` `$ yarn run prod` するとコピーされる

- laravel-mixしたファイルパスの取り方
```
<!-- バージョニングしてるJSやCSSは↓ -->
<script src="{{ asset(mix('js/admin.js')) }}"></script>
<!-- コピーした画像とかは↓ -->
<img src="{{asset('img/images/avatar.png')}}">
```

- laravel-mixの設定(assetsファイルを新たに追加する場合等)  
 プロジェクトフォルダ直下の `/webpack.mix.js` で設定。
 
#### JavaScriptについて
JavaScriptは `resources/assets/js` 内で管理する  
管理画面のレイアウトでは `resources/assets/js/admin.js`  
利用者側のレイアウトでは `resources/assets/js/user.js`  
を基本的に読み込む想定  
チャットボットレイアウトのみ `resources/assets/js/user.js` + `resources/assets/js/user_bot.js`  

基本的に上記JavaScriptはサブディレクトリ中のJavaScriptを呼ぶだけにする

上記JavaScriptはES6で記述可能（コンパイル時にBabelで変換されるのでIEでも動く）  
ただし、viewに直接書く場合はES6で記述するとIEで動かないので注意

#### ルーティングについて
Webページは`routes/web.php` WebAPIは`routes/api.php` に定義する  
最低限のRESTfulを意識する(?) ※getメソッドで登録とかしない


## phpDocumentor
phpDocumentorをインストールしてドキュメントを書き出す方法  
- phpDocumentorをインストールする（composerはうまくいかないので直接の方法）
  - http://phpdoc.org/phpDocumentor.phar 直接DLする
  - プロジェクトフォルダの vendor/bin 等に配置する
- プロジェクトフォルダで以下のコマンドを実行  
`$ php vendor/bin/phpDocumentor.phar -d ./app -t ./public/documents`  
オプション -t の出力先ディレクトリは任意
 

