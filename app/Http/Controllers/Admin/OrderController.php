<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\DigitalProductFulfillment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->with('product')->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }
    
    public function resend(Order $order, DigitalProductFulfillment $fulfillment)
    {
        $fulfillment->send($order, true); // Force resend
        return back()->with('success', 'Fichier renvoyé !');
    }
}
