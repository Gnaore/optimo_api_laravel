<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SousFamille as DataModel;
use Illuminate\Validation\Rule;
use App\Models\Famille;

use Illuminate\Support\Facades\Validator;

class SousFamilleController extends Controller
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
        $famille_id = $request->famille_id;

        $dataModel = DataModel::with(['famille']);

        if(isset($famille_id) && intval($famille_id) > 0){
            $dataModel->where('famille_id', intval($famille_id)); 
        }

        $dataModel->orderBy("id", "desc");
        
        if(isset($request->code)){
            return $dataModel->where('code', $request->code)->first();
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|min:2|max:255|unique:sous_familles',
            'libelle' => 'required|string|max:255',
            'famille_id' => 'nullable|int|max:255'
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
                Rule::unique('sous_familles')->ignore($id),
            ],
            'libelle' => 'required|string|max:255',
            'famille_id' => 'nullable|int|max:255'
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