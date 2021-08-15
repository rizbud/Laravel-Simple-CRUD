<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $page = $request->page ?: 1;
        $limit = $request->limit ?: 10;

        $products = Product::latest()->paginate($limit, ['*'], 'page', $page);
        
        return $this->listResponse($products, $request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'numeric'
        ];
        $validator = $this->validate($request->all(), $rules, $request);
        if ($validator->fails()) {
            return $this->failureResponse($validator->errors(), $request);
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock ?: 0,
        ]);
        return $this->createdResponse($product, $request, 'New product has been added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $product = Product::find($id);

        return $this->findResponse($product, $request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric'
        ];
        $validator = $this->validate($request->all(), $rules, $request);
        if ($validator->fails()) {
            return $this->failureResponse($validator->errors(), $request);
        }

        $product = Product::find($id);

        if ($product) {
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock ?: 0,
            ]);
            return $this->successResponse(['message' => 'Product has been updated'], $request);
        } else {
            return $this->failureResponse('Product not found', $request, 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $product = Product::find($id);

        if ($product) {
            $product->delete();
            return response()->json([], 204);
        } else {
            return $this->failureResponse('Product not found', $request, 404);
        }
    }
}
