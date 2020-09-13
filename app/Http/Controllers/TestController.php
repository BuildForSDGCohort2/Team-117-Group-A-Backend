<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Test;
use App\RequestModel;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Validator;

class TestController extends Controller
{

    public function __construct()
    {
        $this->status = config('constants.status');
    }

    /**
     * @OA\Post(
     * path="/api/addTest",
     * summary="Add Test",
     * description="Add Test by testName, price, description",
     * operationId="addTest",
     * tags={"Add Test"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass test credentials",
     *    @OA\JsonContent(
     *       required={"testName", "price", "description"},
     *       @OA\Property(property="testName", type="string", format="testName", example="Malaria"),
     *       @OA\Property(property="price", type="integer", format="price", example=4000),
     *       @OA\Property(property="description", type="string", format="description", example="A test for Malaria"),
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
     *       @OA\Property(property="message", type="string", example="Sorry, wrong testName, price, description provided, please try again")
     *        )
     *     )
     * )
     */
    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'testName' => 'required',
            'price' => 'required',
            'description' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'error'=>$validator->errors()], 400);
        }
        $test = Test::where('testName', '=', $request->name)->first();
        if ($test) {
            // test exist
            return response()->json(['status' => $this->status['failed'], 'error'=>'Test already exist'], 409);
        }
        $test = Test::create([
            'testName' => $request->testName,
            'price' => $request->price,
            'description' => $request->description,
        ]);
        return response()->json(['status' => 'record created', 'data' => $test], Response::HTTP_CREATED);

    }

    // Get all tests
    /**
     * @OA\Get(
     * path="/api/tests",
     * summary="Get Tests",
     * operationId="tests",
     * tags={"Get Tests"},
     * @OA\Response(response="200", description="Get all tests")
     * )
    */
    public function all(Request $request) {
        $tests = Test::all();
        return response()->json(['status' => $this->status['ok'], 'data' => $tests], Response::HTTP_OK);
    }

     /**
     * @OA\Delete(
     *     path="/api/test/{id}",
     *     description="deletes a single test from the  tests",
     *     operationId="deleteTest",
     * summary="Delete Test",
     * tags={"Delete Test"},
     *     @OA\Parameter(
     *         description="ID of test to delete",
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
     *         description="test deleted"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
    */
    public function delete($id) {
        $res=Test::where('id',$id)->delete();
        if($res){
            return response()->json(['status'=>$this->status['ok']], 200);
        }else {
            return response()->json(['status'=>$this->status['failed']], 400);
        }
    }

     /**
     * @OA\Put(
     *     path="/api/test/{id}",
     *     summary="Update Test",
     *     tags={"Update Test"},
     *     description="Update the values of a test",
     *     operationId="updateTest",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of test that is to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass test credentials",
     *    @OA\JsonContent(
     *       required={"testName", "price", "description"},
     *       @OA\Property(property="testName", type="string", format="testName", example="Malaria"),
     *       @OA\Property(property="price", type="integer", format="price", example=4000),
     *       @OA\Property(property="description", type="string", format="description", example="A test for Malaria"),
     *    ),
     * ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid details supplied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Test not found"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="test updated"
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
            'testName' => $request->testName,
            'price' => $request->price,
            'description' => $request->description,
        ];
        $test = Test::findOrFail($id);
        if(!$test) {
            return response()->json(['status'=>$this->status['failed']], 400);
        }
        $res = $test->fill($data)->save();
        if($res){
            $test = Test::findOrFail($id);
            return response()->json(['status'=>$this->status['ok'], 'data'=>$test], 200);
        }else{
            return response()->json(['status'=>$this->status['failed']], 400);
        }
    }
}
