<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Order Summary Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #2563eb;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-label {
            font-weight: bold;
            color: #6b7280;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .stat-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin: 30px 0 15px;
            color: #2563eb;
            border-left: 4px solid #2563eb;
            padding-left: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background-color: #2563eb;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-size: 14px;
        }

        table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }

        table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-delivered {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        <h1>Order Summary Report</h1>
        <p>Generated on {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <!-- Buyer Information -->
    <div class="info-section">
        <h3 style="margin-bottom: 10px;">Customer Information</h3>
        <div class="info-row">
            <span class="info-label">Name:</span>
            <span>{{ $buyer->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span>{{ $buyer->email }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Report Period:</span>
            <span>{{ $data['date_range']['label'] }}</span>
        </div>
        @if($data['date_range']['start'])
        <div class="info-row">
            <span class="info-label">Date Range:</span>
            <span>
                {{ \Carbon\Carbon::parse($data['date_range']['start'])->format('d M Y') }} -
                {{ \Carbon\Carbon::parse($data['date_range']['end'])->format('d M Y') }}
            </span>
        </div>
        @endif
    </div>

    <!-- Statistics -->
    <h3 class="section-title">Summary Statistics</h3>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ $data['stats']['total_orders'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Spent</div>
            <div class="stat-value">Rp {{ number_format($data['stats']['total_spent'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Items Purchased</div>
            <div class="stat-value">{{ $data['stats']['total_items'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg Order Value</div>
            <div class="stat-value">Rp {{ number_format($data['stats']['average_order_value'], 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Orders by Status -->
    @if(count($data['orders_by_status']) > 0)
    <h3 class="section-title">Orders by Status</h3>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['orders_by_status'] as $status => $stats)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $status)) }}</td>
                <td>{{ $stats['count'] }}</td>
                <td>Rp {{ number_format($stats['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Top Sellers -->
    @if(count($data['orders_by_seller']) > 0)
    <h3 class="section-title">Top Sellers</h3>
    <table>
        <thead>
            <tr>
                <th>Seller Name</th>
                <th>Orders</th>
                <th>Total Spent</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['orders_by_seller'] as $seller)
            <tr>
                <td>{{ $seller['seller_name'] }}</td>
                <td>{{ $seller['count'] }}</td>
                <td>Rp {{ number_format($seller['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Top Products -->
    @if(count($data['top_products']) > 0)
    <h3 class="section-title">Top Products</h3>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Total Spent</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['top_products'] as $product)
            <tr>
                <td>{{ $product['product_name'] }}</td>
                <td>{{ $product['quantity'] }}</td>
                <td>Rp {{ number_format($product['total_spent'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Order Details -->
    <h3 class="section-title">Order Details</h3>
    <table>
        <thead>
            <tr>
                <th>Order No</th>
                <th>Date</th>
                <th>Seller</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->created_at->format('d M Y') }}</td>
                <td>{{ $order->seller->name }}</td>
                <td>{{ $order->orderDetails->sum('quantity') }}</td>
                <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                <td>
                    @if($order->status === 'delivered')
                    <span class="badge badge-delivered">Delivered</span>
                    @elseif(in_array($order->status, ['pending_payment', 'pending_verification']))
                    <span class="badge badge-pending">Pending</span>
                    @else
                    <span class="badge badge-cancelled">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>This is a computer-generated report. No signature required.</p>
        <p>Generated from Bookstore Platform</p>
    </div>

</body>

</html>