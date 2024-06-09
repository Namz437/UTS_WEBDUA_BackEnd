<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AddToCart;
use App\Models\Checkout;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $response = $this->default_response;
 
    // Validate input
    $request->validate([
        'cart_id' => 'required|exists:add_to_cart,id',
        'metode_pembayaran' => 'required',
        'metode_pengiriman' => 'required|in:COD,JNE,SICEPAT',
        'alamat' => 'required',
    ]);
 
    $biaya_pengiriman = [
        'COD' => 5000,
        'JNE' => 2500,
        'SICEPAT' => 3000
    ];
 
    DB::beginTransaction();
 
    try {
        // Get Data Cart
        $cartsQuery = AddToCart::where('customer_id', auth()->user()->id)
            ->whereNull('checkout_id')
            ->whereIn('id', $request->cart_id)
            ->with('product');
 
        $carts = $cartsQuery->get();
 
        if ($carts->count() == 0) {
            $response['status'] = false;
            $response['message'] = 'Cart not found';
            return response()->json($response);
        }
 
        // Validate stock and calculate total price
        $total_harga_product = 0;
        foreach ($carts as $cart) {
            if ($cart->product->stock < $cart->qty) {
                $response['success'] = false;
                $response['message'] = 'Stock produk tidak cukup yang dipesan: ' . $cart->qty . ', stock tersedia: ' . $cart->product->stock . ' (' . $cart->product->name . ')';
                return response()->json($response, 404);
            }
            $total_harga_product += $cart->total_harga;
        }
 
        // Decrease stock in product
        foreach ($carts as $cart) {
            $cart->product->stock -= $cart->qty;
            $cart->product->save();
        }
 
        // Save to checkout table
        $checkout = new Checkout();
        $checkout->customer_id = auth()->user()->id;
        $checkout->total_harga_product = $total_harga_product;
        $checkout->biaya_pengiriman = $biaya_pengiriman[$request->metode_pengiriman];
        $checkout->metode_pembayaran = $request->metode_pembayaran;
        $checkout->metode_pengiriman = $request->metode_pengiriman;
        $checkout->alamat = $request->alamat;
        $checkout->save();
 
        // Update cart with checkout ID
        $cartsQuery->update(['checkout_id' => $checkout->id]);
 
        // Delete items from add_to_cart table
        AddToCart::whereIn('id', $request->cart_id)->delete();
 
        DB::commit();
 
        $response['success'] = true;
        $response['data'] = $checkout;
        $response['message'] = 'Checkout Game Success';
        return response()->json($response);
       
    } catch (Exception $e) {
        DB::rollBack();
        $response['success'] = false;
        $response['message'] = $e->getMessage();
        return response()->json($response);
    }


        
        // Validasi Stock


        // hitung total harga produk

        // Kurangin stok yang ada di produk

        // Save data checkout

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
