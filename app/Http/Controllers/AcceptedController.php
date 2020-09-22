<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RequestModel;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class AcceptedController extends Controller
{
    public function __construct()
    {
        $this->status = config('constants.status');
    }

    /**
     * @OA\Post(
     * path="/api/addAccepted",
     * summary="Add Accepted",
     * description="Add Accepted by requestId, acceptedCompaniesId",
     * operationId="addAccepted",
     * tags={"Add Accepted"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass accepted credentials",
     *    @OA\JsonContent(
     *       required={"requestId", "acceptedCompaniesId"},
     *       @OA\Property(property="requestId", type="integer", format="requestId", example=21),
     *       @OA\Property(property="acceptedCompaniesId", type="integer", format="acceptedCompaniesId", example=4),
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
            'requestId' => 'required',
            'acceptedCompaniesId' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'error'=>$validator->errors()], 400);
        }
        $data = [
            'acceptedCompaniesId' => $request->acceptedCompaniesId,
            'accepted' => 1,
        ];
        $id = $request->requestId;
        $request = RequestModel::findOrFail($id);
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

    // Get all accepted
    /**
     * @OA\Get(
     * path="/api/accepted",
     * summary="Get Accepted",
     * operationId="accepted",
     * tags={"Get Accepted"},
     * @OA\Response(response="200", description="Get all accepted")
     * )
    */
    public function all(Request $request) {
        $requests =RequestModel::with(['test','customer', 'acceptedCompany'])->where('accepted', 1)->get();
        return response()->json(['status' => $this->status['ok'], 'data' => $requests], Response::HTTP_OK);
    }

    // Get all accepted
    /**
     * @OA\Get(
     * path="/api/companyAccepted",
     * summary="Get Company Accepted",
     * operationId="companyAccepted",
     * tags={"Get Company Accepted"},
     * @OA\Response(response="200", description="Get all company accepted")
     * )
    */
    public function companyAll($id) {
        $requests =RequestModel::with(['test','customer', 'acceptedCompany'])->where('accepted', 1)->where('acceptedCompaniesId', $id)->get();
        return response()->json(['status' => $this->status['ok'], 'data' => $requests], Response::HTTP_OK);
    }

     /**
     * @OA\Delete(
     *     path="/api/accepted/{id}",
     *     description="deletes a single accepted request from the  accepted requests",
     *     operationId="deleteAccepted",
     * summary="Delete Accepted",
     * tags={"Delete Request"},
     *     @OA\Parameter(
     *         description="ID of accepted to delete",
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
     *         description="accepted deleted"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
    */
    public function delete($id) {
        $data = [
            'accepted' => 0,
            'acceptedCompaniesId' => null
        ];
        $request = RequestModel::findOrFail($id)->where('accepted', 1)->first();
        if(!$request) {
            return response()->json(['status'=>$this->status['failed']], 400);
        }
        $res = $request->fill($data)->save();
        if($res){
            return response()->json(['status'=>$this->status['ok']], 200);
        }else{
            return response()->json(['status'=>$this->status['failed']], 400);
        }
    }

}
