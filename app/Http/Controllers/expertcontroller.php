<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\User;
use App\expert;
use App\consultation;
use App\meeting;


class expertcontroller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:expert_api', ['except' => ['register']]);
    }


    public  function expertregister(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required|string',
            'password'=>'required|string|min:6',
            'email'=>'required|email|string|unique:App\expert',
            'image'=>'max:2048|mimes:jpg,png,jpeg,gif,svg',
            'specialization'=>'required|string',
            'consult_price'=>'required',




        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 200);

        }
        // $user = User::create(array_merge(
        //     $validator->validated(),
        //     ['password'=>bcrypt($request->password)]


        // ));

        $filename="";
        if($request->hasFile('image')){
            $filename=$request->file('image')->store('images','public');

        }else{
            $filename='images/image1.jpg';
        }

        $expert = new expert();
        $expert->name = $request->name;
        $expert->specialization = $request->specialization;
        $expert->consult_price = $request->consult_price;

        $expert->password = bcrypt($request->password);
        $expert->email = $request->email;
        $expert->image = $filename;
        $expert->account = rand(10,1000);
        $expert->save();


        return response()->json([
            'message'=>'expert register succussfuly',
            'expert'=>$expert

        ], 200);


    }

    public function expertprofile(){

            return response()->json(auth()->guard('expert_api')->user());

    }

    public function expertlogout(){
        auth()->guard('expert_api')->logout();
        return response()->json([
            'message '=> 'expert logout succussfuly'

        ]);

}

public function myconsult(Request $request){

    $myconsult = consultation::where('expert_id' , auth()->guard('expert_api')->user()->id )->where('expert_state' , 0)->get();

    return response()->json([
        'my consult'=> $myconsult

    ]);


    }
    public function getconsult(Request $request){

        $consult = consultation::where('id', $request->id)->first();
        return response()->json([
            'consult'=>$consult,

        ]);


    }


    public function deleteconsult2(Request $request){

        $consult = consultation::where('id', $request->id)->first();
        if($consult->answer = 'not yet'){

            $consult->answer = 'expert dont have any answer to this quastion';

        }

        $consult->expert_state = 1;
        $consult->save();
        return response()->json([
            'message'=>'consult delete from expert succussfuly'
        ]);



    }

    public function deletedate2(Request $request){


        $date = meeting::where('id', $request->id)->first();

        $date->expert_state = 1;
        $date->state = 2 ;
        $date->save();
        return response()->json([
           'message'=>'date delete from user succussfuly'
       ]);

   }

    public function mydate(Request $request){

        $mydate = meeting::where('expert_id' , auth()->guard('expert_api')->user()->id )->where('expert_state' , 0)->get();

        return response()->json([
            'my date'=> $mydate

        ]);


        }

        public function getdate(Request $request){

            $date = meeting::where('id', $request->id)->first();
            return response()->json([
                'date'=>$date,

            ]);


        }
        public function answer(Request $request){

            $validator = Validator::make($request->all(),[

                'answer'=> 'required'

            ]);


            if($validator->fails()){
                return response()->json($validator->errors()->tojson(),402);
            }

            $consult = consultation::where('id', $request->id)->first();
            if($consult->answer != 'not yet'){

                return response()->json([
                    'error'=>'you are answer this quastion alredy '

                ]);

            }

            $consult->answer = $request->answer;
           // $consult->save();

            $price = $consult->price;
            $user = User::where('id',$consult->user_id)->first();
            if($user->account < $price){

                return response()->json([
                    'alert'=>'user how ask dont have enough mony now pleas try in another time'

                ]);
            }
            $user->account = $user->account - $price;
           $expert_account =  auth()->guard('expert_api')->user()->account ;
           $expert_account = $expert_account + $price ;
           auth()->guard('expert_api')->user()->account = $expert_account;

           $consult->save();
           auth()->guard('expert_api')->user()->save();
           $user->save();

           return response()->json([
            'message' =>'expert answer succassfuly'

        ]);





        }
        public function answerdate(Request $request){
            $date = meeting::where('id', $request->id)->first();
            $date->state = $request->state;
            $date->save();
            return response()->json([
                'meeting' => $date,

            ]);




        }


}




