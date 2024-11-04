<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zone as DataModel;
use App\Models\Site;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
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
        if(isset($request->per_page) && intval($request->per_page) > 0){
            return DataModel::paginate(intval($request->per_page)); 
        }

        if(isset($request->code)){
            return DataModel::where('code', $request->code)->first();
        }

        return DataModel::with(['sites'])->orderBy("id", "desc")->get();
    }

    public function validation($data){

        return $validator = Validator::make($data, [
            'code' => 'required|string|min:2|max:2|unique:zones',
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

                $validator = $this->validation($value);
                if (!$validator->fails())
                {
                    $valid_array[] = $value;
                    DataModel::create($value);
                }
            }

            return $valid_array;
        }

        $validator = $this->validation($request->all());

        if ($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
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
        return DataModel::find($id);
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
                'required', 'string', 'min:2', 'max:2',
                Rule::unique('zones')->ignore($id),
            ],
            'libelle' => 'required|string|max:255'
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
        $zone_has_sites = Site::where('zone_id', $id)->count();
        if(isset($zone_has_sites) && $zone_has_sites > 0){
            return response([
                "message" => "Impossible de supprimer cette zone car elle est rattachée à des sites.",
                "error" => 1
            ], 401);  
        }
        return DataModel::destroy($id);
    }
}
