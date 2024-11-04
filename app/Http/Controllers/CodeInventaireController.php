<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CodeInventaire as DataModel;

use Illuminate\Support\Facades\Validator;

class CodeInventaireController extends Controller
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
        $bordereau_code = $request->bordereau_code;

        $total_code_by_bordereau = $request->total_code_by_bordereau;
        $total_code = $request->total_code;

        if(isset($total_code) && (boolval($total_code) == true || boolval($total_code) == 1)){
            
            return DataModel::count();
        }

        if(isset($total_code_by_bordereau) && (boolval($total_code_by_bordereau) == true || boolval($total_code_by_bordereau) == 1)){
            
            return DataModel::where('bordereau_code', $bordereau_code)->count();
        }

        $dataModel = DataModel::with(['bordereau', 'bien']);

        if(isset($bordereau_code) && intval($bordereau_code) > 0){
            $dataModel->where('bordereau_code', $bordereau_code); 
        }

        if(isset($request->code)){
            return $dataModel->where('code', $request->code)->first();
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
            'bordereau_code' => 'required|string|min:8|max:10',
            'code' => 'required|string|min:10|max:15|unique:code_inventaires',
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
                'required', 'string', 'min:10', 'max:15',
                Rule::unique('sous_familles')->ignore($id),
            ],
            'bordereau_code' => 'required|string|min:8|max:10',
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
}
