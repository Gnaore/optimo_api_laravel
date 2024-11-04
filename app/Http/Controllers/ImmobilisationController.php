<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Immobilisation as DataModel;
use App\Models\Localisation;
use App\Models\CodeInventaire;
use App\Models\SousFamille;

use Illuminate\Support\Facades\Validator;

class ImmobilisationController extends Controller
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
        $code_inventaire_id = $request->code_inventaire_id;
        $client_id = $request->client_id;
        $sous_famille_id = $request->sous_familledatetime_id;
        $reference = $request->reference;
        $code_inventaire = $request->code_inventaire;
        
        $total_immobilisation = $request->total_immobilisation;
        $from_at = $request->from_at;
        $to_at = $request->to_at;
        $limit = intval($request->limit);

        $dataModel = DataModel::with(['localisation', 'code_inventaire', 'sous_famille', 'client']);
        //'acquisition', 'avant_rebus', 'au_rebus', 'transferts'
        if(isset($localisation_id) && intval($localisation_id) > 0){
            $dataModel->where('localisation_id', intval($localisation_id)); 
        }
        if(isset($code_inventaire_id) && intval($code_inventaire_id) > 0){
            $dataModel->where('code_inventaire_id', intval($code_inventaire_id)); 
        }

        if(isset($code_inventaire_id) && intval($code_inventaire_id) > 0){
            $dataModel->where('code_inventaire_id', intval($code_inventaire_id)); 
        }

        if(isset($client_id) && intval($client_id) > 0){
            $dataModel->where('client_id', intval($client_id)); 
        }
        if(isset($sous_famille_id) && intval($sous_famille_id) > 0){
            $dataModel->where('sous_famille_id', intval($sous_famille_id)); 
        }

        if(isset($reference)){
            $dataModel->where('reference', $reference);
        }

        if(strlen($from_at)>20 &&  20>strlen($to_at)){
            $dateTime = new \DateTime($from_at);
            $day = $dateTime->format('Y-m-d');
            $dataModel->whereBetween('created_at', [$day."T00:00:00.000000Z", $day."T23:59:59.000000Z"]);
        }
    
        if(strlen($to_at)>20 &&  20>strlen($from_at)){
            $dateTime = new \DateTime($to_at);
            $day = $dateTime->format('Y-m-d');
            $dataModel->whereBetween('created_at', [$day."T00:00:00.000000Z", $day."T23:59:59.000000Z"]);
        }
    
        if(strlen($from_at)>20 && strlen($to_at)>20){
            $dataModel->whereBetween('created_at', ["$from_at", $to_at]);
        }
    
        $dataModel->orderBy("id", "desc");

        if(isset($total_immobilisation) && (boolval($total_immobilisation) == true || boolval($total_immobilisation) == 1)){
            
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

        if(isset($code_inventaire)){

            $immo = null;

            $code = CodeInventaire::where('code', $code_inventaire)->first();

            if(isset($code)){
                $immo = $dataModel->where('code_inventaire_id', intval($code->id))->first();
            }

            return $immo;
        }

        if(isset($request->localisation_code)){

            $localisation = Localisation::where('code', $request->localisation_code)->first();
            if($localisation != null){
                $dataModel->where('localisation_id', $localisation->id); 
            }
        }
        
        return $dataModel->get();
    }

    public function validation($data){

        return $validator = Validator::make($data, [
            'localisation_id' => 'required|int',
            'code_inventaire_id' => 'required|int',
            'reference' => 'nullable|string|max:255',
            'date_enregistrement' => 'required|string|max:255'
        ]);
    }

    public function importValidation($data){

        return $validator = Validator::make($data, [
            'localisation_code' => 'required|string',
            'code_inventaire' => 'required|string',
            'reference' => 'nullable|string|max:255',
            'date_enregistrement' => 'nullable|string|max:255'
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
                    $localisation = Localisation::where('code', $value['localisation_code'])->first();
                    $code_inventaire = CodeInventaire::where('code', $value['code_inventaire'])->first();

                    if($localisation != null && $code_inventaire != null){
                        $value['localisation_id']=$localisation->id;
                        $value['code_inventaire_id']=$code_inventaire->id;
                        $valid_array[] = $value;
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

        return DataModel::create($request->all());
    }

    public function verifyLocalisation(Request $request){

        $immosWithLocalisation = [];
        $immosWithoutLocalisation = [];
        $status = intval($request->status);
        $array = $request->all();

        if(isset($array) && is_array($array) && !empty($array)){
            
            foreach ($array as $key => $value) {

                $localisation = Localisation::where('code', $value['localisation_code'])->first();

                if($localisation != null){
                    $immosWithLocalisation[] = $value;
                }
                else{
                    $immosWithoutLocalisation[] = $value;
                }
            }

            if($status == 1 || $status == true){
                return $immosWithLocalisation;
            }

            return $immosWithoutLocalisation;
        }
        
        return $immosWithoutLocalisation;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return DataModel::with(['localisation', 'code_inventaire'])->find($id);
        //, 'sous_famille', 'client', 'acquisition', 'avant_rebus', 'au_rebus', 'transferts'
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
            'localisation_id' => 'required|int',
            'code_inventaire_id' => 'required|int',
            'reference' => 'required|string|max:255',
            'date_enregistrement' => 'required|string|max:255'
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
