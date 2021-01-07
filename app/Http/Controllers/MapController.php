<?php

namespace App\Http\Controllers;

use App\AvailableLayer;
use App\Entity;
use App\EntityType;
use App\Geodata;
use MStaack\LaravelPostgis\Geometries\Point;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Polygon;
use MStaack\LaravelPostgis\Geometries\MultiPoint;
use MStaack\LaravelPostgis\Geometries\MultiLineString;
use MStaack\LaravelPostgis\Geometries\MultiPolygon;
use \wapmorgan\UnifiedArchive\UnifiedArchive;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MapController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // GET

    public function getData() {
        $user = auth()->user();
        if(!$user->can('view_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to view the geo data')
            ], 403);
        }
        // layers: id => layer
        $layers = AvailableLayer::all()->getDictionary();
        // geoObjects: id => geoO
        $geodata = Geodata::with(['entity'])->get()->getDictionary();

        // Do not load unnecessary attributes
        foreach($geodata as $g) {
            if(isset($g->entity)) {
                $g->entity->setAppends([]);
            }
        }

        return response()->json([
            'layers' => $layers,
            'geodata' => $geodata
        ]);
    }

    public function getLayers(Request $request) {
        $user = auth()->user();
        if(!$user->can('view_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to view layers')
            ], 403);
        }
        $basic = $request->query('basic');
        $dict = $request->query('d');
        $basicOnly = isset($basic);
        $asDict = isset($dict);
        $baselayers = AvailableLayer::where('is_overlay', false)
            ->orderBy('id')
            ->get();
        $overlayQuery = AvailableLayer::with('entity_type')
            ->where('is_overlay', true)
            ->orderBy('id');
        if($basicOnly) {
            $overlayQuery->whereNull('entity_type_id')
                ->where('type', '!=', 'unlinked');
        }
        $overlays = $overlayQuery->get();

        if($asDict) {
            $baselayers = $baselayers->getDictionary();
            $overlays = $overlays->getDictionary();
        }

        return response()->json([
            'baselayers' => $baselayers,
            'overlays' => $overlays
        ]);
    }

    public function getEntityTypeLayers() {
        $user = auth()->user();
        if(!$user->can('view_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to view layers')
            ], 403);
        }
        $entityLayers = AvailableLayer::with(['entity_type'])
            ->whereNotNull('entity_type_id')
            ->orWhere('type', 'unlinked')
            ->orderBy('id')
            ->get();

        return response()->json($entityLayers);
    }

    public function getLayer($id) {
        $user = auth()->user();
        if(!$user->can('view_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to view a layer')
            ], 403);
        }
        try {
            $layer = AvailableLayer::with('entity_type')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This layer does not exist')
            ], 400);
        }

        return response()->json($layer);
    }

    public function getGeometriesByLayer($id) {
        $user = auth()->user();
        if(!$user->can('view_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to view geodata')
            ], 403);
        }

        try {
            $layer = AvailableLayer::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This layer does not exist')
            ], 400);
        }
        $query = Geodata::with(['entity'])->orderBy('id');
        if($layer->type == 'unlinked') {
            $query->doesntHave('entity');
        } else if(isset($layer->entity_type_id)) {
            $query->whereHas('entity', function($q) use ($layer) {
                $q->where('entity_type_id', $layer->entity_type_id);
            });
        }
        $geodata = $query->get();

        // Do not load unnecessary attributes
        foreach($geodata as $g) {
            if(isset($g->entity)) {
                $g->entity->setAppends([]);
            }
        }

        return response()->json($geodata);
    }

    public function getEpsg($srid) {
        $epsg = \DB::table('spatial_ref_sys')
            ->where('srid', $srid)
            ->first();
        return response()->json($epsg);
    }

    public function exportLayer(Request $request, $id) {
        $user = auth()->user();
        if(!$user->can('view_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to export layers')
            ], 403);
        }

        try {
            $layer = AvailableLayer::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This layer does not exist')
            ], 400);
        }
        if(strtoupper($layer->type) != 'UNLINKED' && !isset($layer->entity_type_id)) {
            return response()->json([
                'error' => __('This layer does not support export')
            ], 400);
        }
        if(strtoupper($layer->type) == 'UNLINKED') {
            $geodataBuilder = Geodata::doesntHave('entity');
        } else {
            $geodataBuilder = Geodata::with(['entity'])
                ->whereHas('entity', function($query) use($layer) {
                    $query->where('entity_type_id', $layer->entity_type_id);
                });
        }
        $query = sp_raw_query($geodataBuilder);
        $type = strtoupper($request->query('type', 'geojson'));
        $srid = strtoupper($request->query('srid', '4326'));
        $contentType = 'text/plain';
        switch($type) {
            case 'CSV':
                $geometry = 'AS_XYZ';
                $separator = 'COMMA';
                $exptype = 'CSV';
                $suffix = '.csv';
                $contentType = 'text/csv';
                break;
            case 'WKT':
                $geometry = 'AS_WKT';
                $separator = 'COMMA';
                $exptype = 'CSV';
                $suffix = '.csv';
                $contentType = 'text/csv';
                break;
            case 'KML':
            case 'KMZ':
                $exptype = 'KML';
                $suffix = '.kml';
                $contentType = 'application/vnd.google-earth.kml+xml';
                break;
            case 'GML':
                $exptype = 'GML';
                $suffix = '.gml';
                $contentType = 'application/gml+xml';
                break;
            case 'GEOJSON':
                //GeoJSON is default
            default:
                $exptype = 'GeoJSON';
                $suffix = '.json';
                $contentType = 'application/geo+json';
                break;
        }
        $host = env('DB_HOST');
        $port = env('DB_PORT');
        $user = env('DB_USERNAME');
        $db = env('DB_DATABASE');
        $pw = env('DB_PASSWORD');
        $dt = date('dmYHis');
        $tmpFile = '/tmp/export-'.$dt.$suffix;
        $command = ['ogr2ogr', '-f', $exptype, $tmpFile, "PG:host=$host port=$port user=$user dbname=$db password='$pw'", '-sql', $query];
        if($srid != '4326') {
            array_push($command, '-t_srs', "EPSG:$srid");
        }
        if($exptype == 'CSV') {
            array_push($command, '-lco', "GEOMETRY=$geometry", '-lco', "SEPARATOR=$separator", '-lco', 'LINEFORMAT=LF');
        }
        $process = new Process($command);
        $process->run();
        if(!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        if($type == 'KMZ') {
            $contentType = 'application/vnd.google-earth.kmz';
            $tmpZip = '/tmp/export-kml-'.$dt.'.zip';
            UnifiedArchive::archiveFiles($tmpFile, $tmpZip);
            // overwrite tmpFile path, because the zip should be downloaded
            unlink($tmpFile);
            $tmpFile = $tmpZip;
        }
        // get raw parsed content
        $content = file_get_contents($tmpFile);
        // delete tmp file
        unlink($tmpFile);
        return response(base64_encode($content))->header('Content-Type', $contentType);
    }

    // POST

    public function addGeometry(Request $request) {
        $user = auth()->user();
        if(!$user->can('create_edit_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to add geometric data')
            ], 403);
        }
        $this->validate($request, [
            'collection' => 'required|json',
            'srid' => 'required|integer',
            'metadata' => 'nullable|json',
        ]);

        $objs = Geodata::createFromFeatureCollection(json_decode($request->get('collection')), $request->get('srid'), json_decode($request->get('metadata')), $user);
        return response()->json($objs);
    }

    public function getEpsgByText(Request $request) {
        $srtext = $request->get('srtext');
        $epsg = \DB::table('spatial_ref_sys')
            ->where('srtext', $srtext)
            ->first();
        return response()->json($epsg);
    }

    public function addLayer(Request $request) {
        $user = auth()->user();
        if(!$user->can('create_edit_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to add layers')
            ], 403);
        }
        $this->validate($request, [
            'name' => 'required|string',
            'is_overlay' => 'nullable|boolean_string'
        ]);

        $isOverlay = $request->has('is_overlay') && $request->get('is_overlay') == 'true';

        $layer = AvailableLayer::createFromArray([
            'name' => $request->get('name'),
            'url' => '',
            'type' => '',
            'opacity' => 1,
            'visible' => true,
            'is_overlay' => $isOverlay,
        ]);

        return response()->json($layer);
    }

    public function link(Request $request, $gid, $eid) {
        $user = auth()->user();
        if(!$user->can('link_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to link geo data')
            ], 403);
        }
        try {
            $geodata = Geodata::findOrFail($gid);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This geodata does not exist')
            ], 400);
        }
        try {
            $entity = Entity::findOrFail($eid);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity does not exist')
            ], 400);
        }

        if(isset($entity->geodata_id)) {
            return response()->json([
                'error' => __('This entity is already linked to a geo object')
            ], 400);
        }

        $layer = AvailableLayer::where('entity_type_id', $entity->entity_type_id)->first();

        if($layer->type != 'any') {
            $typeMatched = false;
            $upperType = strtoupper($layer->type);
            if(($geodata->geom instanceof Polygon || $geodata->geom instanceof MultiPolygon) && Str::endsWith($upperType, 'POLYGON')) {
                $typeMatched = true;
            } else if(($geodata->geom instanceof LineString || $geodata->geom instanceof MultiLineString) && Str::endsWith($upperType, 'LINESTRING')) {
                $typeMatched = true;
            } else if(($geodata->geom instanceof Point || $geodata->geom instanceof MultiPoint) && Str::endsWith($upperType, 'POINT')) {
                $typeMatched = true;
            }
            if(!$typeMatched) {
                $geoType = get_class($geodata->geom);
                return response()->json([
                    'error' => "Layer type ('$layer->type') does not match type of geo object ('$geoType')"
                ], 400);
            }
        }

        $entity->geodata_id = $gid;
        $entity->save();

        return response()->json(null, 204);
    }

    // PUT

    // PATCH

    public function updateGeometry($id, Request $request) {
        $user = auth()->user();
        if(!$user->can('create_edit_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to edit geometric data')
            ], 403);
        }
        $this->validate($request, [
            'geometry' => 'required|json',
            'srid' => 'required|integer|exists:spatial_ref_sys,srid'
        ]);

        try {
            $geodata = Geodata::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This geodata does not exist')
            ], 400);
        }
        $geodata->patch($request->get('geometry'), $request->get('srid'), $user);

        return response()->json(null, 204);
    }

    public function updateLayer($id, Request $request) {
        $user = auth()->user();
        if(!$user->can('create_edit_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to update layers')
            ], 403);
        }
        $this->validate($request, AvailableLayer::patchRules);
        try {
            $layer = AvailableLayer::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This layer does not exist')
            ], 400);
        }

        $layer->patch($request->toArray());
        return response()->json(null, 204);
    }

    // DELETE

    public function delete($id) {
        $user = auth()->user();
        if(!$user->can('upload_remove_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to delete geo data')
            ], 403);
        }
        try {
            $geodata = Geodata::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This geodata does not exist')
            ], 400);
        }
        $geodata->delete();

        return response()->json(null, 204);
    }

    public function deleteLayer($id) {
        $user = auth()->user();
        if(!$user->can('upload_remove_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to delete layers')
            ], 403);
        }
        try {
            $layer = AvailableLayer::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This layer does not exist')
            ], 400);
        }
        if(isset($layer->entity_type_id) || $layer->type == 'unlinked') {
            return response()->json([
                'error' => __('This layer can not be deleted')
            ], 400);
        }

        $layer->delete();

        return response()->json(null, 204);
    }

    public function unlink(Request $request, $gid, $eid) {
        $user = auth()->user();
        if(!$user->can('link_geodata')) {
            return response()->json([
                'error' => __('You do not have the permission to unlink geo data')
            ], 403);
        }
        try {
            Geodata::findOrFail($gid);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This geodata does not exist')
            ], 400);
        }
        try {
            $entity = Entity::findOrFail($eid);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity does not exist')
            ], 400);
        }

        if($entity->geodata_id != $gid) {
            return response()->json([
                'error' => __('The entity is not linked to the provided geo object')
            ], 400);
        }

        $entity->geodata_id = NULL;
        $entity->save();

        return response()->json(null, 204);
    }
}
