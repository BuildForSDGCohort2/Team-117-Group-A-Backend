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
 * tags={"auth"},
 * security={ {"bearer": {} }},
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
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Logout Successful'], 200);
    }
}
