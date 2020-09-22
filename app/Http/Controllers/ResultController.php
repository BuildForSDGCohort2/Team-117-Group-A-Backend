<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Result;
use Symfony\Component\HttpFoundation\Response;
use Validator;


class ResultController extends Controller
{
    public function __construct()
    {
        $this->status = config('constants.status');
    }

    /**
     * @OA\Post(
     * path="/api/addResult",
     * summary="Add Result",
     * description="Add Result by requestId, testId, customerId, companiesId, result",
     * operationId="addResult",
     * tags={"Add Result"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass result credentials",
     *    @OA\JsonContent(
     *       required={"requestId", "testId", "customerId", "companiesId", "result"},
     *      @OA\Property(property="requestId", type="integer", format="requestId", example=11),
     *       @OA\Property(property="testId", type="integer", format="testId", example=21),
     *       @OA\Property(property="customerId", type="integer", format="customerId", example=4),
     *     @OA\Property(property="companiesId", type="integer", format="companiesId", example=22),
     *       @OA\Property(property="result", type="string", format="string", example="Positive, you have malaria"),
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
     *       @OA\Property(property="message", type="string", example="Sorry, wrong testId, customerId, companiesId, result provided, please try again")
     *        )
     *     )
     * )
     */
    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'requestId' => 'required',
            'testId' => 'required',
            'customerId' => 'required',
            'companiesId' => 'required',
            'result' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'error'=>$validator->errors()], 400);
        }
        $result = Result::create([
            'requestId' => $request->requestId,
            'testId' => $request->testId,
            'customerId' => $request->customerId,
            'companiesId' => $request->companiesId,
            'result' => $request->result,
        ]);
        return response()->json(['status' => 'record created', 'data' => $result], Response::HTTP_CREATED);
    }

    // Get all results
    /**
     * @OA\Get(
     * path="/api/results",
     * summary="Get Results",
     * operationId="results",
     * tags={"Get Results"},
     * @OA\Response(response="200", description="Get all results")
     * )
    */
    public function all(Request $request) {
        $requests = Result::with(['request', 'test','customer', 'company'])->where('accepted', 0)->get();
        return response()->json(['status' => $this->status['ok'], 'data' => $requests], Response::HTTP_OK);
    }

     /**
     * @OA\Delete(
     *     path="/api/result/{id}",
     *     description="deletes a single result from the  results",
     *     operationId="deleteResult",
     * summary="Delete Result",
     * tags={"Delete Result"},
     *     @OA\Parameter(
     *         description="ID of result to delete",
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
     *         description="result deleted"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
    */
    public function delete($id) {
        $res=Result::where('id',$id)->delete();
        if($res){
            return response()->json(['status'=>$this->status['ok']], 200);
        }else {
            return response()->json(['status'=>$this->status['failed']], 400);
        }
    }

     /**
     * @OA\Put(
     *     path="/api/result/{id}",
     *     summary="Update Result",
     *     tags={"Update Result"},
     *     description="Update the values of a result",
     *     operationId="updateResult",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of result that is to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass result credentials",
     *    @OA\JsonContent(
     *       required={'requestId', "testId", "customerId", "companiesId", "result"},
     *       @OA\Property(property="requestId", type="integer", format="requestId", example=41),
     *       @OA\Property(property="testId", type="integer", format="testId", example=4),
     *       @OA\Property(property="customerId", type="integer", format="customerId", example=7),
     *      @OA\Property(property="companiesId", type="integer", format="companiesId", example=17),
     *       @OA\Property(property="result", type="string", format="string", example="Negative, you do not have malaria"),
     *    ),
     * ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid details supplied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Result not found"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="result updated"
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
            'requestId' => $request->requestId,
            'testId' => $request->testId,
            'customerId' => $request->customerId,
            'companiesId' => $request->companiesId,
            'result' => $request->address,
        ];
        $request = Result::findOrFail($id)->first();
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
