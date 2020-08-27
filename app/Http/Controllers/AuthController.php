<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

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
        // $check = User::where('email', $request->email)->get();
        // if($check) {
        //     return response()->json(['status' => 'failed', 'error' => 'duplicate email'], 403);
        // }
        // $check2 = User::where('phone', $request->phone)->get();
        // if($check2) {
        //     return response()->json(['status' => 'failed', 'error' => 'duplicate phone number'], 403);
        // }
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);
        return response()->json(['status' => 'record created', 'data' => $user], 201);

    }

    /**
 * @OA\Get(
 * path="/api/logout",
 * summary="Logout",
 * description="Logout user and invalidate token",
 * operationId="authLogout",
 * tags={"Logout"},
 * security={ {"passport": {} }},
 * @OA\Response(
 *    response=200,
 *    description="Success"
 *     ),
 * @OA\Response(
 *    response=401,
 *    description="Returns when user is not authenticated",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Not authorized"),
 *    )
 * )
 * )
 */
    public function logout(Request $request) {
        if($request->user()){
            $request->user()->token()->revoke();
            return response()->json(['message' => 'Logout Successful'], 200);
        }else {
            return response()->json(['message' => 'User not logged in'], 401);
        }

    }
}
