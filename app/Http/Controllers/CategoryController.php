<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category as DataModel;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;
class CategoryController extends Controller
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
        $dataModel = Category::orderBy('id', 'desc');

        if(isset($request->per_page) && intval($request->per_page) > 0){
            $dataModel->paginate(intval($request->per_page)); 
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|min:2|max:255|unique:categories',
            'libelle' => 'required|string|max:255'
        ]); 

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
                'required', 'string', 'min:2', 'max:255',
                Rule::unique('categories')->ignore($id),
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
        $model_has_chield = Famille::where('category_id', $id)->count();
        if(isset($model_has_chield) && $model_has_chield > 0){
            return response([
                "message" => "Impossible de supprimer cette catégorie car elle est rattachée à des familles.",
                "error" => 1
            ], 401);  
        }

        return DataModel::destroy($id);
    }
}
