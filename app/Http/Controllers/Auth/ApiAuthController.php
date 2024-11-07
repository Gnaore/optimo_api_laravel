<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserDetailsResource;
use App\Http\Resources\UserResource;
use App\Notifications\EmailVerification;
use App\Notifications\PasswordResetVerification;
use App\Notifications\ProfileValidNotification;
use App\Notifications\UserProfileLockNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Laravel\Passport\Token;
use App\Models\Compagny;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class ApiAuthController extends Controller
{

    public function __construct()
    {
        $this->middleware(['role:admin|root','permission:write all|read all'])
        ->only(['changeAccountLockedState', 'destroy', 'giveRolesToUser', 'removeRolesToUser']);
    }

    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'country' => 'nullable|string|max:16',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:6', //ici dans ce projet la personne qui va valider son mot de passe
            'compagnie_id' => 'nullable',
        ]); 

        if ($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
        }

        DB::beginTransaction();

        $password = $request['email'];
        if(isset($request['password'])){
            $password = $request['password'];
        }

        $request['password']= Hash::make($password);
        $request['remember_token'] = Str::random(10);
        $request["verification_state"] = "VALID";
        $request["email_verified_at"] = new \DateTime("now");
        $request["status"] = true;

        if(isset($request["compagnie_id"]) && intval($request["compagnie_id"]) != 0){
            $request["compagnie_id"] = (int)$request["compagnie_id"]; 
        }
        else{
            $compagny = Compagny::first();
            if(isset($compagny)){
                $request['compagnie_id'] =$compagny->id;
            }
        }

        $user = User::create(
            $request->toArray()
        );

        DB::commit();

        $token = $user->createToken($user->email)->accessToken;
        $user->permissions = $user->getAllPermissions();
        $user->accessToken = $token;

        return response((new UserDetailsResource($user)), 200);
    }

    public function inserAdmin(){

        Compagny::updateOrInsert(
            ['sigle' => 'optimo'], // The condition to check
            [
                'name' => "Opt'imo",
                'sigle' => 'optimo',
                'logo' => 'optimo.jpeg',
                'address' => 'adresse',
                'email' => 'optimo@gmail.com',
                'phone' => '0555475465',
                'description' => ''
            ]
        );

        $admin = [
            "firstname" => "Admin",
            "lastname" => "Optimo",
            "phone" => "+2250555475465",
            "email" => "admin@gmail.com",
            "verification_state" => "VALID",
            "compagnie_id"=> 1,
            "status" => true,
            "email_verified_at" => new \DateTime("now"),
            "remember_token" => Str::random(10),
            "country" => "Côte d'Ivoire",
            "password" => Hash::make("admin@gmail.com"),
        ];

        DB::beginTransaction();

        $user = User::create($admin);
        $role = Role::create(['name' => 'admin', "guard_name"=>"web"]);
        $user->syncRoles([$role->name]);

        $admin_permissions = [
            'write all', 'read all', 'edit all', 'delete all', 'update all', 'config notall'
        ];
        foreach ($admin_permissions as $key => $value) {
            Permission::create(['name' => $value, "guard_name"=>"web"]);
        }

        $role->syncPermissions($admin_permissions);
        DB::commit();

        return $user;
    }

    public function inserRoot(){

        Compagny::updateOrInsert(
            ['sigle' => 'optimo'], // The condition to check
            [
                'name' => "Opt'imo",
                'sigle' => 'optimo',
                'logo' => 'optimo.jpeg',
                'address' => 'adresse',
                'email' => 'optimo@gmail.com',
                'phone' => '0555475465',
                'description' => ''
            ]
        );

        $root = [
            "firstname" => "Root",
            "lastname" => "Optimo",
            "phone" => "+2250555475465",
            "email" => "root@gmail.com",
            "verification_state" => "VALID",
            "compagnie_id"=> 1,
            "status" => true,
            "email_verified_at" => new \DateTime("now"),
            "remember_token" => Str::random(10),
            "country" => "Côte d'Ivoire",
            "password" => Hash::make("root@gmail.com"),
        ];

        DB::beginTransaction();
        
        $user = User::create($root);
        $role = Role::create(['name' => 'root', "guard_name"=>"web"]);
        $user->syncRoles([$role->name]);

        DB::commit();

        return $user;
    }

    public function registerAdmin () {

        return $this->inserAdmin();
    }


    public function registerRoot () {

        return $this->inserRoot();
    }

    public function login (Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
        }

        $user = User::where('email', $request['email'])->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)
            && $user->account_locked === 'DISABLED' && $user->email_verified_at !== null) {
                $token = $user->createToken($request['email'])->accessToken;
                $user->permissions = $user->getAllPermissions();
                $user->accessToken = $token;

                return response((new UserDetailsResource($user)), 200);
            }elseif ($user->account_locked === 'ENABLED'){
                $response = ["message" => "Compte suspendu veuillez contacter le support pour toute assistance."];
                return response($response, 200);
            }
            elseif ($user->email_verified_at === null){
                $response = ["message" => "Votre compte n'est pas encore activé."];
                return response($response, 403);
            }
            else {
                $response = ["password" => "Votre mot de passe est incorrect."];
                return response($response, 403);
            }
        }

        $response = ["email" =>'Votre compte '.$request['email'].' est introuvable.'];
        return response($response, 403);
    }

    public function changeAccountLockedState(Request $request){

        $user = User::where('id', intval($request->get('id')))->first();

        if ($user !== null && !empty($user)) {

            if ($user->account_locked === 'DISABLED') {
                $user->account_locked = 'ENABLED';
                $user->save();
                $response = [
                    'message' => "Utilisateur bloqué avec succès.",
                    'data' => $user
                ];
                //$user->notify(new UserProfileLockNotification($user->firstname.' '.$user->lastname, $user->account_locked));
                return response($response, 200);
            }elseif ($user->account_locked === 'ENABLED'){
                $user->account_locked = 'DISABLED';
                $user->save();
                //$user->notify(new UserProfileLockNotification($user->firstname.' '.$user->lastname, $user->account_locked));
                $response = [
                    'message' => "Utilisateur débloqué avec succès.",
                    'data' => $user
                ];
                return response($response, 200);
            } else {
                $response = ["message" => "L'utilisateur n'existe pas"];
                return response($response, 422);
            }
        } else {
            $response = ["message" => "L'utilisateur n'existe pas"];
            return response($response, 422);
        }
    }

    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function index(Request $request){

        $compagnie_id = $request->compagnie_id;
        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $email = $request->email;
        $limit = $request->limit;

        $dataModel = User::with(['compagny']);

        if(isset($compagnie_id) && intval($compagnie_id) > 0){
            $dataModel->where('compagnie_id', intval($compagnie_id)); 
        }

        if(isset($firstname)){
            $dataModel->where('firstname', "LIKE","%{$firstname}%"); 
        }

        if(isset($lastname)){
            $dataModel->where('lastname', "LIKE","%{$lastname}%"); 
        }

        if(isset($email)){
            $dataModel->where('email', "LIKE","%{$email}%"); 
        }

        $dataModel->orderBy("id", "desc");

        if(isset($request->per_page) && intval($request->per_page) > 0){
            return $dataModel->paginate(intval($request->per_page)); 
        }

        if(isset($limit) && intval($limit)  > 1){
            $dataModel->limit(intval($limit));
        }

        if(isset($limit) && intval($limit) == 1){

            $user = $dataModel->first();
            $user->roles = $user->getRoleNames();

            return response((new UserDetailsResource($user)), 200);
        }

        return new UserCollection($dataModel->get());
    }

    public function myProfile (Request $request) {

        $user = Auth::user();
        $user = User::with('compagny')->where('email', $user->email)->firstOrFail();
        $user->roles = $user->getRoleNames();

        return response(new UserDetailsResource($user), 200);
    }

    public function show($id) {

        $user = User::with('compagny')->where('id', $id)->firstOrFail();
        $user->roles = $user->getRoleNames();

        return response(new UserDetailsResource($user), 200);
    }

    public function resetPassword(Request $request){

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|max:255|min:8',
            'email' => 'required|string|max:255',
        ]);

        if($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
        }

        DB::beginTransaction();

        $user = User::where('email', $request['email'])->first();

        if($user === null){
            $response = [
                'valid' => 0,
                'errors' => 'Compte introuvable',
                'password' => 'Votre compte est introuvable. Veuillez contacter un administrateur!'
            ];
            return response($response, 400);
        }

        $user->password = Hash::make($request['password']);

        $user->save();

        $user->roles = $user->getRoleNames();

        DB::commit();

        return response($user, 200);
    }

    public function update(Request $request, $id){

        $userId = Auth::user()->id;

        if(!empty($id)){
            $userId  = $id;
        }
        
        $validator = Validator::make($request->all(), [
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'country' => 'nullable|string|max:16',
            'address' => 'nullable|string|max:255',
            'birth_day' => 'nullable|date',
            'city' => 'nullable|string|max:255',
            'gender' => 'nullable|string',
            'avatar' => 'nullable|string',
            'compagnie_id' => 'nullable'
        ]);

        if($validator->fails())
        {
            return response(validationFormatErrors($validator), 403);
        }
        DB::beginTransaction();
        $user = User::where('id', $userId)->first();

        $user->lastname = $request->get('lastname');
        $user->firstname = $request->get('firstname');
        $user->country = $request->get('country');
        $user->address = $request->get('address');
        $user->phone = $request->get('phone');
        $user->whatsapp = $request->get('whatsapp');

        // if(!empty($request->get('password'))){
        //     $user->password = Hash::make($request->get('password'));
        // }

        $user->proffession = $request->get('proffession');

        if(!empty($request->get('lang'))){
            $user->lang = $request->get('lang');
        }

        if(!empty($request->get('compagnie_id')) && (int)$request->get('compagnie_id') > 0){
            $user->compagnie_id = $request->get('compagnie_id');
        }

        if(!empty($request->get('email'))){
            $userfind = User::where('email', $request->get('email'))->first();
            if(!isset($userfind)){
                $user->email = $request->get('email');
            }
        }

        if(!empty($request->get('birth_day'))){
            $user->birth_day = Carbon::parse($request->get('birth_day'));
        }

        $user->avatar = $request->get('avatar');
        $user->gender = $request->get('gender');
        $user->city = $request->get('city');

        $user->save();
        $user->roles = $user->getRoleNames();

        DB::commit();
        return response(new UserDetailsResource($user), 200);
    }

    public function giveRolesToUser(Request $request, $id){

        $user = User::where('id', $id)->firstOrFail();
        $user->assignRole($request->roles);
        $user->roles = $user->getRoleNames();

        return $user;
    }

    public function removeRolesToUser(Request $request, $id){

        $user = User::where('id', $id)->firstOrFail();

        if(isset($request->roles) && is_array($request->roles)){
            foreach ($request->roles as $key => $value) {
                $user->removeRole($value);
            }
        }
        else{
            $user->removeRole($request->roles);
        }

        return 1;
    }

    public function upload(Request $request){

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = $file->getClientOriginalName();
                $file->move(public_path('uploads'), $fileName);

                $response = [
                    "fileName" => $fileName,
                    "path" => "uploads/$fileName",
                    "url" => env("STATIC_URL")."/uploads/$fileName"
                ];
                
                return $response;
            }
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }

    }

    public function destroy($id)
    {
        $user = User::where('id', $id)->firstOrFail();
        $role = $user->hasAnyRole(['root']);

        if(!$role){
            User::where('id', (int)$id)->delete();
            return response(['message' => 'Utilisateur supprimé avec succès'], 200);
        }
        else{
            return response(['message' => 'Impossible de supprimer ce compte'], 404);
        }
        
    }
}
