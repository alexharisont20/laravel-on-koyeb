<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Product;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $_start = Carbon::parse(\request('start'));
        $start = $_start->format('Y-m-d');
        $_end = Carbon::parse(\request('end'));
        $end = $_end->format('Y-m-d');

        $productsCount = Product::count();
        $orderQ = Order::query()->whereBetween('created_at', [$_start->startOfDay()->toDateTimeString(), $_end->endOfDay()->toDateTimeString()]);
        $ordersCount = with($orderQ)->count();
        $initialStatus = data_get(config('app.orders'), 0, 'PENDING');
        $initialOrdersCount = with($orderQ)->whereStatus($initialStatus)->count();
        $returnedOrdersCount = with($orderQ)->whereStatus('returned')->count();
        $inactiveProducts = Product::whereIsActive(0)->get();
        $outOfStockProducts = Product::whereShouldTrack(1)->where('stock_count', '<=', 0)->get();
        return view('admin.dashboard', compact('productsCount', 'ordersCount', 'initialStatus', 'initialOrdersCount', 'returnedOrdersCount', 'inactiveProducts', 'outOfStockProducts', 'start', 'end'));
    }
}
