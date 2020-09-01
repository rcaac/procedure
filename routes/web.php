<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware'=>['guest']],function(){
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::get('/selectedEntity', 'EntityController@getSelectedEntity');
    Route::get('/dependencySearch', 'DependencyController@getDependencySearch');
});

Route::get('/tramite/seguimiento', 'ShowOutsideController@getDocumentShowOutside');
Route::get('/monitoring', 'ShowOutsideController@monitoring');
Route::get('/tramite', 'ShowOutsideController@process');
Route::get('/documentProcess', 'ShowOutsideController@getDocumentProcess');
Route::get('/documentProcessQuery/{id}', 'ShowOutsideController@getDocumentProcessQuery');
Route::get('/identification', 'ShowOutsideController@getIdentification');
Route::get('/documentTypeExternal', 'ShowOutsideController@getDocumentTypeExtrernal');
Route::post('/documentDestinationExternal', 'ShowOutsideController@getDocumentDestinationExternal');
Route::post('/documents/external', 'ShowOutsideController@store');
Route::get('/showPersonDate/{query}', 'ShowOutsideController@getShowPersonDate');
Route::get('/document/reportExternal/{id}', 'ShowOutsideController@printPdf');
Route::get('/document/reportTracingExternal/{id}', 'ShowOutsideController@getPdfMonitoring');
Route::post('/persistArchive', 'ShowOutsideController@setArchive');

Route::group(['middleware'=>['auth']],function(){

    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
    //Notificaciones
    Route::post('/notification/get', 'NotificationController@get');

    Route::group(['middleware' => ['web']], function () {

        Route::get('/role/selectRole', 'RoleController@selectRole');

        Route::get('/listDependencyChange', 'DependencyController@getListDependencyChange');
        Route::get('/listEntityDependencyChange', 'DependencyController@getListEntityDependencyChange');
        Route::get('/dependency', 'DependencyController@listDependency');
        Route::post('/dependency/register', 'DependencyController@store');
        Route::put('/dependency/update', 'DependencyController@update');
        Route::patch('/dependency', 'DependencyController@trash');
        Route::get('/dependency/trashed', 'DependencyController@trashed');
        Route::delete('/dependency/{id}/delete', 'DependencyController@destroy');
        Route::get('/selectDependency', 'DependencyController@getDependency');
        Route::get('/bringDependency/{id}', 'DependencyController@getListDependency');
        Route::get('/getAuthUser', 'DependencyController@getAuthUser');

        Route::get('/listEntityChange', 'EntityController@getListEntityChange');
        Route::get('/entity', 'EntityController@getListDependencyEntity');
        Route::post('/entity/register', 'EntityController@store');
        Route::put('/entity/update', 'EntityController@update');
        Route::patch('/entity/trash/{id}', 'EntityController@trash');
        Route::get('/entity/trashed', 'EntityController@trashed');
        Route::delete('/entity/{id}/delete', 'EntityController@destroy');
        Route::get('/selectEntityType', 'EntityController@getEntityType');
        Route::get('/selectListEntity/{id}', 'EntityController@getListEntity');
        Route::get('/entityDependencyId/{id}', 'EntityController@getEntityDependencyId');
        Route::get('/entityExternal', 'EntityController@getEntityExternal');

        Route::get('/user', 'UserController@index');
        Route::get('/showPerson', 'UserController@showPerson');
        Route::post('/user/register', 'UserController@store');
        Route::post('/userExternal/register', 'UserController@externalStore');
        Route::post('/personDependency', 'UserController@personDependencyCharge');
        Route::put('/user/update', 'UserController@update');
        Route::post('chargeUser', 'UserController@registerCharge');
        Route::put('/user/editCharge', 'UserController@updateCharge');
        Route::post('/chargeAssignment/{id}', 'UserController@listChargeAssignment');
        Route::get('/user/listUserTrashed', 'UserController@getListUserTrashed');
        Route::get('/user/listChargeTrashed', 'UserController@getListChargeTrashed');
        Route::patch('/user/{id}', 'UserController@trash');
        Route::patch('/charge/{id}', 'UserController@chargeTrash');
        Route::get('/user/trashed', 'UserController@trashed');
        Route::delete('/user/{id}/delete', 'UserController@destroy');
        Route::get('/authUser', 'UserController@getAuthUser');
        Route::get('/identificationDocument', 'UserController@getIdentificationDocument');
        Route::put('/userAuthUpdate', 'UserController@userAuthUpdate');

        Route::get('/module', 'ModuleController@index');
        Route::post('/module/register', 'ModuleController@store');
        Route::put('/module/update', 'ModuleController@update');
        Route::patch('/module/{id}', 'ModuleController@trash');
        Route::get('/module/trashed', 'ModuleController@trashed');
        Route::get('/selectedModules', 'ModuleController@getSelectedModules');

        Route::get('/attentionTypes', 'AttentionTypeController@index');
        Route::post('/attentionTypes/register', 'AttentionTypeController@store');
        Route::put('/attentionTypes/update', 'AttentionTypeController@update');
        Route::patch('/attentionTypes/{id}', 'AttentionTypeController@trash');
        Route::get('/attentionTypes/trashed', 'AttentionTypeController@trashed');
        Route::get('/procedure/attentionTypes/{id}', 'AttentionTypeController@listAttentionTypes');

        Route::get('/requirement', 'RequirementController@index');
        Route::post('/requirement/register', 'RequirementController@store');
        Route::get('/requirement/{id}', 'RequirementController@getListRequirement');
        Route::patch('/trashRequirement/{id}', 'RequirementController@trashRequirement');

        Route::get('/procedure', 'ProcedureController@index');
        Route::post('/procedure/register', 'ProcedureController@store');
        Route::put('/procedure/update', 'ProcedureController@update');
        Route::patch('/trashProcedure/{id}', 'ProcedureController@trashProcedure');
        Route::get('/listProcedure', 'ProcedureController@listProcedure');
        Route::get('/listProcedureQualification', 'ProcedureController@listProcedureQualification');

        Route::put('/procedureRequirement/update', 'ProcedureRequirementController@update');
        Route::get('/dependentTheProcedure', 'ProcedureRequirementController@getDependentTheProcedure');

        Route::post('/attentionProcedure/register', 'AttentionProcedureController@store');
        Route::put('/attentionProcedure/update', 'AttentionProcedureController@update');
        Route::patch('/trashAttentionProcedure/{id}', 'AttentionProcedureController@trashAttentionProcedure');

        Route::get('/documents', 'DocumentController@index');
        Route::get('/dependencyCharge/{id}', 'DocumentController@getDependencyCharge');
        Route::get('/dependencyBringType/{id}', 'DocumentController@getdependencyBringType');
        Route::get('/documentPersonCharge', 'DocumentController@getPersonCharge');
        Route::get('/documentPersonQuery', 'DocumentController@getPersonQuery');
        Route::get('/documentDependency/{id}', 'DocumentController@getDocumentDependency');
        Route::get('/documentDependencyCharge/{id}', 'DocumentController@getDocumentDependencyCharge');
        Route::get('/documentProcedureSearch', 'DocumentController@getDocumentProcedureSearch');
        Route::get('/documentProcedureSearchExternal', 'DocumentController@getDocumentProcedureSearchExternal');
        Route::get('/documentProcedureQuery/{id}', 'DocumentController@getDocumentProcedureQuery');
        Route::get('/documentProcedureQueryProcess/{id}', 'DocumentController@getDocumentProcedureProcess');
        Route::get('/documentProcedureQueryState/{id}', 'DocumentController@getDocumentProcedureQueryState');
        Route::get('/documentDependencyDestination', 'DocumentController@getDocumentDependencyDestination');
        Route::get('/documentDependencyReport', 'DocumentController@getDocumentDependencyReport');
        Route::get('/documentDependencyReception', 'DocumentController@getDocumentDependencyReception');
        Route::post('/documentDestination', 'DocumentController@setDocumentDestination');
        Route::patch('/documentProvidedDelete/{id}', 'DocumentController@deleteProvided');
        Route::get('/detailDocumentType', 'DocumentTypeController@getDetailDocumentType');
        Route::get('/documentTypes/{id}', 'DocumentController@getDocumentTypes');
        Route::get('/documentToSent', 'DocumentController@getDocumentToSent');
        Route::post('/documents/register', 'DocumentController@store');
        Route::put('/documents/update', 'DocumentController@update');
        Route::patch('/documents/{id}', 'DocumentController@trash');
        Route::get('/documents/trashed', 'DocumentController@trashed');
        Route::get('/documentReferences', 'DocumentController@getDocumentReferences');
        Route::get('/checkedRequirements', 'DocumentController@checkedRequirements');
        Route::put('/document/updateObservationRequirement', 'DocumentController@updateObservationRequirement');
        Route::put('/sentDocument/update', 'DocumentController@sentDocumentUpdate');
        Route::get('/getTupa', 'DocumentController@getTupa');
        Route::get('/personReception', 'DocumentController@getPersonReception');
        Route::get('/documentAuth', 'DocumentController@getDocumentAuth');
        Route::get('/documentCharts', 'DocumentController@getDocumentCharts');
        Route::get('/documentModalInternal/{id}', 'DocumentController@getDocumentModalInternal');
        Route::get('/documentModalExternal/{id}/{dependency_id}/{person_id}', 'DocumentController@getDocumentModalExternal');

        Route::get('/slopes', 'DocumentPendingController@getSlopes');
        Route::get('/slopes/{id}', 'DocumentPendingController@slopesFetched');
        Route::get('/partyTable', 'DocumentPendingController@getPartyTable');
        Route::get('/partyTableReport', 'DocumentPendingController@getPartyTableReport');

        Route::get('/tableDocumentReport', 'DocumentReportController@getTableDocumentReport');
        Route::get('/tableDocumentReportInternal', 'DocumentReportController@getTableDocumentReportInternal');
        Route::get('/tableReport', 'DocumentReportController@getTableReport');
        Route::put('/manageInternal/update', 'DocumentReportController@update');
        Route::patch('/internal', 'DocumentReportController@delete');

        Route::get('/process', 'DocumentProcessController@getProcess');
        Route::post('/process/register', 'DocumentProcessController@setDerive');
        Route::post('/documentReturn', 'DocumentProcessController@documentReturn');

        Route::get('/sent', 'DocumentSentController@getSent');
        Route::post('/documentInvalid', 'DocumentSentController@documentInvalid');
        Route::post('/documentFinalize', 'DocumentSentController@documentFinalize');
        Route::get('/changeDependencySent/{id}', 'DocumentSentController@getDocumentSend');
        Route::post('/addDocumentSent', 'DocumentSentController@setDocumentsSent');
        Route::put('/sent/update', 'DocumentSentController@update');
        Route::patch('/externalRequirement/{id}', 'DocumentSentController@deleteRequirement');
        Route::post('/documentRequirementUpdate', 'DocumentSentController@documentRequirementUpdate');
        Route::post('/documentAnnexeUpdate', 'DocumentSentController@documentAnnexeUpdate');
        Route::patch('/externalAnnexe', 'DocumentSentController@deleteAnnexe');
        Route::post('/documentAnnexeCreate', 'DocumentSentController@documentAnnexeCreate');

        Route::get('/records', 'DocumentArchiveController@getRecords');
        Route::post('/records', 'DocumentArchiveController@recordFetched');
        Route::post('/documentUnarchive', 'DocumentArchiveController@documentUnarchive');

        Route::get('/tracing', 'DocumentTracingController@getTracing');
        Route::get('/tracingRequirement/{id}', 'DocumentTracingController@getTracingRequirement');

        Route::get('/documentType', 'DocumentTypeController@getDocumentType');
        Route::post('/documentType/register', 'DocumentTypeController@store');
        Route::put('/documentType/update', 'DocumentTypeController@update');
        Route::patch('/documentType/{id}', 'DocumentTypeController@trash');
        Route::get('/documentType/trashed', 'DocumentTypeController@trashed');

        Route::get('/processState', 'ProcessStateController@getProcessState');
        Route::post('/processState/register', 'ProcessStateController@store');
        Route::put('/processState/update', 'ProcessStateController@update');
        Route::patch('/processState/{id}', 'ProcessStateController@trash');
        Route::get('/processState/trashed', 'ProcessStateController@trashed');

        Route::get('/procedureState', 'ProcedureStateController@getProcedureState');
        Route::post('/procedureState/register', 'ProcedureStateController@store');
        Route::put('/procedureState/update', 'ProcedureStateController@update');
        Route::patch('/procedureState/{id}', 'ProcedureStateController@trash');
        Route::get('/procedureState/trashed', 'ProcedureStateController@trashed');

        Route::get('/documentOrigin', 'DocumentOriginController@getDocumentOrigin');
        Route::post('/documentOrigin/register', 'DocumentOriginController@store');
        Route::put('/documentOrigin/update', 'DocumentOriginController@update');
        Route::patch('/documentOrigin/{id}', 'DocumentOriginController@trash');
        Route::get('/documentOrigin/trashed', 'DocumentOriginController@trashed');

        Route::get('/provided', 'ProvidedController@getProvided');
        Route::post('/provided/register', 'ProvidedController@store');
        Route::put('/provided/update', 'ProvidedController@update');
        Route::patch('/provided/{id}', 'ProvidedController@trash');
        Route::get('/provided/trashed', 'ProvidedController@trashed');        

        Route::get('/documentAnnexes/{id}', 'DocumentController@getAnnexes');

        Route::get('/pending', 'DocumentPendingController@getCountPending');
        Route::get('/printDocument/{id}', 'DocumentSentController@getPrintDocument');

        Route::get('/document/reportDocument/{id}', 'DocumentController@getPdfDocument');

        Route::get('/{vue?}', function () {
            return view('templates.content');
           })->where('vue', '[\/\w\.-]*');
    });
});
