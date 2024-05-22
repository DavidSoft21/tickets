<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Status\StatusCreateRequest;
use App\Http\Requests\Api\Status\StatusUpdateRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Status;

class StatusController extends Controller
{
    /**
     * @OA\Schema(
     *   schema="Status",
     *   type="object",
     *   @OA\Property(property="id", type="integer"),
     *   @OA\Property(property="name", type="string"),
     *   @OA\Property(property="description", type="string"),
     *   
     * )
     * 
     * @OA\Get(
     *   path="/status/index",
     *   summary="List of Status",
     *   description="List of Status",
     *   operationId="StatusIndex",
     *   tags={"Status"},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Ok"),
     *       @OA\Property(property="status", type="array", @OA\Items(ref="#/components/schemas/Status")),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not Found",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="NotFound"),
     *       @OA\Property(property="errors", type="object", example={"errors": {"Status not found"}}),
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Server Error"),
     *       @OA\Property(property="errors", type="object", example={"errors": {"Internal server error!"}}),
     *     )
     *   ),
     * )
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $status = Status::orderBy('id', 'asc')->get();

            if ($status->isEmpty()) {
                return response()->json([
                    'message' => 'Status not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Ok',
                'Status' => $status
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error',
                'errors' => ['errors' => 'Internal server error!']
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *  path="/status/store",
     * summary="Create Status",
     * description="Create a new Status",
     * operationId="StoreStatus",
     * tags={"Status"},
     * @OA\RequestBody(
     *   required=true,
     *  description="Status information",
     * @OA\JsonContent(
     *   required={"name","description"},
     *  @OA\Property(property="name", type="string", example="Active"),
     * @OA\Property(property="description", type="string", example="The Status is active"),
     * )
     * ),
     * @OA\Response(
     *  response=200,
     * description="Successfully registered",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Successfully registered"),
     * @OA\Property(property="Status", type="object", ref="#/components/schemas/Status"),
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Validation Error"),
     * @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}}),
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Server Error"),
     * @OA\Property(property="errors", type="object", example={"errors": {"Internal server error!"}}),
     * )
     * ),
     * )
     * 
     * @param StatusCreateRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     * 
     */
    public function store(StatusCreateRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $status = Status::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description']
            ]);

            return response()->json([
                'message' => 'Successfully registered',
                'Status' => $status
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }
    }


    /**
     * @OA\Get(
     *  path="/status/show/{id}",
     * summary="Show Status",
     * description="Show a Status by ID",
     * operationId="ShowStatus",
     * tags={"Status"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="Status ID",
     * @OA\Schema(
     * type="integer"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="OK",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Ok"),
     * @OA\Property(property="Status", type="object", ref="#/components/schemas/Status"),
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="NotFound"),
     * @OA\Property(property="errors", type="object", example={"errors": {"The Status with the provided ID was not found."}}),
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Server Error"),
     * @OA\Property(property="errors", type="object", example={"errors": {"Internal server error!"}}),
     * )
     * ),
     * )
     * 
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     * 
     */
    public function show(int $id)
    {
        try {
            $status = Status::findOrFail($id);

            if (!$status) {
                return response()->json([
                    'message' => 'Status not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Ok',
                'Status' => $status
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'NotFound',
                'errors' => ['id' => 'The Status with the provided ID was not found.']
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error',
                'errors' => ['errors' => 'Internal server error!']
            ], 500);
        }
    }


    /**
     * @OA\Put(
     *   path="/status/update/{id}",
     *   summary="Update Status",
     *   description="Update a Status by ID",
     *   operationId="UpdateStatus",
     *   tags={"Status"},
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Bearer token",
     *     @OA\Schema(
     *       type="string",
     *       default="Bearer "
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Status ID",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     description="Status information",
     *     @OA\JsonContent(
     *       required={"name","description"},
     *       @OA\Property(property="name", type="string", example="Active"),
     *       @OA\Property(property="description", type="string", example="The Status is active"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Successfully updated",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Successfully updated"),
     *       @OA\Property(property="Status", type="object", ref="#/components/schemas/Status"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation Error",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Validation Error"),
     *       @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}}),
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Server Error"),
     *       @OA\Property(property="errors", type="object", example={"errors": {"Internal server error!"}}),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not Found",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="NotFound"),
     *       @OA\Property(property="errors", type="object", example={"errors": {"The Status with the provided ID was not found."}}),
     *     )
     *   ),
     * )
     *
     * @param StatusUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function update(StatusUpdateRequest $request, int $id)
    {
        try {
            $validatedData = $request->validated();

            $status = Status::find($id);

            if (!$status) {
                return response()->json([
                    'message' => 'Status not found',
                ], 404);
            }

            $status->update([
                'name' => $validatedData['name'],
                'description' => $validatedData['description']
            ]);

            return response()->json([
                'message' => 'Successfully updated',
                'Status' => $status
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Delete(
     *  path="/status/destroy/{id}",
     * summary="Delete Status",
     * description="Delete a Status by ID",
     * operationId="DestroyStatus",
     * tags={"Status"},
     * security={{"bearerAuth": {}}},
     * @OA\Parameter(
     * name="Authorization",
     * in="header",
     * required=true,
     * description="Bearer token",
     * @OA\Schema(
     * type="string",
     * )
     * ),
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="Status ID",
     * @OA\Schema(
     * type="integer"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Successfully deleted",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Successfully deleted"),
     * @OA\Property(property="Status", type="object", ref="#/components/schemas/Status"),
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="NotFound"),
     * @OA\Property(property="errors", type="object", example={"errors": {"The Status with the provided ID was not found."}}),
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Server Error"),
     * @OA\Property(property="errors", type="object", example={"errors": {"Internal server error!"}}),
     * )
     * ),
     * )
     * 
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     *
     */
    public function destroy(int $id)
    {
        try {
            $status = Status::findOrFail($id);
            if (!$status) {
                return response()->json([
                    'message' => 'Status not found',
                ], 404);
            }

            $status->delete();

            return response()->json([
                'message' => 'Successfully deleted',
                'Status' => $status
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Status not found',
                'errors' => ['id' => 'The Status with the provided ID was not found.']
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error',
                'errors' => ['errors' => 'Internal server error!']
            ], 500);
        }
    }
}
