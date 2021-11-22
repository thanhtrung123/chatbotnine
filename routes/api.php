<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//利用者側
Route::group(['prefix' => 'bot', 'as' => 'api.bot.'], function () {
    Route::post('/', 'Bot\ApiController@index')->name('index');
    Route::post('/sns', 'Bot\ApiController@sns')->name('sns');
    Route::post('/userLog', 'Bot\ApiController@userLog')->name('user_log');
    Route::get('/suggest', 'Bot\ApiController@suggest')->name('suggest');
});

//管理側
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'as' => 'api.admin.', 'middleware' => 'auth:api'], function () {
    //list
    Route::get('/user/list', 'UserController@userList')->name('user.list');
    Route::get('/role/list', 'RoleController@roleList')->name('role.list');
    Route::get('/learning/list', 'LearningController@learningList')->name('learning.list');
    Route::get('/synonym/list', 'SynonymController@synonymList')->name('synonym.list');
    Route::get('/variant/list', 'VariantController@variantList')->name('variant.list');
    Route::get('/log/list', 'LogController@logList')->name('log.list');
    Route::get('/response_info/list', 'ResponseInfoController@responseInfoList')->name('response_info.list');
    Route::get('/response_aggregate/ranking_list', 'ResponseAggregateController@rankingList')->name('response_aggregate.ranking_list');
    Route::get('/response_aggregate/overview_list', 'ResponseAggregateController@overviewList')->name('response_aggregate.overview_list');
    Route::get('/key_phrase/list', 'KeyPhraseController@keyPhraseList')->name('key_phrase.list');
    Route::get('/category/list', 'CategoryController@CategoryList')->name('category.list');
    Route::get('/scenario/list', 'ScenarioController@scenarioList')->name('scenario.list');
    Route::get('/proper_noun/list', 'ProperNounController@properNounList')->name('proper_noun.list');
    Route::get('/learning_relation/list', 'LearningRelationController@learningRelationList')->name('learning_relation.list');
    Route::get('/enquete/list', 'EnqueteController@enqueteList')->name('enquete.list');
    //detail
    Route::get('/response_info/detail', 'ResponseInfoController@responseInfoDetail')->name('response_info.detail');
    Route::get('/response_info/truth_detail', 'ResponseInfoController@responseInfoTruthDetail')->name('response_info.truth_detail');
    //ajax
    Route::get('/learning/sync', 'LearningController@sync')->name('learning.sync');
    //choice
    Route::get('/key_phrase/choice', 'KeyPhraseController@keyPhraseChoice')->name('key_phrase.choice');
    Route::get('/scenario_keyword/choice', 'ScenarioController@keywordChoice')->name('scenario_keyword.choice');
    // Scenario save zip
    Route::post('/scenario/save/zip', 'ScenarioController@saveFileZip')->name('scenario.save.zip');
    // Scenario save file excel
    Route::post('/scenario/save/file', 'ScenarioController@getScenarioFileExport')->name('scenario.save.file');
     // Scenario import file zip
    Route::post('/scenario/import/zip', 'ScenarioController@importZip')->name('scenario.import.zip');
});