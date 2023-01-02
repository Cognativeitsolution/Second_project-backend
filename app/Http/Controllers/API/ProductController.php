<?php

namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Product;
use Validator;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Str;
use App\Helpers\helper as Helper;

class ProductController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','store']]);
        $this->middleware('permission:product-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return Helper::site_id() ;

        $search = request('search');

        if(!empty($search)){
            $data = Product::where('products.name', 'like', '%'.$search.'%')
                ->orWhere('products.email', 'like', '%'.$search.'%')
                ->orWhere('products.contact', 'like', '%'.$search.'%')
                ->orWhere('products.detail', 'like', '%'.$search.'%')
                ->latest()
                ->paginate(10);
            $data->appends (array ('search' => $search));

        }else{
            $data = Product::orderBy('id','DESC')->paginate(10);
        }

        foreach($data as $key => $value){
            $data[$key]['personal_email'] = $this->obfuscate_email($value->email);
            $data[$key]['personal_contact'] = Str::mask($value->contact, '*', 4, 5);
            unset( $data[$key]->email );
            unset( $data[$key]->contact );
        }

        return $this->sendResponse($data, 'Products retrieved successfully.');
    
        //return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $request->validate([
            'name' => 'required',
            'detail' => 'required'
        ]);
   
        $product = Product::create($input);
   
        return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
    }

    public function show($id)
    {
        $product = Product::find($id);
  
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
   
        return $this->sendResponse(new ProductResource($product), 'Product show - retrieved successfully.');
    }

    public function edit($id)
    {
        $product = Product::find($id);
  
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
   
        return $this->sendResponse(new ProductResource($product), 'Product edit - retrieved successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $input = $request->all();
   
        $request->validate([
            'name' => 'required',
            'detail' => 'required'
        ]);
   
        $product->name = $input['name'];
        $product->detail = $input['detail'];
        $product->save();
   
        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
   
        return $this->sendResponse([], 'Product deleted successfully.');
    }

}
