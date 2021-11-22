<?php
/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

//ChatBot
Route::get('/', 'Bot\IndexController@index')->name('home');
//アンケート
Route::get('/enquete/entry/{form_hash}/{key?}', 'Bot\EnqueteController@entry')->name('enquete.entry');
Route::post('/enquete/store', 'Bot\EnqueteController@store')->name('enquete.store');

//管理者ページ
Route::get('/admin', 'Admin\IndexController@index')->name('admin')->middleware('auth');
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'auth', 'as' => 'admin.'], function () {
    //学習データ
    Util::addCsvRoute('learning');
    Route::get('/learning/edit/{api_id}', 'LearningController@editFromApiId')->name('learning.edit.api');
    Route::resource('/learning', 'LearningController');
    //類義語
    Util::addCsvRoute('synonym');
    Route::resource('/synonym', 'SynonymController');
    //異表記
    Util::addCsvRoute('variant');
    Route::resource('/variant', 'VariantController');
    //アカウント
    Route::resource('/user', 'UserController');
    Route::resource('/role', 'RoleController');
    //ログ
    Route::resource('/log', 'LogController')->only('index');
    //応答
    Util::addCsvRoute('response_info', null, ['export']);
    Route::resource('/response_info', 'ResponseInfoController')->only('index');
    //キーフレーズ
    Util::addCsvRoute('key_phrase');
    Route::get('/key_phrase/edit/{key_phrase_id}', 'KeyPhraseController@editFromKeyPhraseId')->name('key_phrase.edit.key_phrase_id');
    Route::resource('/key_phrase', 'KeyPhraseController');
    //カテゴリ
    Route::resource('/category', 'CategoryController');
    //シナリオ
    Route::get('/scenario/editor', 'ScenarioController@editor')->name('scenario.editor');
    Route::get('/scenario/qaData', 'ScenarioController@getDataQa')->name('scenario.getDataQa');
    Route::get('/scenario/editor/fillter', 'ScenarioController@scenarioListAjax')->name('scenario.fillter');
    Route::get('/scenario/editor/edit', 'ScenarioController@editScenario')->name('scenario.editor.edit');
    Route::get('/scenario/editor/detail', 'ScenarioController@detailScenario')->name('scenario.editor.detail');
    Route::get('/scenario/editor/learningDetail', 'ScenarioController@detailLearning')->name('scenario.editor.learningDetail');
    Route::post('/scenario/editor/connection/store', 'ScenarioController@connectionStore')->name('scenario.connection.store');
    Route::delete('/scenario/delete', 'ScenarioController@deleteNode')->name('scenario.delete');
    Route::post('/scenario', 'ScenarioController@store')->name('scenario.store');
    Route::post('/scenario/download/zip', 'ScenarioController@downloadZip')->name('scenario.download.zip');
    Route::post('/scenario/export/file', 'ScenarioController@exportFile')->name('scenario.export.file');
    //固有名詞
    Util::addCsvRoute('proper_noun');
    Route::get('/proper_noun/edit/{proper_noun_id}', 'ProperNounController@editFromProperNounId')->name('proper_noun.edit.proper_noun_id');
    Route::resource('/proper_noun', 'ProperNounController');
    // 関連質問
    Util::addCsvRoute('learning_relation');
    Route::resource('/learning_relation', 'LearningRelationController');
    // アンケート
    Util::addCsvRoute('enquete', null, ['export']);
    Route::resource('/enquete', 'EnqueteController');

    //裏ツール
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/tools', 'ToolsController@index')->name('tools.index');
        Route::get('/tools/api', 'ToolsController@api')->name('tools.api');
        Route::match(['get', 'post'], '/tools/stop_word', 'ToolsController@stopWord')->name('tools.stop_word');
        Route::get('/tools/truth', 'ToolsController@truth')->name('tools.truth');
        Route::get('/tools/truth_sync', 'ToolsController@truthSync')->name('tools.truth_sync');
        Route::match(['get', 'post'], '/tools/truth_morph', 'ToolsController@truthMorph')->name('tools.truth_morph');
        Route::get('/tools/truth_action', 'ToolsController@truthAction')->name('tools.truth_action');
        Route::get('/tools/truth_db', 'ToolsController@truthDb')->name('tools.truth_db');
        Route::get('/tools/scenario', 'ToolsController@scenario')->name('tools.scenario');
        Route::post('/tools/scenario', 'ToolsController@scenarioPost')->name('tools.scenario');
        Route::get('/tools/related_answer', 'ToolsController@relatedAnswer')->name('tools.related_answer');
        Route::post('/tools/related_answer', 'ToolsController@relatedAnswerPost')->name('tools.related_answer');
        Route::get('/tools/common', 'ToolsController@common')->name('tools.common');
    });

    //DashBoard
    Route::get('/report', 'ReportController@index')->name('report.list');
    Route::post('/upload/image', 'ReportController@uploadImage')->name('report.upload');
    Util::addCsvRoute('report', null, ['export']);
});
//Speech to text
Route::post('/speech/upload', 'Bot\SpeechController@uploadSpeech')->name('speech.upload');
//ログインページ
Auth::routes();

//デバッグ用
if (env('APP_DEBUG')) {
}
