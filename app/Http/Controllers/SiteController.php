<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zone;
use App\Models\Localisation;
use App\Models\Site as DataModel;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|root','permission:delete all'])
        ->only(['destroys']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $zone_id = $request->zone_id;
        $zone_code = $request->zone_code;
        $total_site_by_zone = $request->total_site_by_zone;
        $total_site = $request->total_site;

        if(isset($total_site) && (boolval($total_site) == true || boolval($total_site) == 1)){
            
            return DataModel::count();
        }

        $dataModel = DataModel::with(['zone']);

        if(isset($zone_code)){
            $zone = Zone::where('code', $zone_code)->first();
            if(isset($zone)){
                $zone_id = $zone->id;
            }
        }

        if(isset($zone_id) && intval($zone_id) > 0){
            $dataModel->where('zone_id', intval($zone_id)); 
        }

        $dataModel->orderBy("id", "desc");

        if(isset($total_site_by_zone) && (boolval($total_site_by_zone) == true || boolval($total_site_by_zone) == 1)){
            
            return $dataModel->count();
        }

        if(isset($request->per_page) && intval($request->per_page) > 0){
            return $dataModel->paginate(intval($request->per_page)); 
        }

        if(isset($request->limit) && intval($request->limit)  > 1){
            $dataModel->limit(intval($request->limit));
        }

        if(isset($request->limit) && intval($request->limit) == 1){

            return $dataModel->first();
        }

        if(isset($request->code)){
            return $dataModel->where('code', $request->code)->first();
        }

        return $dataModel->get();
    }

    public function validation($data){

        return $validator = Validator::make($data, [
            'code' => 'required|string|min:4|max:4|unique:sites',
            'libelle' => 'required|string|max:255',
            'zone_id' => 'required|int|max:255'
        ]);
    }

    public function importValidation($data){

        return $validator = Validator::make($data, [
            'code' => 'required|string|min:4|max:4|unique:sites',
            'libelle' => 'required|string|max:255'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $array = $request->array;
        $valid_array = [];

        if(isset($array) && is_array($array) && !empty($array)){
            
            foreach ($array as $key => $value) {

                $validator = $this->importValidation($value);
                if (!$validator->fails())
                {
                    $valid_array[] = $value;

                    $zone_code = substr($value['code'], 0, 2);
                    $zone = Zone::where('code', $zone_code)->first();

                    if($zone != null){
                        $value['zone_id'] = $zone->id;
                        DataModel::create($value);
                    }  
                }
            }

            return $valid_array;
        }

        $validator = $this->validation($request->all());

        if ($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
        }

        $zone_code = substr($request->code, 0, 2);

        $zone = Zone::where('code', $zone_code)->first();
        if($zone == null){
            return response([
                "message" => "Impossible de créer ce site. La zone ayant pour code $zone_code n'existe pas.",
                "error" => 1
            ], 403);
        }

        return DataModel::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return DataModel::with(['zone'])->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => [
                'required', 'string', 'min:4', 'max:4',
                Rule::unique('sites')->ignore($id),
            ],
            'libelle' => 'required|string|max:255',
            'zone_id' => 'required|int|max:255'
        ]); 

        if ($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
        }

        $zone_code = substr($request->code, 0, 2);

        $zone = Zone::where('code', $zone_code)->first();
        if($zone == null){
            return response([
                "message" => "Impossible de créer ce site. La zone ayant pour code $zone_code n'existe pas.",
                "error" => 1
            ], 403);
        }

        return DataModel::where('id', $id)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model_has_chield = Localisation::where('site_id', $id)->count();
        if(isset($model_has_chield) && $model_has_chield > 0){
            return response([
                "message" => "Impossible de supprimer cet site car il est rattaché à des localisations.",
                "error" => 1
            ], 401);  
        }
        return DataModel::destroy($id);
    }

    /**
     * Create a resource code.
     *
     * @param  int  $zone_code
     * @return \Illuminate\Http\Response
     */
    public function getCode($zone_code) 
    {

        $zone = Zone::where('code', $zone_code)->firstOrFail();
        $last = DataModel::where('zone_id', $zone->id)->latest()->first();

        $last_id = isset($last)? $last->id : 0;
        $last_id = intval($last_id) + 1;
        
        if(strlen($last_id) < 2){
            $last_id = "0".$last_id;
        }

        return response(['code' => $zone->code."".$last_id], 200);
    }
}
