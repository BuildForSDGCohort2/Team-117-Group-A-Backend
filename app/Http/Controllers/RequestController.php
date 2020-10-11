<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RequestModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class RequestController extends Controller
{

    public function __construct()
    {
        $this->status = config('constants.status');
    }

    /**
     * @OA\Post(
     * path="/api/addRequest",
     * summary="Add Request",
     * description="Add Request by testId, customerId, address",
     * operationId="addRequest",
     * tags={"Add Request"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass request credentials",
     *    @OA\JsonContent(
     *       required={"testId", "customerId", "address"},
     *       @OA\Property(property="testId", type="integer", format="testId", example=21),
     *       @OA\Property(property="customerId", type="integer", format="customerId", example=4),
     *       @OA\Property(property="address", type="string", format="address", example="50 Malaba street, Tunisia"),
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
     *       @OA\Property(property="message", type="string", example="Sorry, wrong testId, customerId, address provided, please try again")
     *        )
     *     )
     * )
     */
    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'testId' => 'required',
            'customerId' => 'required',
            'address' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'error'=>$validator->errors()], 400);
        }
        $request = RequestModel::create([
            'testId' => $request->testId,
            'customerId' => $request->customerId,
            'address' => $request->address,
        ]);
        return response()->json(['status' => 'record created', 'data' => $request], Response::HTTP_CREATED);

    }

    // Get all requests
    /**
     * @OA\Get(
     * path="/api/requests",
     * summary="Get Requests",
     * operationId="requests",
     * tags={"Get Requests"},
     * @OA\Response(response="200", description="Get all requests")
     * )
    */
    public function all(Request $request) {
        $requests = RequestModel::with(['test','customer'])->where('accepted', 0)->get();
        return response()->json(['status' => $this->status['ok'], 'data' => $requests], Response::HTTP_OK);
    }

    // Get all requests
    /**
     * @OA\Get(
     * path="/api/userRequests",
     * summary="Get User Requests",
     * operationId="userRequests",
     * tags={"Get User Requests"},
     * @OA\Response(response="200", description="Get all user requests")
     * )
    */
    public function user(Request $request) {
        $requests = RequestModel::with(['test'])->where('customerId', $request->user()->id)->get();
        return response()->json(['status' => $this->status['ok'], 'data' => $requests], Response::HTTP_OK);
    }

     /**
     * @OA\Delete(
     *     path="/api/request/{id}",
     *     description="deletes a single request from the  requests",
     *     operationId="deleteRequest",
     * summary="Delete Request",
     * tags={"Delete Request"},
     *     @OA\Parameter(
     *         description="ID of request to delete",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="request deleted"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
    */
    public function delete($id) {
        $res=RequestModel::where('id',$id)->delete();
        if($res){
            return response()->json(['status'=>$this->status['ok']], 200);
        }else {
            return response()->json(['status'=>$this->status['failed']], 400);
        }
    }

     /**
     * @OA\Put(
     *     path="/api/request/{id}",
     *     summary="Update Request",
     *     tags={"Update Request"},
     *     description="Update the values of a request",
     *     operationId="updateRequest",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of request that is to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass request credentials",
     *    @OA\JsonContent(
     *       required={"testId", "customerId", "address"},
     *       @OA\Property(property="testId", type="integer", format="testId", example=4),
     *       @OA\Property(property="customerId", type="integer", format="customerId", example=7),
     *       @OA\Property(property="address", type="string", format="address", example="55 Malabu, NY"),
     *    ),
     * ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid details supplied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Request not found"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="request updated"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
    */
    public function update(Request $request, $id)
    {
        $data = [
            'testId' => $request->testId,
            'customerId' => $request->customerId,
            'address' => $request->address,
        ];
        $request = RequestModel::findOrFail($id)->first;
        if(!$request) {
            return response()->json(['status'=>$this->status['failed']], 400);
        }
        $res = $request->fill($data)->save();
        if($res){
            return response()->json(['status'=>$this->status['ok'], 'data'=>$request], 200);
        }else{
            return response()->json(['status'=>$this->status['failed']], 400);
        }
    }
}
