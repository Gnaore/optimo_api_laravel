<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Acquisition as DataModel;
use App\Models\Nature;
use App\Models\CodeInventaire;
use App\Models\BonCommande;

use Illuminate\Support\Facades\Validator;

class AcquisitionController extends Controller
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
        $nature_id = $request->nature_id;
        $code_inventaire_id = $request->code_inventaire_id;
        $bon_commande_id = $request->bon_commande_id;
        $valeur_acquisition = $request->valeur_acquisition;

        $bon_commande_code = $request->bon_commande_code;
        $nature = $request->nature;
        $code_inventaire = $request->code_inventaire;
        $libelle = $request->libelle;

        $total_acquisition = $request->total_acquisition;
        $from_at = $request->from_at;
        $to_at = $request->to_at;
        $limit = intval($request->limit);

        $date_acquisition_from = $request->date_acquisition_from;
        $date_acquisition_to = $request->date_acquisition_to; 

        $date_mise_service_from = $request->date_mise_service_from_at;
        $date_mise_service_to = $request->date_mise_service_from_at;

        $date_cession_from = $request->date_cession_from;
        $date_cession_to = $request->date_cession_to;

        $parent_id = $request->parent_id;

        $dataModel = DataModel::with(['nature', 'code_inventaire', 'bon_commande']);

        if(isset($nature_id) && intval($nature_id) > 0){
            $dataModel->where('nature_id', intval($nature_id)); 
        }

        if(isset($parent_id) && intval($parent_id) > 0){
            $dataModel->where('parent_id', intval($parent_id))->orWhere('id', intval($parent_id)); 
        }

        if(!isset($parent_id)){
            $dataModel->where('parent_id', NULL);
        }

        if(isset($code_inventaire_id) && intval($code_inventaire_id) > 0){
            $dataModel->where('code_inventaire_id', intval($code_inventaire_id)); 
        }

        if(isset($bon_commande_id) && intval($bon_commande_id) > 0){
            $dataModel->where('bon_commande_id', intval($bon_commande_id)); 
        }

        if(isset($valeur_acquisition)){
            $dataModel->where('valeur_acquisition', $valeur_acquisition); 
        }
        if(isset($libelle)){
            $dataModel->where('libelle', $libelle); 
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

        //date acquisition 
        if(strlen($date_acquisition_from)>20 &&  20>strlen($date_acquisition_to)){
            $dateTime = new \DateTime($date_acquisition_from);
            $day = $dateTime->format('Y-m-d');
            $dataModel->whereBetween('date_acquisition', [$day."T00:00:00.000000Z", $day."T23:59:59.000000Z"]);
        }
    
        if(strlen($date_acquisition_to)>20 &&  20>strlen($date_acquisition_from)){
            $dateTime = new \DateTime($date_acquisition_to);
            $day = $dateTime->format('Y-m-d');
            $dataModel->whereBetween('date_acquisition', [$day."T00:00:00.000000Z", $day."T23:59:59.000000Z"]);
        }
    
        if(strlen($date_acquisition_from)>20 && strlen($date_acquisition_to)>20){
            $dataModel->whereBetween('date_acquisition', ["$date_acquisition_from", $date_acquisition_to]);
        }
        //end

        //date mise en service 
        if(strlen($date_mise_service_from)>20 &&  20>strlen($date_mise_service_to)){
            $dateTime = new \DateTime($date_mise_service_to);
            $day = $dateTime->format('Y-m-d');
            $dataModel->whereBetween('date_mise_service', [$day."T00:00:00.000000Z", $day."T23:59:59.000000Z"]);
        }
    
        if(strlen($date_mise_service_to)>20 &&  20>strlen($date_mise_service_from)){
            $dateTime = new \DateTime($date_mise_service_to);
            $day = $dateTime->format('Y-m-d');
            $dataModel->whereBetween('date_mise_service', [$day."T00:00:00.000000Z", $day."T23:59:59.000000Z"]);
        }
    
        if(strlen($date_mise_service_from)>20 && strlen($date_mise_service_to)>20){
            $dataModel->whereBetween('date_mise_service', ["$date_mise_service_from", $date_mise_service_to]);
        }
        //end

        //date acquisition 
        if(strlen($date_cession_from)>20 &&  20>strlen($date_cession_to)){
            $dateTime = new \DateTime($date_cession_from);
            $day = $dateTime->format('Y-m-d');
            $dataModel->whereBetween('date_cession', [$day."T00:00:00.000000Z", $day."T23:59:59.000000Z"]);
        }
    
        if(strlen($date_cession_to)>20 &&  20>strlen($date_cession_from)){
            $dateTime = new \DateTime($date_cession_to);
            $day = $dateTime->format('Y-m-d');
            $dataModel->whereBetween('date_cession', [$day."T00:00:00.000000Z", $day."T23:59:59.000000Z"]);
        }
    
        if(strlen($date_cession_from)>20 && strlen($date_cession_to)>20){
            $dataModel->whereBetween('date_cession', ["$date_cession_from", $date_cession_to]);
        }
        //end
    
        $dataModel->orderBy("id", "desc");

        if(isset($total_acquisition) && (boolval($total_acquisition) == true || boolval($total_acquisition) == 1)){
            
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


        if(isset($bon_commande_code)){

            $bon_commande = BonCommande::where('code', $bon_commande_code)->first();
            if($bon_commande != null){
                $dataModel->where('bon_commande_id', $bon_commande->id); 
            }
        }

        if(isset($nature)){

            $natureData = Nature::where('libelle', $nature)->first();
            if($natureData != null){
                $dataModel->where('nature_id', $natureData->id); 
            }
        }
        
        return $dataModel->get();
    }


    public function validation($data){

        return $validator = Validator::make($data, [
            'bon_commande_id' => 'nullable|int',
            'code_inventaire_id' => 'nullable|int',
            'nature_id' => 'nullable|int',
            'date_acquisition' => 'required|string|max:255',
            'valeur_acquisition' => 'required|string|max:255'
        ]);
    }

    public function importValidation($data){

        return $validator = Validator::make($data, [
            'bon_commande_id' => 'nullable|int',
            'code_inventaire_id' => 'nullable|int',
            'nature_id' => 'nullable|int',
            'date_acquisition' => 'required|string|max:255',
            'valeur_acquisition' => 'required|string|max:255'
        ]);
    }

    public function findAquictisionParentByItems($libelle, $bon_commande_id, $nature_id){

        $parent_id = null;

        $parent = DataModel::where('bon_commande_id', $bon_commande_id)
                            ->first();

        if($parent != null){
            $parent_id = $parent->id;
        }

        return $parent_id;
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
        $bon_commande_id = NULL;
        $nature_id = NULL;

        if(isset($array) && is_array($array) && !empty($array)){
            
            foreach ($array as $key => $value) {

                $validator = $this->importValidation($value);

                if (!$validator->fails())
                {

                    if(isset($value['nature_bien'])){
                        $nature = Nature::where('libelle', $value['nature_bien'])->first();

                        if($nature != null){
                            $value['nature_id']=$nature->id;
                            $nature_id=$nature->id;
                        }
                    }

                    if(isset($value['numero_bc'])){
                        $bon_commande = BonCommande::where('code', $value['numero_bc'])->first();

                        if($bon_commande != null){
                            $value['bon_commande_id']=$bon_commande->id;
                            $bon_commande_id=$bon_commande->id;
                        }
                        else{
                            $data = BonCommande::create([
                                "code"=> $value['numero_bc'],
                                "libelle"=> $value['numero_bc'],
                                "montant"=> 01
                            ]);
                            $bon_commande_id=$data->id;
                            $value['bon_commande_id']=$bon_commande_id;
                        }
                    }else{
                        $bon_commande = BonCommande::first();
                        $numero_bc = '012345BC';
                        if($bon_commande != null){
                            $numero_bc = "0".$bon_commande->code;
                        }
                        $data = BonCommande::create([
                            "code"=> $numero_bc,
                            "libelle"=> $numero_bc,
                            "montant"=> 02
                        ]);
                        $bon_commande_id=$data->id;
                        $value['bon_commande_id']=$bon_commande_id;
                    }

                    if(isset($value['code_inventaire'])){
                        $code_inventaire = CodeInventaire::where('code', $value['code_inventaire'])->first();

                        if($code_inventaire != null){
                            $value['code_inventaire_id']=$code_inventaire->id;
                        }
                    }

                    $parent_id = $this->findAquictisionParentByItems($value['libelle'], $bon_commande_id, $nature_id);

                    if($parent_id != null){
                        $value['parent_id'] = $parent_id;
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

        $body = $request->all();
        $parent_id = $this->findAquictisionParentByItems($body['libelle'], $body['bon_commande_id'], $body['nature_id']);

        if($parent_id != null){
            $body['parent_id'] = $parent_id;
        }

        $parent = DataModel::create($body);

        if(isset($body['quantity']) && $parent != null){

            $quantity = intval($body['quantity']);
            $parent_id = $this->findAquictisionParentByItems($parent['libelle'], $parent['bon_commande_id'], $parent['nature_id']);
            
            $acquisition_array = [];

            for ($i=1; $i <= $quantity; $i++) {

                $body['parent_id'] =  $parent_id;
                $body['updated_at'] = $parent['updated_at'];
                $body['created_at'] = $parent['created_at'];
                $newBody = $body;
                unset($newBody["quantity"]);
                $acquisition_array[] = $newBody;
            }

            DataModel::insert($acquisition_array);
        }

        return $parent;
    }

    public function verifyCodeInventaire(Request $request){

        $immosWithCodeInventaire = [];
        $immosWithoutCodeInventaire = [];
        $status = intval($request->status);
        $array = $request->all();

        if(isset($array) && is_array($array) && !empty($array)){
            
            foreach ($array as $key => $value) {

                $CodeInventaire = CodeInventaire::where('code', $value['code_inventaire'])->first();

                if($CodeInventaire != null){
                    $immosWithCodeInventaire[] = $value;
                }
                else{
                    $immosWithoutCodeInventaire[] = $value;
                }
            }

            if($status == 1 || $status == true){
                return $immosWithCodeInventaire;
            }

            return $immosWithoutCodeInventaire;
        }
        
        return $immosWithoutCodeInventaire;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return DataModel::with(['nature', 'code_inventaire', 'bon_commande'])->find($id);
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
            'bon_commande_id' => 'nullable|int',
            'code_inventaire_id' => 'nullable|int',
            'nature_id' => 'nullable|int',
            'date_acquisition' => 'required|string|max:255',
            'valeur_acquisition' => 'required|string|max:255'
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
