<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Mail;
use App\Mail\PasswordReset;

use DB;

class APIAuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        //


        //return 'prudent';
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


     public function login(Request $request){

       $input = $request->only('email', 'password');
       $jwt_token = null;

       if (!$jwt_token = JWTAuth::attempt($input)) {
           return response()->json([
               'error'=>401,
               'success' => false,
               'message' => 'Invalid Email or Password',
           ], 401);
       }

       // get the user
       $user = Auth::user();

       return response()->json([
           'success' => true,
           'token' => $jwt_token,
           'user' => $user

       ]);
     }




     public function cdata(Request $request){

       $fullname = $request->name;
       $emailx = $request->email;
       $password = $request->password;

       $insertArray = [
         'name'=>$fullname,
         'email'=>$emailx,
         'password'=>$password
       ];

       //check if user already registered

       $countUser = DB::table('users')->where('email','=', $emailx)->count();

       $randx =  str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');

       $locationID = substr($randx,0, 8);

       if($countUser >=1){
        return response()->json([
            'error'=>300,
            'success'=> false,
            'message'=> 'User already registered, Please try later',
        ], 300);
       }
       else{
       $insertData = DB::table('users')->insert($insertArray);

        if($insertData = true){

           // return $this->login($request);

            return response()->json([
            'success'=> true,
            'message'=>'Data Submitted Successfully'
            ], 200);
        }
        else{

            return response()->json([
            'message'=>'Error can\'t register the user',
            'success'=> false,
            ], 422);
        }
        }
     }



     public function register(Request $request){

        $plainPassword = $request->password;

        $password = bcrypt($request->password);

        // $fistname = $request->firstname;
        // $lastname = $request->lastname;


        //check it the user has register before

       
        $emailx = $request->email;

        $checkDB = DB::table('users')
                    ->where('email', $emailx)
                    ->get();
        if(count($checkDB) >= 1){

            return response()->json([
                'error'=>401,
                'success'=>false,
                'message'=>'You have registered this account before.. please recover you login ID'
            ]);
        }
        
        else{

            $request->request->add(['password' => $password]);
            // create the user account
            $created = User::create($request->all());
            $request->request->add(['password' => $plainPassword]);
            
            // login now..
            return $this->login($request);

        }


     }

     public function complete_profile(Request $request){

        $dob =  str_replace('00:00:00.000','',$request->dob);
        $sex = $request->sex;
        $marital = $request->marital;
        $occupation = $request->occupation;
        $userID = $request->userID;

        //check if the user exist
        $getUser = DB::table('users')->where('user_uid', $userID)->get();

        if(count($getUser) >=1){

            $updateData = [
                'gender'=>$sex,
                'marital'=>$marital,
                'dob'=>$dob,
                'occupation'=>$occupation,
                'user_uid'=>$userID,
                'status'=>1
            ];

            $updateDB = DB::table('users')->where('user_uid', $userID)->update($updateData);

            if($updateDB){

                return response()->json([
                    'success'=>true,
                    'message'=> 'Data updated successfully'
                ], 200);
            }

            else{
                return response()->json([
                    'success'=> false,
                    'message'=> 'Unable to update database'
                ], 400);
            }
        }

        else{

            return response()->json([
                'success'=>false,
                'message'=>'Data not found'
            ]);
        }

     }

     public function logout(Request $request){

       if(!User::checkToken($request)){
            return response()->json([
             'message' => 'Token is required',
             'success' => false,
            ],422);
        }

        try {
            JWTAuth::invalidate(JWTAuth::parseToken($request->token));
            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], 500);
        }

     }



     public function getCurrentUser(Request $request){


      if(!User::checkToken($request)){
          return response()->json([
           'message' => 'Token is required'
          ],422);
      }

       $user = JWTAuth::parseToken()->authenticate();
      
       // add isProfileUpdated....

      $isProfileUpdated=false;
       if($user->isPicUpdated==1 && $user->isEmailUpdated){
           $isProfileUpdated=true;
       }
       $user->isProfileUpdated=$isProfileUpdated;
       return $user;
}








    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //



        $user=$this->getCurrentUser($request);

        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'User is not found'
            ]);
        }

        unset($request['token']);

        $data = [
            'name'=> $request['name'],
            'email'=>$request['email'],
        ];


    $updatedUser = User::where('id', $user->id)->update($data);
    $user =  User::find($user->id);
    return response()->json([
        'success' => true,
        'message' => 'Information has been updated successfully!',
        'user' =>$user
    ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
