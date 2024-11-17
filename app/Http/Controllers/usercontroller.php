<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\User;
use App\expert;
use App\consultation;
use App\meeting;
use Carbon\Carbon;



class usercontroller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }


    public  function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required|string',
            'password'=>'required|string|min:6',
            'email'=>'required|email|string|unique:App\User',
            'image'=>'max:2048|mimes:jpg,png,jpeg,gif,svg',

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

        $user = new User();
        $user->name = $request->name;

        $user->password = bcrypt($request->password);
        $user->email = $request->email;
        $user->image = $filename;
        $user->account = rand(10,1000);
        $user->save();


        return response()->json([
            'message'=>'user register succussfuly',
            'user'=>$user

        ], 402);


    }
    public  function login(Request $request){

        $validator = Validator::make($request->all(),[
            'password'=>'required|string|min:6',
            'email'=>'required|email|string'


        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);

        }




        if (!$token=auth()->attempt($validator->validated()) and !$token=auth()->guard('expert_api')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if($token=auth()->attempt($validator->validated())){

            return $this->createnewtoken1($token);

        }
        if($token=auth()->guard('expert_api')->attempt($validator->validated())){

            return $this->createnewtoken2($token);

        }



    }

    protected function createnewtoken1($token)
    {
        return response()->json([
            'access_token' => $token,
            'type'=>1,
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
    protected function createnewtoken2($token)
    {
        return response()->json([
            'access_token' => $token,
            'type'=>2,
            'expires_in' => auth()->factory()->getTTL() * 60,
            'expert' => auth()->guard('expert_api')->user()

        ]);
    }

    public function profile(){

            return response()->json(auth()->user());

    }

    public function logout(){
        auth()->logout();
        return response()->json([
            'message '=> 'user logout succussfuly'

        ]);

}
public function typeexpert(Request $request){

    $experts = expert::where('specialization', $request->type)->get();
    return response()->json([
        'experts'=>$experts,

    ]);


}

public function getexpert(Request $request){

    $experts = expert::where('id', $request->id)->first();
    return response()->json([
        'expert'=>$experts,

    ]);


}
public function consult(Request $request){
    $validator = Validator::make($request->all(),[
        'consult'=>'required|string|max:500',


    ]);
    if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 200);

    }
    $consult = new consultation();
    $experts = expert::where('id', $request->id)->first();
    $consult->price =$experts->consult_price;
    $user = auth()->user();

    if($user->account < $experts->consult_price){

        return response()->json([
           'error'=> 'your account less than price consult'

        ]);

    }

    $consult->question = $request->consult;
    $consult->expert_id = $request->id;
    $consult->user_id = auth()->user()->id;
    $consult->answer = "not yet";
    $consult->user_state = 0;
    $consult->expert_state = 0;

    $consult->save();
    return response()->json([
        'consult'=>$consult,

    ]);



}

///////////////////////////////////////////////////////////////////////////////////////
public function deleteconsult(Request $request){

    $consult = consultation::where('id', $request->id)->first();
    if($consult->answer == 'not yet'){
        $consult->user_state = 1 ;
        $consult->expert_state = 1 ;
        $consult->save();
    return response()->json([
        'message'=>'consult delete succussfuly'
    ]);
    }
    $consult->user_state = 1;
    $consult->save();
    return response()->json([
        'message'=>'consult delete from user succussfuly'
    ]);



}

public function allconsult(Request $request){
    $all = consultation::where('user_id' , auth()->user()->id )->where('user_state' , 0)->get();

    return response()->json([
        'my consult'=> $all

    ]);


    }
    public function getconsult(Request $request){

        $consult = consultation::where('id', $request->id)->first();
        return response()->json([
            'consult'=>$consult,

        ]);


    }

    public function reservdate(Request $request){
        $validator = Validator::make($request->all(),[
            'date'=>'required',


        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 200);

        }


$date1 = $request->date;
// $date1 = Carbon::createFromFormat('Y-m-d H:i:s',  $date)->format('Y-m-d H:i');
$year = Carbon::createFromFormat('Y-m-d H:i:s', $date1)->format('Y');
$month = Carbon::createFromFormat('Y-m-d H:i:s', $date1)->format('m');
$day = Carbon::createFromFormat('Y-m-d H:i:s', $date1)->format('d');
$hour = Carbon::createFromFormat('Y-m-d H:i:s', $date1)->format('H');

$date2 = now();

// $date2 = Carbon::createFromFormat('Y-m-d H:i:s',  $time)->format('Y-m-d H:i');
$year1 = Carbon::createFromFormat('Y-m-d H:i:s',  $date2)->format('Y');
$month1 = Carbon::createFromFormat('Y-m-d H:i:s', $date2)->format('m');
$day1 = Carbon::createFromFormat('Y-m-d H:i:s',   $date2)->format('d');
$hour1 = Carbon::createFromFormat('Y-m-d H:i:s',  $date2)->format('H');
if($year < $year1 || ($year1 == $year && $month < $month1 ) ||
($year1 == $year && $month == $month1 &&  $day < $day1) ||
($year1 == $year && $month == $month1 &&  $day == $day1 && $hour <  $hour1  ) ||
  ($hour>21 || $hour<8)){

return response()->json(['error'=> 'not correct date or time']);

}
$dateing = now();
$alldate = meeting::where('expert_id' ,$request->id)->get();
foreach($alldate as $meet){
    $dateing = $meet->date;
    $year3 = Carbon::createFromFormat('Y-m-d H:i:s',  $dateing)->format('Y');
    $month3 = Carbon::createFromFormat('Y-m-d H:i:s', $dateing)->format('m');
    $day3 = Carbon::createFromFormat('Y-m-d H:i:s',   $dateing)->format('d');
    $hour3 = Carbon::createFromFormat('Y-m-d H:i:s',  $dateing)->format('H');
    if(($year == $year3) && ($month ==  $month3) && ($day3 == $day) && ($hour == $hour3))  {

        return response()->json(['error'=> 'this date is reserved before'],402);
    }
}




$meet = new meeting();
$meet->date = $request->date;
$meet->user_id = auth()->user()->id;
$meet->expert_id = $request->id;
$meet->expert_state = 0;
$meet->user_state = 0;
$meet->state = 0;
$meet->save();
return response()->json([

    'meeting'=>$meet
]);


    }

////////////////////////////////////////////////////////////////////////////////
    public function deletedate(Request $request){


         $date = meeting::where('id', $request->id)->first();
         if($date->state == 0){
            $date->user_state = 1 ;
            $date->expert_state = 1 ;
            $date->save();
    return response()->json([
        'message'=>'date delete succussfuly'

    ]);
         }
         $date->user_state = 1;
         $date->save();
         return response()->json([
            'message'=>'date delete from user succussfuly'
        ]);

    }
    public function alldate(Request $request){
        $alldate = meeting::where('user_id' , auth()->user()->id)->where('user_state' , 0)->get();

        return response()->json([
            'all date'=> $alldate

        ]);


        }


}


