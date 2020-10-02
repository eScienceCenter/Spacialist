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

Route::middleware(['before' => 'jwt.auth', 'after' => 'jwt.refresh'])->prefix('v1')->group(function() {
    Route::get('/pre', 'HomeController@getGlobalData');
    Route::get('/version', function() {
        $versionInfo = new App\VersionInfo();
        return response()->json([
            'full' => $versionInfo->getFullRelease(),
            'readable' => $versionInfo->getReadableRelease(),
            'release' => $versionInfo->getRelease(),
            'name' => $versionInfo->getReleaseName(),
            'time' => $versionInfo->getTime()
        ]);
    });
});

// CONTEXT
Route::middleware(['before' => 'jwt.auth', 'after' => 'jwt.refresh'])->prefix('v1/entity')->group(function() {
    Route::get('/top', 'EntityController@getTopEntities');
    Route::get('/{id}', 'EntityController@getEntity')->where('id', '[0-9]+');
    Route::get('/entity_type/{ctid}/data/{aid}', 'EntityController@getDataForEntityType')->where('ctid', '[0-9]+')->where('aid', '[0-9]+');
    Route::get('/{id}/data', 'EntityController@getData')->where('id', '[0-9]+');
    Route::get('/{id}/data/{aid}', 'EntityController@getData')->where('id', '[0-9]+')->where('aid', '[0-9]+');
    Route::get('/{id}/reference', 'ReferenceController@getByEntity')->where('id', '[0-9]+');
    Route::get('/{id}/parentIds', 'EntityController@getParentIds')->where('id', '[0-9]+');
    Route::get('/byParent/{id}', 'EntityController@getEntitiesByParent')->where('id', '[0-9]+');

    Route::post('', 'EntityController@addEntity');
    Route::post('/{id}/reference/{aid}', 'ReferenceController@addReference')->where('id', '[0-9]+')->where('aid', '[0-9]+');

    Route::patch('/{id}/attributes', 'EntityController@patchAttributes')->where('id', '[0-9]+');
    Route::patch('/{id}/attribute/{aid}', 'EntityController@patchAttribute')->where('id', '[0-9]+')->where('aid', '[0-9]+');
    Route::patch('/{id}/attribute/{aid}/moderation', 'EntityController@handleModeration')->where('id', '[0-9]+')->where('aid', '[0-9]+');
    Route::patch('/{id}/name', 'EntityController@patchName')->where('id', '[0-9]+');
    Route::patch('/{id}/rank', 'EntityController@moveEntity')->where('id', '[0-9]+');
    Route::patch('/reference/{id}', 'ReferenceController@patchReference')->where('id', '[0-9]+');

    Route::delete('/{id}', 'EntityController@deleteEntity')->where('id', '[0-9]+');
    Route::delete('/reference/{id}', 'ReferenceController@delete')->where('id', '[0-9]+');
});

// SEARCH
Route::middleware(['before' => 'jwt.auth', 'after' => 'jwt.refresh'])->prefix('v1/search')->group(function() {
    Route::get('', 'SearchController@searchGlobal');
    Route::get('/entity', 'SearchController@searchEntityByName');
    Route::get('/entity-type', 'SearchController@searchEntityTypes');
    Route::get('/label', 'SearchController@searchInThesaurus');
    Route::get('/attribute', 'SearchController@searchInAttributes');
    Route::get('/selection/{id}', 'SearchController@getConceptChildren')->where('id', '[0-9]+');
});

// EDITOR
Route::middleware(['before' => 'jwt.auth', 'after' => 'jwt.refresh'])->prefix('v1/editor')->group(function() {
    Route::get('/dm/entity_type/occurrence_count/{id}', 'EditorController@getEntityTypeOccurrenceCount')->where('id', '[0-9]+');
    Route::get('/dm/attribute/occurrence_count/{aid}', 'EditorController@getAttributeValueOccurrenceCount')->where('aid', '[0-9]+');
    Route::get('/dm/attribute/occurrence_count/{aid}/{ctid}', 'EditorController@getAttributeValueOccurrenceCount')->where('aid', '[0-9]+')->where('ctid', '[0-9]+');
    Route::get('/dm/entity_type/top', 'EditorController@getTopEntityTypes');
    Route::get('/dm/attribute', 'EditorController@getAttributes');
    Route::get('/dm/attribute_types', 'EditorController@getAttributeTypes');
    Route::get('/entity_type/{id}', 'EditorController@getEntityType')->where('id', '[0-9]+');
    Route::get('/entity_type/{id}/attribute', 'EditorController@getEntityTypeAttributes')->where('id', '[0-9]+');
    Route::get('/attribute/{id}/selection', 'EditorController@getAttributeSelection')->where('id', '[0-9]+');
    Route::get('/dm/geometry', 'EditorController@getAvailableGeometryTypes');

    Route::post('/dm/entity_type', 'EditorController@addEntityType');
    Route::post('/dm/{id}/relation', 'EditorController@setRelationInfo')->where('id', '[0-9]+');
    Route::post('/dm/attribute', 'EditorController@addAttribute');
    Route::post('/dm/entity_type/{ctid}/attribute', 'EditorController@addAttributeToEntityType')->where('ctid', '[0-9]+');
    Route::post('/dm/entity_type/{ctid}/duplicate', 'EditorController@duplicateEntityType')->where('ctid', '[0-9]+');

    Route::patch('/dm/entity_type/{ctid}/label', 'EditorController@patchLabel')->where('ctid', '[0-9]+');
    Route::patch('/dm/entity_type/{ctid}/attribute/{aid}/position', 'EditorController@reorderAttribute')->where('ctid', '[0-9]+')->where('aid', '[0-9]+');
    Route::patch('/dm/entity_type/{ctid}/attribute/{aid}/dependency', 'EditorController@patchDependency')->where('ctid', '[0-9]+')->where('aid', '[0-9]+');

    Route::delete('/dm/entity_type/{id}', 'EditorController@deleteEntityType')->where('id', '[0-9]+');
    Route::delete('/dm/attribute/{id}', 'EditorController@deleteAttribute')->where('id', '[0-9]+');
    Route::delete('/dm/entity_type/{ctid}/attribute/{aid}', 'EditorController@removeAttributeFromEntityType')->where('ctid', '[0-9]+')->where('aid', '[0-9]+');
});

// USER
Route::post('/v1/auth/login', 'UserController@login');

Route::middleware(['before' => 'jwt.auth', 'after' => 'jwt.refresh'])->prefix('v1')->group(function() {
    Route::get('/auth/refresh', 'UserController@refreshToken');
    Route::get('/auth/user', 'UserController@getUser');
    Route::get('/user', 'UserController@getUsers');
    Route::get('/role', 'UserController@getRoles');

    Route::post('/user', 'UserController@addUser');
    Route::post('/user/reset/password', 'Auth\\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/role', 'UserController@addRole');
    Route::post('/auth/logout', 'UserController@logout');

    Route::patch('/user/{id}', 'UserController@patchUser');
    Route::patch('/role/{id}', 'UserController@patchRole');

    Route::delete('/user/{id}', 'UserController@deleteUser')->where('id', '[0-9]+');
    Route::delete('/role/{id}', 'UserController@deleteRole')->where('id', '[0-9]+');
});

// PREFERENCES
Route::middleware(['before' => 'jwt.auth', 'after' => 'jwt.refresh'])->prefix('v1/preference')->group(function() {
    Route::get('', 'PreferenceController@getPreferences');
    Route::get('/{id}', 'PreferenceController@getUserPreferences')->where('id', '[0-9]+');

    Route::patch('/{id}', 'PreferenceController@patchPreference')->where('id', '[0-9]+');
});

// BIBLIOGRAPHY
Route::middleware(['before' => 'jwt.auth', 'after' => 'jwt.refresh'])->prefix('v1/bibliography')->group(function() {
    Route::get('/', 'BibliographyController@getBibliography');
    Route::get('/export', 'BibliographyController@exportBibtex');
    Route::get('/{id}/ref_count', 'BibliographyController@getReferenceCount')->where('id', '[0-9]+');

    Route::post('/', 'BibliographyController@addItem');
    Route::post('/import', 'BibliographyController@importBibtex');

    Route::patch('/{id}', 'BibliographyController@updateItem')->where('id', '[0-9]+');

    Route::delete('/{id}', 'BibliographyController@deleteItem')->where('id', '[0-9]+');
});

// EXTENSIONS

// FILE
Route::middleware(['before' => 'jwt.auth', 'after' => 'jwt.refresh'])->prefix('v1/file')->group(function() {
    Route::get('/{id}', 'FileController@getFile')->where('id', '[0-9]+');
    Route::get('/{id}/archive/list', 'FileController@getArchiveFileList')->where('id', '[0-9]+');
    Route::get('/{id}/archive/download', 'FileController@downloadArchivedFile')->where('id', '[0-9]+');
    Route::get('/{id}/as_html', 'FileController@getAsHtml')->where('id', '[0-9]+');
    Route::get('/{id}/link_count', 'FileController@getLinkCount')->where('id', '[0-9]+');
    Route::get('/{id}/sub_files', 'FileController@getSubFiles')->where('id', '[0-9]+');
    // Filters
    Route::get('/filter/category', 'FileController@getCategories');
    Route::get('/filter/camera', 'FileController@getCameraNames');
    Route::get('/filter/date', 'FileController@getDates');
    Route::get('/tags', 'FileController@getTags');


    Route::post('', 'FileController@getFiles');
    Route::post('/unlinked', 'FileController@getUnlinkedFiles');
    Route::post('/linked/{cid}', 'FileController@getLinkedFiles')->where('cid', '[0-9]+');
    Route::post('/new', 'FileController@uploadFile');
    // Should be patch, but file upload is not allowed as patch
    Route::post('/{id}/patch', 'FileController@patchContent')->where('id', '[0-9]+');
    Route::post('/export', 'FileController@exportFiles');

    Route::patch('/{id}/property', 'FileController@patchProperty')->where('id', '[0-9]+');
    Route::patch('/{id}/tag', 'FileController@patchTags')->where('id', '[0-9]+');

    Route::put('/{id}/link', 'FileController@linkToEntity')->where('id', '[0-9]+');

    Route::delete('/{id}', 'FileController@deleteFile')->where('id', '[0-9]+');
    Route::delete('/{fid}/link/{cid}', 'FileController@unlinkEntity')->where('fid', '[0-9]+')->where('cid', '[0-9]+');
});

// MAP
Route::middleware(['before' => 'jwt.auth', 'after' => 'jwt.refresh'])->prefix('v1/map')->group(function() {
    Route::get('', 'MapController@getData');
    Route::get('layer', 'MapController@getLayers');
    Route::get('layer/entity', 'MapController@getEntityTypeLayers');
    Route::get('layer/{id}', 'MapController@getLayer')->where('id', '[0-9]+');
    Route::get('layer/{id}/geometry', 'MapController@getGeometriesByLayer')->where('id', '[0-9]+');
    Route::get('epsg/{srid}', 'MapController@getEpsg')->where('srid', '[0-9]+');
    Route::get('export/{id}', 'MapController@exportLayer')->where('id', '[0-9]+');

    Route::post('', 'MapController@addGeometry');
    Route::post('epsg/text', 'MapController@getEpsgByText');
    Route::post('/layer', 'MapController@addLayer');
    Route::post('/link/{gid}/{eid}', 'MapController@link')->where('gid', '[0-9]+')->where('eid', '[0-9]+');

    Route::patch('/{id}', 'MapController@updateGeometry')->where('id', '[0-9]+');
    Route::patch('/layer/{id}', 'MapController@updateLayer')->where('id', '[0-9]+');

    Route::delete('/{id}', 'MapController@delete')->where('id', '[0-9]+');
    Route::delete('layer/{id}', 'MapController@deleteLayer')->where('id', '[0-9]+');
    Route::delete('/link/{gid}/{eid}', 'MapController@unlink')->where('gid', '[0-9]+')->where('eid', '[0-9]+');
});

// ANALYSIS
Route::middleware(['before' => 'jwt.auth', 'after' => 'jwt.refresh'])->prefix('v1/analysis')->group(function() {
    Route::post('export', 'AnalysisController@export');
    Route::post('export/{type}', 'AnalysisController@export');
    Route::post('filter', 'AnalysisController@applyFilterQuery');
});
