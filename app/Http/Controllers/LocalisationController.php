<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Site;
use App\Models\Localisation as DataModel;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;

class LocalisationController extends Controller
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
        $site_id = $request->site_id;
        $site_code = $request->site_code;
        $total_localisation_by_site = $request->total_localisation_by_site;
        $total_localisation = $request->total_localisation;
        $code = $request->code;

        if(isset($total_localisation) && (boolval($total_localisation) == true || boolval($total_localisation) == 1)){
            
            return DataModel::count();
        }

        $dataModel = DataModel::with(['site']);

        if(isset($site_code)){
            $site = Site::where('code',$site_code)->first();
            if(isset($site)){
                $site_code = $site->id;
            }
        }

        if(isset($site_id) && intval($site_id) > 0){
            $dataModel->where('site_id', intval($site_id)); 
        }

        $dataModel->orderBy("id", "desc");

        if(isset($total_localisation_by_site) && (boolval($total_localisation_by_site) == true || boolval($total_localisation_by_site) == 1)){
            
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
        
        if(isset($code)){
            return DataModel::with(['site', 'immobilisations', 'bordereaux'])->where('code', $code)->first(); 
        }

        return $dataModel->get();
    }

    public function validation($data){

        return $validator = Validator::make($data, [
            'code' => 'required|string|min:7|max:7|unique:localisations',
            'libelle' => 'required|string|max:255',
            'site_id' => 'required|int|max:255'
        ]); 
    }

    public function importValidation($data){

        return $validator = Validator::make($data, [
            'code' => 'required|string|min:7|max:7|unique:localisations',
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
                    $site_code = substr($value['code'], 0, 4);
                    $site = Site::where('code', $site_code)->first();
                    
                    if($site != null){
                        $value['site_id'] = $site->id;
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

        $site_code = substr($request->code, 0, 4);

        $site = Site::where('code', $site_code)->first();
        if($site == null){
            return response([
                "message" => "Impossible de créer cette localisation. Le site ayant pour code $site_code n'existe pas.",
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
        return DataModel::with(['site', 'immobilisations', 'bordereaux'])->find($id);
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
                'required', 'string', 'min:7', 'max:7',
                Rule::unique('localisations')->ignore($id),
            ],
            'libelle' => 'required|string|max:255',
            'site_id' => 'required|int|max:255'
        ]); 

        if ($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
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
        return DataModel::destroy($id);
    }

    /**
     * Create a resource code.
     *
     * @param  int  $site_code
     * @return \Illuminate\Http\Response
     */
    public function getCode($site_code)
    {
        $site = Site::where('code', $site_code)->firstOrFail();
        $last = DataModel::where('site_id', $site->id)->latest()->first();

        $last_id = isset($last)? $last->id : 0;
        $last_id = intval($last_id) + 1;
        
        if(strlen($last_id) > 2 && strlen($last_id) < 3){
            $last_id = "0".$last_id;
        }
        if(strlen($last_id) < 2){
            $last_id = "00".$last_id;
        }

        return $site->code."".$last_id;
    }
}
