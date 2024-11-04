<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Site;
use App\Models\Localisation;
use App\Models\CodeInventaire;
use App\Models\Bordereau as DataModel;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;

class BordereauController extends Controller
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
        $localisation_id = $request->localisation_id;
        $site_code = $request->site_code;
        $total_bordereau_by_site = $request->total_bordereau_by_site;
        $total_bordereau_by_localisation = $request->total_bordereau_by_localisation;
        $total_bordereau = $request->total_bordereau;

        if(isset($total_bordereau) && (boolval($total_bordereau) == true || boolval($total_bordereau) == 1)){
            
            return DataModel::count();
        }

        if(isset($total_bordereau_by_site) && (boolval($total_bordereau_by_site) == true || boolval($total_bordereau_by_site) == 1)){
            
            return DataModel::where('site_code', $site_code)->count();
        }

        $dataModel = DataModel::with(['site', 'localisation']);
        
        if(isset($site_code)){
            $dataModel->where('site_code', $site_code); 
        }

        if(isset($localisation_id) && intval($localisation_id) > 0){
            $dataModel->where('localisation_id', intval($localisation_id)); 
        }

        if(isset($request->localisation_code)){

            $localisation = Localisation::where('code', $request->localisation_code)->first();
            if($localisation != null){
                $dataModel->where('localisation_id', $localisation->id); 
            }
        }

        if(isset($request->code)){
            return $dataModel->where('code', $request->code)->first();
        }

        $dataModel->orderBy("id", "desc");

        if(isset($total_bordereau_by_localisation) && (boolval($total_bordereau_by_localisation) == true || boolval($total_bordereau_by_localisation) == 1)){
            
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

        return $dataModel->get();
    }

    public function validation($data){

        return $validator = Validator::make($data, [
            'code' => 'required|string|min:8|max:10|unique:bordereaus',
            'libelle' => 'required|string|max:255',
            'localisation_id' => 'nullable|int|max:255',
            'site_code' => 'nullable|string|max:4'
        ]);
    }

    public function importValidation($data){

        return $validator = Validator::make($data, [
            'code' => 'required|string|min:8|max:10|unique:bordereaus',
            'libelle' => 'required|string|max:255',
            'localisation_id' => 'nullable|int|max:255',
            'site_code' => 'nullable|string|max:4'
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

                    $site_code = substr($value['code'], 2, 4);

                    $site = Site::where('code', $site_code)->first();

                    if($site != null){

                        $value['site_code'] = $site_code;

                        $code_inventaires_array = [];
                        $code = $value['code'];

                        for ($i=1; $i <= 25; $i++) {

                            $index = $i;
                            if(strlen($index)<2){
                                $index = "0".$index;
                            }

                            $code_inventaires_array[] = [
                                "bordereau_code" => $code,
                                "code" => $code.$index,
                            ];
                        }

                        DataModel::create($value);

                        CodeInventaire::insert($code_inventaires_array);
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

        $site_code = substr($request->code, 2, 4);

        $site = Site::where('code', $site_code)->first();
        if($site == null){
            return response([
                "message" => "Impossible de créer ce bordereau. Le site ayant pour code $site_code n'existe pas.",
                "error" => 1
            ], 403);
        }

        $request->site_code = substr($request->code, 2, 4);

        $code_inventaires_array = [];
        $code = $request->code;

        for ($i=1; $i <= 25; $i++) {

            $index = $i;
            if(strlen($index)<2){
                $index = "0".$index;
            }

            $code_inventaires_array[] = [
                "bordereau_code" => $code,
                "code" => $code.$index,
            ];
        }

        $body = $request->all();
        $body['site_code'] = $site_code;

        $response = DataModel::create($body);

        CodeInventaire::insert($code_inventaires_array);

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return DataModel::with(['site', 'localisation'])->find($id);
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
                'required', 'string', 'min:8', 'max:10',
                Rule::unique('bordereaus')->ignore($id),
            ],
            'libelle' => 'required|string|max:255',
            'localisation_id' => 'nullable|int|max:255',
            'site_code' => 'nullable|string|max:4'
        ]); 

        if ($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
        }

        $body = $request->all();
        $body['site_code'] = substr($request->code, 2, 4);

        return DataModel::where('id', $id)->update($body);
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
     * @param  int  $localalisation_code
     * @return \Illuminate\Http\Response
     */

    public function getCode($localisation_code, $year)
    {
        $localisation = Localisation::where('code', $localisation_code)->firstOrFail();
        $last = DataModel::where('localisation_id', $localisation->id)->latest()->first();

        $last_id = isset($last)? $last->id : 0;
        $last_id = intval($last_id) + 1;

        if(strlen($last_id) < 2){
            $last_id = "0".$last_id;
        }

        $site_code = substr($localisation_code, 0, 4);

        $year_code = strlen($year) == 2 ? $year : substr($year, 2, 4);

        return $year_code."".$site_code."".$last_id;
    }

    public function create25InventaireCode($borderau_code)
    {
        $code_inventaires_array = [];

        DataModel::where('code', $borderau_code)->firstOrFail();

        $code = CodeInventaire::where('bordereau_code', $borderau_code)->count();

        if(isset($code) && $code >= 25){

            return response([
                "message" => "Impossible de générer 25 code inventaires pour ce bordereau. Le borderau $borderau_code a dëjà $code code inventaires.",
                "error" => 1
            ], 403);
        }

        $last = CodeInventaire::where('bordereau_code', $borderau_code)->latest()->first();
        
        $last_id = isset($last)? intval(substr($last->code, -2)) : 1;

        for ($i=$last_id; $i <= 25; $i++) {

            $index = $i;
            if(strlen($index)<2){
                $index = "0".$index;
            }

            $code_inventaires_array[] = [
                "bordereau_code" => $borderau_code,
                "code" => $borderau_code.$index,
            ];
        }

        return CodeInventaire::insert($code_inventaires_array);
    }
}
