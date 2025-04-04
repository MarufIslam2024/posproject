<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function DashboardSummary(Request $request){
        $user_id = request()->header('id');

        $product = Product::where('user_id', $user_id)->count();
        $category = Category::where('user_id', $user_id)->count();
        $customer = Customer::where('user_id', $user_id)->count();
        $invoice = Invoice::where('user_id', $user_id)->count();
        $total = Invoice::where('user_id', $user_id)->sum('total');
        $vat = Invoice::where('user_id', $user_id)->sum('vat');
        $payable = Invoice::where('user_id', $user_id)->sum('payable');
        $discount = Invoice::where('user_id', $user_id)->sum('discount');

        $data = [
            'product' => $product,
            'category' => $category,
            'customer' => $customer,
            'invoice' => $invoice,
            'total' => $total,
            'vat' => $vat,
            'payable' => $payable,
            'discount' => $discount
        ];

        return $data;
    }

}
