<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Http\Controllers\APIAuthController;


class ShapeController extends Controller{

    public function __construct(){
        
    }


    public function myshape(Request $request){

        $this->validate($request, [
            'shape'=>'required',
        ]);

        try{


            $inputShape = $request['shape'];

            if($request['shape'] == 'square'){

                //side_a * side_a

                $result =  $request['dimensions']['length_a'] * $request['dimensions']['length_a'];
                return round($result, 2);

                //insert into database

                //DB::table('shape_calculation')->insert();

                //$user = $this->getCurrentUser($request['token']);

                //return($user);



            }

            if($request['shape'] == 'rectangle'){

                $result = $request['dimensions']['length_a'] * $request['dimensions']['length_b'];



                return round($result);
            }

            if($request['shape'] == 'triangle'){

                $side_a = $request['dimensions']['length_a'];
                $side_b = $request['dimensions']['length_b'];
                $side_c = $request['dimensions']['length_c'];

                $triangleSide = ($side_a + $side_b + $side_c)/2;
                $getResult =  sqrt($triangleSide*($triangleSide - $side_a)*($triangleSide - $side_b)*($triangleSide - $side_c));

                return  round($getResult);
            }

            if($request['shape'] == 'circle'){

                $radius = $request['radius'];
                $result = round(pi()*pow($radius, 2), 2);

                return $result;

            }

            return 'Thanks';
        }


        catch(\Exception $e){

            return response()->json([
                'status'=>false,
                'message'=> $e
            ]);
        }


        
       
    }








}
