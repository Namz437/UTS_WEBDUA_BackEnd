<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\AddToCart;
use App\Models\Product;
use Illuminate\Http\Request;
 
class AddToCartController extends Controller
{
    public function index(Request $request)
    {
        $response = $this->default_response;
 
        // Get  data cart berdasarkan customer id dan checkout id is null
        $add_to_cart = AddToCart::where('customer_id', $request->user()->id)
        ->whereNull('checkout_id')
        ->with('product')
        ->get();
 
        $response['success'] = true;
        $response['data'] = $add_to_cart;
        return response()->json($response);
    }
    public function store(Request $request)
    {
        $response = $this->default_response;
        // dd ($request->all());
        // Validasi product id nya harus ada di table product
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric',
        ]);
 
        // validasi qty yang di add
        $product = Product::find($request->product_id);
 
        if($request->qty > $product->stock){
            $response['success'] = false;
            $response['message'] = 'Stock Game not enough';
            return response()->json($response);
        }
 
        // simpan ke table add to cart
        $add_to_cart = AddToCart::where('product_id', $request->product_id)
        ->where('customer_id', $request->user()->id)
        ->whereNull('checkout_id')
        ->first();
 
        if($add_to_cart){
            $add_to_cart->qty += $request->qty;
            $add_to_cart->total_harga = $add_to_cart->harga_satuan * $add_to_cart->qty;
            $add_to_cart->save();
        }else{
            $add_to_cart = new AddToCart();
            $add_to_cart->product_id = $request->product_id;
            $add_to_cart->customer_id = $request->user()->id;
            $add_to_cart->harga_satuan = (int) $product->price;
            $add_to_cart->qty = $request->qty;
            $add_to_cart->total_harga = $add_to_cart->harga_satuan * $add_to_cart->qty;
            $add_to_cart->save();
        }
        $response['success'] = true;
        $response['message'] = 'Add Game to cart success';
        $response['data'] = $add_to_cart;
        return response()->json($response);
    }
 
    public function update(Request $request, string $id)
    {
        $response = $this->default_response;
        // dd ($request->all());
        // Validasi product id nya harus ada di table product
        $request->validate([
            'qty' => 'required|numeric',
        ]);
       
        // simpan ke table add to cart
        $add_to_cart = AddToCart::where('customer_id', $request->user()->id)
        ->whereNull('checkout_id')
        ->with('product')
        ->find($id);
       
        if(empty($add_to_cart)){
            $response['success'] = false;
            $response['message'] = 'Add game to cart not found';
            return response()->json($response);
        }
       
 
        // Validate qty
 
        if($request->qty > $add_to_cart->product->stock){
            $response['success'] = false;
            $response['message'] = 'Stock game not enough';
            return response()->json($response);
        }
 
            $add_to_cart->harga_satuan = (int) $add_to_cart->product->price;
            $add_to_cart->qty = $request->qty;
            $add_to_cart->total_harga = $add_to_cart->harga_satuan * $add_to_cart->qty;
            $add_to_cart->save();
       
        $response['success'] = true;
        $response['message'] = 'game at cart updated successfully';
        $response['data'] = $add_to_cart;
        return response()->json($response);
    }
 
    public function destroy(Request $request, string $id)
    {
        $response = $this->default_response;
       
        $add_to_cart = AddToCart::where('customer_id', $request->user()->id)
        ->whereNull('checkout_id')
        ->find($id);
 
        if(empty($add_to_cart)){
            $response['success'] = false;
            $response['message'] = 'game not found';
            return response()->json($response);
        }
 
        $add_to_cart->delete();
 
        $response['success'] = true;
        $response['message'] = ' game from cart successfully deleted';
        return response()->json($response);
    }
}