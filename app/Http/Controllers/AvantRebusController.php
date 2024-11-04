<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AvantRebus as DataModel;
use App\Models\Immobilisation;

use Illuminate\Support\Facades\Validator;

class AvantRebusController extends Controller
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
        $user_id = $request->user_id;
        $immobilisation_id = $request->immobilisation_id;
        $total_avant_rebus = $request->total_avant_rebus;
        $from_at = $request->from_at;
        $to_at = $request->to_at;
        $limit = intval($request->limit);

        $dataModel = DataModel::with(['immobilisation' => function ($query) {
            $query->withTrashed();
        }, 'user']);

        if(isset($immobilisation_id) && intval($immobilisation_id) > 0){
            $dataModel->where('immobilisation_id', intval($immobilisation_id)); 
        }

        if(isset($user_id) && intval($user_id) > 0){
            $dataModel->where('user_id', intval($user_id)); 
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

        if(isset($total_avant_rebus) && (boolval($total_avant_rebus) == true || boolval($total_avant_rebus) == 1)){
            
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'immobilisation_id' => 'required|int',
            'motif' => 'required|string|max:255',
            'date' => 'required|string',
        ]);

        if ($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
        }

        $response = DataModel::create($request->all());

        //Immobilisation::destroy(intval($request->immobilisation_id));

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
        return DataModel::with(['immobilisation' => function ($query) {
            $query->withTrashed();
        }, 'user'])->find($id);
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
        $response = DataModel::find($id);
        Immobilisation::where('id', $response->immobilisation_id)->update(
            ['deleted_at' => NULL]
        );

        return DataModel::destroy($id);
    }
}
