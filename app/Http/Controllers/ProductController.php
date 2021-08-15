<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

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
        $list = [];
        foreach ($products as $prods => $obj) {
            $obj = array_merge($obj->toArray(), [
                'image_url' => $request->getSchemeAndHttpHost().Storage::url('public/images/').$obj->image
            ]);
            array_push($list, $obj);
        }
        
        return $this->listResponse($products, $request, $list);
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
            'image' => 'required|image:png,jpg,jpeg|max:2048',
            'price' => 'required|numeric',
            'stock' => 'numeric',
        ];
        $validator = $this->validate($request->all(), $rules, $request);
        if ($validator->fails()) {
            return $this->failureResponse($validator->errors(), $request);
        }

        $image = $request->file('image');
        $image->storeAs('public/images', $image->hashName());
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock ?: 0,
            'image' => $image->hashName()
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
        $data = array_merge($product->toArray(), [
            'image_url' => $request->getSchemeAndHttpHost().Storage::url('public/images/').$product->image
        ]);

        return $this->findResponse($data, $request);
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
            'image' => 'image:png,jpg,jpeg|max:2048',
            'price' => 'required|numeric',
            'stock' => 'numeric',
        ];
        $validator = $this->validate($request->all(), $rules, $request);
        if ($validator->fails()) {
            return $this->failureResponse($validator->errors(), $request);
        }

        $product = Product::find($id);

        if ($product) {
            if ($request->file('image')) {
                Storage::disk('local')->delete('public/images'.$product->image);
                $image = $request->file('image');
                $image->storeAs('public/images', $image->hashName());
                $product->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'stock' => $request->stock ?: 0,
                    'image' => $image->hashName()
                ]);
            } else {
                $product->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'stock' => $request->stock ?: 0,
                ]);
            }
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
            Storage::disk('local')->delete('public/images'.$product->image);
            $product->delete();
            return response()->json([], 204);
        } else {
            return $this->failureResponse('Product not found', $request, 404);
        }
    }
}
