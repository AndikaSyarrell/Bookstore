<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class SalesReportController extends Controller
{
    /**
     * Generate monthly sales report Excel
     */
    public function generateMonthlyReport(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
        ]);

        $sellerId = Auth::id();
        $month = $request->month;
        $year = $request->year;

        try {
            // Gather sales data
            $salesData = $this->gatherSalesData($sellerId, $month, $year);

            
            // Generate Excel
            $filename = $this->generateExcel($salesData);

            // Return download response
            return response()->download($filename, basename($filename))->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gather sales data for the period
     */
    private function gatherSalesData($sellerId, $month, $year)
{
    $user = Auth::user();

    $startDate = "{$year}-{$month}-01";
    $endDate = date('Y-m-t', strtotime($startDate));

    $orders = Order::where('seller_id', $sellerId)
    ->where('status', 'delivered')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->with(['buyer', 'orderDetails.product', 'shipment'])
    ->orderBy('created_at', 'desc')
    ->get();

    // Summary sesuai DB
    $summary = [
        'total_orders' => $orders->count(),

        // Total revenue dari order_details.total_price
        'total_revenue' => DB::table('order_details')
            ->join('orders','orders.id','=','order_details.order_id')
            ->where('orders.seller_id',$sellerId)
            ->where('orders.status','delivered')
            ->whereBetween('orders.created_at',[$startDate,$endDate])
            ->sum('order_details.total_price'),

        'total_products_sold' => DB::table('order_details')
            ->join('orders','orders.id','=','order_details.order_id')
            ->where('orders.seller_id',$sellerId)
            ->where('orders.status','delivered')
            ->whereBetween('orders.created_at',[$startDate,$endDate])
            ->sum('order_details.quantity'),

        'avg_order_value' => $orders->count() > 0
            ? $orders->sum('total_amount') / $orders->count()
            : 0,
    ];

    $ordersData = $orders->map(function ($order) {
    return [
        'order_number' => $order->order_number,
        'date' => $order->created_at->format('Y-m-d'),
        'buyer_name' => $order->buyer->name ?? '-',
        'items' => $order->orderDetails->sum('quantity'),
        'total' => $order->total_amount,
        'status' => ucfirst($order->status),

        'shipping' => [
            'carrier' => $order->shipment->carrier ?? '-',
            'tracking_number' => $order->shipment->tracking_number ?? '-',
            'tracking_url' => $order->shipment->tracking_url ?? null,
            'shipped_date' => $order->shipment->shipped_date ?? null,
            'delivery_date' => $order->shipment->delivery_date ?? null,
            'estimated_delivery' => $order->shipment->estimated_delivery ?? null,
        ],
    ];
})->toArray();

    // Products sold sesuai DB
    $products = DB::table('order_details')
        ->join('orders', 'order_details.order_id', '=', 'orders.id')
        ->join('products', 'order_details.product_id', '=', 'products.id')
        ->where('orders.seller_id', $sellerId)
        ->where('orders.status', 'delivered')
        ->whereBetween('orders.created_at', [$startDate, $endDate])
        ->select(
            'products.title as product_name',
            'products.id as sku',

            DB::raw('SUM(order_details.quantity) as quantity_sold'),

            // Revenue = total_price
            DB::raw('SUM(order_details.total_price) as revenue'),

            // Profit sesuai struktur DB
            DB::raw('SUM(order_details.quantity * (products.selling_price - products.price)) as profit'),

            DB::raw('AVG(order_details.total_price / order_details.quantity) as avg_price')
        )
        ->groupBy('products.id','products.title')
        ->orderByDesc('revenue')
        ->get()
        ->map(function ($item) {
            return [
                'product_name' => $item->product_name,
                'sku' => 'PRD-'.$item->sku,
                'quantity_sold' => (int)$item->quantity_sold,
                'revenue' => (float)$item->revenue,
                'profit' => (float)$item->profit,
                'avg_price' => (float)$item->avg_price,
            ];
        })
        ->toArray();

    $monthNames = [
        1=>'January',2=>'February',3=>'March',4=>'April',
        5=>'May',6=>'June',7=>'July',8=>'August',
        9=>'September',10=>'October',11=>'November',12=>'December'
    ];

    return [
        'seller'=>[
            'name'=>$user->name,
            'email'=>$user->email,
        ],
        'period'=>[
            'month'=>$month,
            'year'=>$year,
            'month_name'=>$monthNames[$month],
        ],
        'summary'=>$summary,
        'orders'=>$ordersData,
        'products'=>$products,
    ];
}

    /**
     * Generate Excel file using Python script
     */
    private function generateExcel($salesData)
    {
        // Create temp JSON file
        $jsonFile = storage_path('app/temp/sales_data_' . uniqid() . '.json');
        $outputFile = storage_path('app/temp/sales_report_' . uniqid() . '.xlsx');

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Write JSON data
        file_put_contents($jsonFile, json_encode($salesData, JSON_PRETTY_PRINT));

        // Run Python script
        $scriptPath = base_path('scripts/GenerateSalesReport.py');
        
        $result = Process::run("python3 {$scriptPath} {$jsonFile} {$outputFile}");

        // Clean up JSON file
        unlink($jsonFile);

        if ($result->failed()) {
            throw new \Exception('Failed to generate Excel: ' . $result->errorOutput());
        }

        // Recalculate formulas
        $recalcScript = base_path('vendor/anthropic/xlsx-skill/scripts/recalc.py');
        if (file_exists($recalcScript)) {
            Process::run("python3 {$recalcScript} {$outputFile}");
        }

        return $outputFile;
    }

    /**
     * Show report download page
     */
    public function showReportPage()
    {
        $currentMonth = date('n');
        $currentYear = date('Y');

        return view('seller.reports.index', compact('currentMonth', 'currentYear'));
    }
}