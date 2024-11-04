<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BonCommande as DataModel;
use App\Models\Fournisseur;
use Illuminate\Support\Facades\Validator;

class BonCommandeController extends Controller
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
        $dataModel = DataModel::with(['fournisseur'])->orderBy('id', 'desc');

        if(isset($request->per_page) && intval($request->per_page) > 0){
            $dataModel->paginate(intval($request->per_page)); 
        }

        if(isset($request->limit) && intval($request->limit)  > 1){
            $dataModel->limit(intval($request->limit));
        }

        if(isset($request->code)){
            return DataModel::where('code', $request->code)->first();
        }
        
        if(isset($request->limit) && intval($request->limit) == 1){

            return $dataModel->first();
        }

        return $dataModel->get();
    }

    public function validation($data){

        return $validator = Validator::make($data, [
            'code' => 'required|string|min:2|max:255',
        ]);
    }

    public function importValidation($data){

        return $validator = Validator::make($data, [
            'code' => 'required|string|min:2|max:255',
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
                    $fournisseur = Fournisseur::where('code', $value['code'])->first();

                    if($fournisseur != null){
                        $value['fournisseur_id']=$fournisseur->id;
                    }

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
            'code' => 'required|string|min:2|max:255',
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
