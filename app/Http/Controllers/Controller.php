<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function validate($data, $rules, $request) {
        return Validator::make($data, $rules, $message = [
            'required' => ':attribute is required',
            'string' => ':attribute must be a string',
            'numeric' => ':attribute must be a number',
            'unique' => ':attribute with value :input has been registered',
            'confirm_password.same' => 'Password confirmation does not match',
            'email' => 'Format email is not valid'
        ]);
    }

    public function listResponse($data, $request, $list = null) {
        if ($data) {
            return response()->json([
                'response' => [
                    'status' => 200,
                    'message' => 'OK',
                    'url' => $request->fullUrl()
                ],
                'data' => $list ?: $data->toArray()['data']
            ])->withHeaders([
                'Total' => $data->toArray()['total'],
                'Page' => $data->toArray()['current_page'],
                'Total-Page' => $data->toArray()['last_page'],
                'Per-Page' => $data->toArray()['per_page']
            ], 200);
        } else {
            $this->failureResponse(null, $request);
        }
    }

    public function successResponse($data, $request, $status = 200) {
        return response()->json([
            'response' => [
                'status' => $status,
                'message' => getMessage($status),
                'url' => $request->fullUrl()
            ],
            'data' => $data
        ], $status);
    }

    public function failureResponse($message = null, $request, $status = 422) {
        return response()->json([
            'response' => [
                'status' => $status,
                'message' => getMessage($status),
                'url' => $request->fullUrl()
            ],
            'data' => [
                'message' => $message ?: getMessage($status)
            ]
        ], $status);
    }

    public function createdResponse($data, $request, $message = null) {
        if ($data) {
            return $this->successResponse(
                ['message' => $message ?: getMessage(201)],
                $request,
                201
            );
        } else {
            return $this->failureResponse(null, $request, 500);
        }
    }

    public function findResponse($data, $request, $message = null) {
        if ($data) {
            return $this->successResponse($data, $request);
        } else {
            return $this->failureResponse(null, $request, 404);
        }
    }
}
