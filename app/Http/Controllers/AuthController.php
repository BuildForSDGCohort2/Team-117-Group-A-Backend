<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Company;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->status = config('constants.status');
    }
     /**
     * Register
     *
     * @param  mixed $request
     * @return void
     */

    /**
     * @OA\Post(
     * path="/api/register",
     * summary="Register User",
     * description="register user by first name, last name, phone number, email, password",
     * operationId="authRegister",
     * tags={"Register User"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"first_name", "last_name", "phone", "email", "password"},
     *       @OA\Property(property="first_name", type="string", format="first_name", example="Segun"),
     *       @OA\Property(property="last_name", type="string", format="last_name", example="Aka"),
     *      @OA\Property(property="phone", type="string", format="phone", example="234894445"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
    *    response=201,
    *    description="Record Created"
    *     ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'error'=>$validator->errors()], 400);
        }
        $user = User::where('email', '=', $request->email)->orWhere('phone', '=', $request->phone)->first();
        if ($user) {
            // user exist
            return response()->json(['status' => $this->status['failed'], 'error'=>'User already exist'], 409);
        }
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);
        return response()->json(['status' => 'record created', 'data' => $user], Response::HTTP_CREATED);

    }

    /**
     * @OA\Post(
     * path="/api/registerCompany",
     * summary="Register Company",
     * description="register company by name, address, phone number, email, password",
     * operationId="authRegisterCompany",
     * tags={"Register Company"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass company credentials",
     *    @OA\JsonContent(
     *       required={"name", "address", "phone", "email", "password"},
     *       @OA\Property(property="name", type="string", format="name", example="Excel Hospital"),
     *       @OA\Property(property="address", type="string", format="address", example="12 Abayo street"),
     *      @OA\Property(property="phone", type="string", format="phone", example="234894445"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
    *    response=201,
    *    description="Record Created"
    *     ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */
    public function registerCompany(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'error'=>$validator->errors()], 400);
        }
        $company = Company::where('email', '=', $request->email)->orWhere('phone', '=', $request->phone)->first();
        if ($company) {
            // user exist
            return response()->json(['status' => $this->status['failed'], 'error'=>'Company already exist'], 409);
        }
        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
        ]);
        return response()->json(['status' => 'record created', 'data' => $company], Response::HTTP_CREATED);

    }

    //
    public function logout(Request $request) {
        if($request->user()){
            $request->user()->token()->revoke();
            return response()->json(['message' => 'Logout Successful'], 200);
        }
        return response()->json(['message' => 'User not logged in'], 401);

    }
    public function user(Request $request) {
        return $request->user();
    }

}
