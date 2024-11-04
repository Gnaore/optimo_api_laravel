<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
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
        $dataModel = Role::orderBy('id', 'desc');

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
            'name' => 'required|string|min:2|max:255|unique:roles'
        ]); 

        if ($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
        }

        return Role::create(['name' => $request->name]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);

        $role->permissions = $role->permissions;

        return $role;
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
                Rule::unique('roles')->ignore($id),
            ],
        ]); 

        if ($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
        }

        return Role::where('id', $id)->update(['name' => $request->name]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Role::destroy($id);
    }

    public function givePermissionsToRole(Request $request, $id){

        $role = Role::where('id', $id)->orWhere('name', $id)->firstOrFail();

        if(isset($request->permissions) && is_array($request->permissions)){
            foreach ($request->permissions as $key => $value) {
                $role->givePermissionTo($value);
            }
        }
        else{
            $role->givePermissionTo($request->permissions);
        }

        return 1;
    }

    public function removePermissionsToRole(Request $request, $id){

        $role = Role::where('id', $id)->orWhere('name', $id)->firstOrFail();

        if(isset($request->permissions) && is_array($request->permissions)){
            foreach ($request->permissions as $key => $value) {
                $role->revokePermissionTo($value);
            }
        }
        else{
            $role->revokePermissionTo($request->permissions);
        }

        return 1;
    }
}
