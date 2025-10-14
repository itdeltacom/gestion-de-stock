<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Customer;
use App\Models\CreditSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // Rapport des ventes
    public function salesReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();
        
        $sales = Sale::where('status', 'valide')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->with(['customer', 'warehouse', 'user'])
            ->get();
        
        $totalSales = $sales->sum('total_ttc');
        $totalHT = $sales->sum('total_ht');
        $totalTVA = $sales->sum('total_tva');
        $totalTransactions = $sales->count();
        
        // Ventes par type
        $salesByType = $sales->groupBy('type')->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_ttc')
            ];
        });
        
        // Ventes par entrepôt
        $salesByWarehouse = $sales->groupBy('warehouse_id')->map(function($group) {
            return [
                'warehouse' => $group->first()->warehouse->name,
                'count' => $group->count(),
                'total' => $group->sum('total_ttc')
            ];
        });
        
        return view('reports.sales', compact(
            'sales',
            'totalSales',
            'totalHT',
            'totalTVA',
            'totalTransactions',
            'salesByType',
            'salesByWarehouse',
            'startDate',
            'endDate'
        ));
    }

    public function salesReportData(Request $request)
    {
        if ($request->ajax()) {
            $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
            $endDate = $request->end_date ?? Carbon::now()->endOfMonth();
            
            $data = Sale::where('status', 'valide')
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->with(['customer', 'warehouse'])
                ->select('sales.*');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer_name', function($row){
                    return $row->customer->getDisplayName();
                })
                ->addColumn('warehouse_name', function($row){
                    return $row->warehouse->name;
                })
                ->addColumn('total_formatted', function($row){
                    return number_format($row->total_ttc, 2) . ' MAD';
                })
                ->addColumn('profit', function($row){
                    $profit = $row->details->sum(function($detail) {
                        return $detail->getProfitAmount();
                    });
                    return number_format($profit, 2) . ' MAD';
                })
                ->make(true);
        }
    }

    // Rapport des achats
    public function purchasesReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();
        
        $purchases = Purchase::where('status', 'recu')
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->with(['supplier', 'warehouse'])
            ->get();
        
        $totalPurchases = $purchases->sum('total_ttc');
        $totalHT = $purchases->sum('total_ht');
        $totalTVA = $purchases->sum('total_tva');
        $totalTransactions = $purchases->count();
        
        return view('reports.purchases', compact(
            'purchases',
            'totalPurchases',
            'totalHT',
            'totalTVA',
            'totalTransactions',
            'startDate',
            'endDate'
        ));
    }

    // Rapport de stock
    public function stockReport(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        
        $query = Stock::with(['product.category', 'warehouse']);
        
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        
        $stocks = $query->get();
        
        $totalValue = $stocks->sum(function($stock) {
            return $stock->getTotalValue();
        });
        
        $lowStockCount = $stocks->filter(function($stock) {
            return $stock->isLowStock();
        })->count();
        
        return view('reports.stock', compact('stocks', 'totalValue', 'lowStockCount'));
    }

    public function stockReportData(Request $request)
    {
        if ($request->ajax()) {
            $warehouseId = $request->warehouse_id;
            
            $query = Stock::with(['product.category', 'warehouse']);
            
            if ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            }
            
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('product_name', function($row){
                    return $row->product->name;
                })
                ->addColumn('product_code', function($row){
                    return $row->product->code;
                })
                ->addColumn('category', function($row){
                    return $row->product->category->name;
                })
                ->addColumn('warehouse_name', function($row){
                    return $row->warehouse->name;
                })
                ->addColumn('quantity_badge', function($row){
                    $class = $row->isLowStock() ? 'bg-danger' : 'bg-success';
                    return '<span class="badge '.$class.'">'.$row->quantity.'</span>';
                })
                ->addColumn('average_cost_formatted', function($row){
                    return number_format($row->average_cost, 2) . ' MAD';
                })
                ->addColumn('total_value', function($row){
                    return number_format($row->getTotalValue(), 2) . ' MAD';
                })
                ->rawColumns(['quantity_badge'])
                ->make(true);
        }
    }

    // Rapport des créances clients
    public function creditReport(Request $request)
    {
        $customerId = $request->customer_id;
        
        $query = CreditSchedule::with(['customer', 'sale'])
            ->where('status', '!=', 'paye');
        
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }
        
        $schedules = $query->orderBy('due_date', 'asc')->get();
        
        $totalCredit = $schedules->sum('amount');
        $totalPaid = $schedules->sum('paid_amount');
        $totalRemaining = $schedules->sum(function($schedule) {
            return $schedule->getRemainingAmount();
        });
        
        $overdueCount = $schedules->filter(function($schedule) {
            return $schedule->isOverdue();
        })->count();
        
        return view('reports.credit', compact(
            'schedules',
            'totalCredit',
            'totalPaid',
            'totalRemaining',
            'overdueCount'
        ));
    }

    public function creditReportData(Request $request)
    {
        if ($request->ajax()) {
            $customerId = $request->customer_id;
            
            $query = CreditSchedule::with(['customer', 'sale'])
                ->where('status', '!=', 'paye')
                ->select('credit_schedules.*');
            
            if ($customerId) {
                $query->where('customer_id', $customerId);
            }
            
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', function($row){
                    return $row->customer->getDisplayName();
                })
                ->addColumn('sale_reference', function($row){
                    return $row->sale->reference;
                })
                ->addColumn('amount_formatted', function($row){
                    return number_format($row->amount, 2) . ' MAD';
                })
                ->addColumn('remaining_formatted', function($row){
                    return number_format($row->getRemainingAmount(), 2) . ' MAD';
                })
                ->addColumn('status_badge', function($row){
                    if ($row->isOverdue()) {
                        return '<span class="badge bg-danger">En retard (' . $row->getDaysOverdue() . ' jours)</span>';
                    }
                    return '<span class="badge bg-warning">En attente</span>';
                })
                ->rawColumns(['status_badge'])
                ->make(true);
        }
    }

    // Rapport de profit
    public function profitReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();
        
        $sales = Sale::where('status', 'valide')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->with('details')
            ->get();
        
        $totalRevenue = $sales->sum('total_ttc');
        
        $totalCost = 0;
        $totalProfit = 0;
        
        foreach ($sales as $sale) {
            foreach ($sale->details as $detail) {
                $totalCost += $detail->cost_price * $detail->quantity;
                $totalProfit += $detail->getProfitAmount();
            }
        }
        
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
        
        return view('reports.profit', compact(
            'totalRevenue',
            'totalCost',
            'totalProfit',
            'profitMargin',
            'startDate',
            'endDate'
        ));
    }
}