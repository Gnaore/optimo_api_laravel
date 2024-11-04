<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;
class PermissionController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['role:admin|root','permission:write all|read all']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dataModel = Permission::orderBy('id', 'desc');

        if(isset($request->per_page) && intval($request->per_page) > 0){
            $dataModel->paginate(intval($request->per_page)); 
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
            'name' => 'required|string|min:2|max:255|unique:permissions'
        ]); 

        if ($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
        }

        return Permission::create(['name' => $request->name]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Permission::find($id);
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
            'name' => [
                'required', 'string', 'min:2', 'max:255',
                Rule::unique('permissions')->ignore($id),
            ],
        ]); 

        if ($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
        }

        return Permission::where('id', $id)->update(['name' => $request->name]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Permission::destroy($id);
    }
}
