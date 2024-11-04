<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Famille as DataModel;
use App\Models\SousFamille;
use App\Models\Category;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;

class FamilleController extends Controller
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
        $category_id = $request->category_id;

        $dataModel = DataModel::with(['category']);

        if(isset($category_id) && intval($category_id) > 0){
            $dataModel->where('category_id', intval($category_id)); 
        }

        $dataModel->orderBy("id", "desc");

        if(isset($request->per_page) && intval($request->per_page) > 0){
            return $dataModel->paginate(intval($request->per_page)); 
        }

        if(isset($request->limit) && intval($request->limit)  > 1){
            $dataModel->limit(intval($request->limit));
        }

        if(isset($request->limit) && intval($request->limit) == 1){

            return $dataModel->first();
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
        
        return $dataModel->get();
    }

    public function validation($data){

        return $validator = Validator::make($data, [
            'code' => 'nullable|string|min:2|max:255|unique:familles',
            'libelle' => 'required|string|max:255',
            'category_id' => 'nullable|int|max:255'
        ]);
    }

    public function importValidation($data){

        return $validator = Validator::make($data, [
            'code' => 'nullable|string|min:2|max:255|unique:familles',
            'libelle' => 'required|string|max:255',
            'category_id' => 'nullable|int|max:255'
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
        return DataModel::with(['category'])->find($id);
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
                'nullable', 'string', 'min:2', 'max:255',
                Rule::unique('familles')->ignore($id),
            ],
            'libelle' => 'required|string|max:255',
            'category_id' => 'nullable|int|max:255'
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
        $model_has_chield = SousFamille::where('famille_id', $id)->count();
        if(isset($model_has_chield) && $model_has_chield > 0){
            return response([
                "message" => "Impossible de supprimer cette famille car elle est rattachée à des sous familles.",
                "error" => 1
            ], 401);  
        }

        return DataModel::destroy($id);
    }
}
