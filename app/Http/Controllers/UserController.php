<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;

use Exception;
use App\Models\User;
use Inertia\Inertia;
use App\Mail\OTPMail;
use App\Models\Invoice;
use App\Models\Product;
use App\Helper\JWTToken;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    //

    public function LoginPage(Request $request){
        return Inertia::render('LoginPage');
    }//end method

    public function RegistrationPage(Request $request){
        return Inertia::render('RegistrationPage');
    }//end method

    public function SendOTPPage(Request $request){
        return Inertia::render('SendOTPPage');
    }//end method

    public function VerifyOTPPage(Request $request){
        return Inertia::render('VerifyOTPPage');
    }//end method

    public function ResetPasswordPage(Request $request){
        return Inertia::render('ResetPasswordPage');
    }//end method

    public function UserRegistration(Request $request){
        try{
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'phone' => 'required',
            ]);

            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'phone' => $request->input('phone'),
            ]);

            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'User created successfully',
            //     'data' => $user
            // ]);

            $data = ['message'=>'User created successfully','status'=>true,'error'=>''];
            return redirect('/login')->with($data);
        }catch(Exception $e){
            // return response()->json([
            //     'status' => 'fail',
            //     'message' => $e->getMessage()
            // ]);
            $data = ['message'=>'Something went wrong','status'=>false,'error'=>''];
            return redirect('/registration')->with($data);
        }
    }//end method

    public function UserLogin(Request $request)
    {
        // প্রথমে ইমেইল দিয়ে ইউজার খুঁজে বের করা
        $user = User::where('email', $request->input('email'))->first();
    
        // ইউজার আছে কিনা এবং পাসওয়ার্ড সঠিক কিনা চেক করা
        if ($user && Hash::check($request->input('password'), $user->password)) {
            // ইউজারের ID সংরক্ষণ
            $user_id = $user->id;
            $email = $user->email;
    
            // *সেশন সংরক্ষণ*
            $request->session()->put('email', $email);
            $request->session()->put('user_id', $user_id);
    
            // সফল লগইন মেসেজ সহ রিডাইরেক্ট
            return redirect('/DashboardPage')->with([
                'message' => 'User login successfully',
                'status' => true,
                'error' => ''
            ]);
        } else {
            // ব্যর্থ হলে লগইন পেজে ফিরিয়ে দেয়া
            return redirect('/login')->with([
                'message' => 'Login failed',
                'status' => false,
                'error' => ''
            ]);
        }
    }
    public function DashboardPage(Request $request){
        $user_id = request()->header('id');

        $product = Product::where('user_id', $user_id)->count();
        $category = Category::where('user_id', $user_id)->count();
        $customer = Customer::where('user_id', $user_id)->count();
        $invoice = Invoice::where('user_id', $user_id)->count();
        $total = Invoice::where('user_id', $user_id)->sum('total');
        $vat = Invoice::where('user_id', $user_id)->sum('vat');
        $payable = Invoice::where('user_id', $user_id)->sum('payable');
        $discount = Invoice::where('user_id', $user_id)->sum('discount');

        $data = [
            'product' => $product,
            'category' => $category,
            'customer' => $customer,
            'invoice' => $invoice,
            'total' => round($total),
            'vat' => round($vat),
            'payable' => round($payable),
            'discount' => $discount
        ];

        return Inertia::render('DashboardPage',['list'=>$data]);
    }//end method

    public function UserLogout(Request $request){
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'User logout successfully',
        // ],200)->cookie('token', '', -1);

        $request->session()->forget('email');
        $request->session()->forget('user_id');

        $data = ['message'=>'User logout successfully','status'=>true,'error'=>''];
        return redirect('/login')->with($data);
    }//end method

    public function SendOTPCode(Request $request){
        $email = $request->input('email');
        $otp = rand(1000,9999);

        $count = User::where('email',$email)->count();

        if($count == 1){
            // Mail::to($email)->send(new OTPMail($otp));
            User::where('email', $email)->update(['otp' => $otp]);
            $request->session()->put('email', $email);
            // return response()->json([
            //     'status' => 'success',
            //     'message' => "4 Digit {$otp} OTP send successfully",
            // ],200);

            $data = ["message"=>"4 Digit {$otp} OTP send successfully","status"=>true,"error"=>''];
            return redirect('/verify-otp')->with($data);
        }else{
            // return response()->json([
            //     'status' => 'fail',
            //     'message' => 'unauthorized'
            // ]);

            $data = ['message'=>'unauthorized','status'=>false,'error'=>''];
            return redirect('/registration')->with($data);
        }
    }//end method


    public function VerifyOTP(Request $request){
        // $email = $request->input('email');
        $email = $request->session()->get('email');
        $otp = $request->input('otp');

        $count = User::where('email', $email)->where('otp', $otp)->count();

        if($count == 1){
            User::where('email', $email)->update(['otp' => 0]);

            // $token = JWTToken::CreateTokenForSetPassword($request->input('email'));

            $request->session()->put('otp_verify','yes');

            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'OTP verification successfully',
            // ],200)->cookie('token', $token, 60 * 24 * 30);

            $data = ["message"=>"OTP verification successfully","status"=>true,"error"=>''];
            return redirect('/reset-password')->with($data);
        }else{
            // return response()->json([
            //     'status' => 'fail',
            //     'message' => 'unauthorized'
            // ]);
            $data = ['message'=> 'unauthorized','status'=>false, 'error'=>''];
            return redirect('/login')->with($data);
        }
    }//end method

    public function ResetPassword(Request $request){
        try{
            // $email = $request->header('email');
            $email = $request->session()->get('email','default');
            $password = $request->input('password');

            $otp_verify = $request->session()->get('otp_verify','default');
            if($otp_verify === 'yes'){
                User::where('email', $email)->update(['password' => $password]);
                $request->session()->flush();

                $data = ['message'=> 'Password reset successfully','status'=>true, 'error'=>'' ];
                return redirect('/login')->with($data);
            }else{
                $data = ['message'=> 'Request fail','status'=>false, 'error'=>'' ];
                return redirect('/reset-password')->with($data);
            }
            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Password reset successfully',
            // ],200);

        }catch(Exception $e){
            // return response()->json([
            //     'status' => 'fail',
            //     'message' => 'somthing went wrong'
            // ]);
            $data = ['message'=> $e->getMessage(),'status'=>false, 'error'=>'' ];
            return redirect('/reset-password')->with($data);
        }
    }//end method

}
