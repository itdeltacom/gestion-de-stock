<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Customer;
use App\Models\CreditSchedule;
use App\Models\Warehouse;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public function index()
    {
        // Statistiques générales pour le dashboard des rapports
        $totalSales = Sale::where('status', 'valide')->sum('total_ttc');
        $totalPurchases = Purchase::where('status', 'recu')->sum('total_ttc');
        $totalProducts = Product::where('is_active', true)->count();
        $totalCustomers = Customer::where('is_active', true)->count();

        // Ventes du mois
        $salesThisMonth = Sale::where('status', 'valide')
            ->whereMonth('sale_date', Carbon::now()->month)
            ->whereYear('sale_date', Carbon::now()->year)
            ->sum('total_ttc');

        // Achats du mois
        $purchasesThisMonth = Purchase::where('status', 'recu')
            ->whereMonth('purchase_date', Carbon::now()->month)
            ->whereYear('purchase_date', Carbon::now()->year)
            ->sum('total_ttc');

        // Profit du mois
        $monthlyProfit = $this->calculateMonthlyProfit();

        // Ventes par jour sur les 7 derniers jours
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last7Days[] = [
                'date' => $date->format('d/m'),
                'sales' => Sale::where('status', 'valide')
                    ->whereDate('sale_date', $date)
                    ->sum('total_ttc'),
                'purchases' => Purchase::where('status', 'recu')
                    ->whereDate('purchase_date', $date)
                    ->sum('total_ttc')
            ];
        }

        // Top produits vendus ce mois
        $topProducts = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('products.name', 'products.code', DB::raw('SUM(sale_details.quantity) as total_quantity'))
            ->where('sales.status', 'valide')
            ->whereMonth('sales.sale_date', Carbon::now()->month)
            ->whereYear('sales.sale_date', Carbon::now()->year)
            ->groupBy('products.id', 'products.name', 'products.code')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        // Alertes stock
        $lowStockProducts = Product::where('is_active', true)
            ->whereHas('stocks')
            ->get()
            ->filter(function ($product) {
                return $product->isLowStock();
            })
            ->take(5);

        // Créances en retard
        $overdueSchedules = CreditSchedule::where('status', 'retard')
            ->orWhere(function ($query) {
                $query->where('status', 'en_attente')
                    ->where('due_date', '<', now());
            })
            ->with(['customer', 'sale'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        // Ventes par entrepôt
        $salesByWarehouse = Warehouse::where('is_active', true)
            ->withCount([
                'sales' => function ($query) {
                    $query->where('status', 'valide')
                        ->whereMonth('sale_date', Carbon::now()->month)
                        ->whereYear('sale_date', Carbon::now()->year);
                }
            ])
            ->withSum([
                'sales' => function ($query) {
                    $query->where('status', 'valide')
                        ->whereMonth('sale_date', Carbon::now()->month)
                        ->whereYear('sale_date', Carbon::now()->year);
                }
            ], 'total_ttc')
            ->get();

        return view('reports.index', compact(
            'totalSales',
            'totalPurchases',
            'totalProducts',
            'totalCustomers',
            'salesThisMonth',
            'purchasesThisMonth',
            'monthlyProfit',
            'last7Days',
            'topProducts',
            'lowStockProducts',
            'overdueSchedules',
            'salesByWarehouse'
        ));
    }

    private function calculateMonthlyProfit()
    {
        $sales = Sale::where('status', 'valide')
            ->whereMonth('sale_date', Carbon::now()->month)
            ->whereYear('sale_date', Carbon::now()->year)
            ->with('details')
            ->get();

        $totalProfit = 0;
        foreach ($sales as $sale) {
            foreach ($sale->details as $detail) {
                $totalProfit += $detail->getProfitAmount();
            }
        }

        return $totalProfit;
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
        $salesByType = $sales->groupBy('type')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_ttc')
            ];
        });

        // Ventes par entrepôt
        $salesByWarehouse = $sales->groupBy('warehouse_id')->map(function ($group) {
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
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->getDisplayName();
                })
                ->addColumn('warehouse_name', function ($row) {
                    return $row->warehouse->name;
                })
                ->addColumn('total_formatted', function ($row) {
                    return number_format($row->total_ttc, 2) . ' MAD';
                })
                ->addColumn('profit', function ($row) {
                    $profit = $row->details->sum(function ($detail) {
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

        $totalValue = $stocks->sum(function ($stock) {
            return $stock->getTotalValue();
        });

        $lowStockCount = $stocks->filter(function ($stock) {
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
                ->addColumn('product_name', function ($row) {
                    return $row->product->name;
                })
                ->addColumn('product_code', function ($row) {
                    return $row->product->code;
                })
                ->addColumn('category', function ($row) {
                    return $row->product->category->name;
                })
                ->addColumn('warehouse_name', function ($row) {
                    return $row->warehouse->name;
                })
                ->addColumn('quantity_badge', function ($row) {
                    $class = $row->isLowStock() ? 'bg-danger' : 'bg-success';
                    return '<span class="badge ' . $class . '">' . $row->quantity . '</span>';
                })
                ->addColumn('average_cost_formatted', function ($row) {
                    return number_format($row->average_cost, 2) . ' MAD';
                })
                ->addColumn('total_value', function ($row) {
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
        $totalRemaining = $schedules->sum(function ($schedule) {
            return $schedule->getRemainingAmount();
        });

        $overdueCount = $schedules->filter(function ($schedule) {
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
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->getDisplayName();
                })
                ->addColumn('sale_reference', function ($row) {
                    return $row->sale->reference;
                })
                ->addColumn('amount_formatted', function ($row) {
                    return number_format($row->amount, 2) . ' MAD';
                })
                ->addColumn('remaining_formatted', function ($row) {
                    return number_format($row->getRemainingAmount(), 2) . ' MAD';
                })
                ->addColumn('status_badge', function ($row) {
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

    // Rapport financier complet
    public function financialReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();

        // Revenus
        $totalRevenue = Sale::where('status', 'valide')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->sum('total_ttc');

        // Coûts
        $totalCosts = Purchase::where('status', 'recu')
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->sum('total_ttc');

        // Profit brut
        $grossProfit = $totalRevenue - $totalCosts;

        // Marge bénéficiaire
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        // Ventes par mois (12 derniers mois)
        $monthlySales = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlySales[] = [
                'month' => $date->format('M Y'),
                'revenue' => Sale::where('status', 'valide')
                    ->whereMonth('sale_date', $date->month)
                    ->whereYear('sale_date', $date->year)
                    ->sum('total_ttc'),
                'costs' => Purchase::where('status', 'recu')
                    ->whereMonth('purchase_date', $date->month)
                    ->whereYear('purchase_date', $date->year)
                    ->sum('total_ttc')
            ];
        }

        return view('reports.financial', compact(
            'totalRevenue',
            'totalCosts',
            'grossProfit',
            'profitMargin',
            'monthlySales',
            'startDate',
            'endDate'
        ));
    }

    // Rapport des clients
    public function customersReport(Request $request)
    {
        $customers = Customer::where('is_active', true)
            ->withSum([
                'sales' => function ($query) {
                    $query->where('status', 'valide');
                }
            ], 'total_ttc')
            ->withCount([
                'sales' => function ($query) {
                    $query->where('status', 'valide');
                }
            ])
            ->orderBy('sales_sum_total_ttc', 'desc')
            ->get();

        $totalCustomers = $customers->count();
        $totalSales = $customers->sum('sales_sum_total_ttc');
        $averageOrderValue = $totalCustomers > 0 ? $totalSales / $totalCustomers : 0;

        return view('reports.customers', compact(
            'customers',
            'totalCustomers',
            'totalSales',
            'averageOrderValue'
        ));
    }

    // Rapport des fournisseurs
    public function suppliersReport(Request $request)
    {
        $suppliers = Supplier::where('is_active', true)
            ->withSum([
                'purchases' => function ($query) {
                    $query->where('status', 'recu');
                }
            ], 'total_ttc')
            ->withCount([
                'purchases' => function ($query) {
                    $query->where('status', 'recu');
                }
            ])
            ->orderBy('purchases_sum_total_ttc', 'desc')
            ->get();

        $totalSuppliers = $suppliers->count();
        $totalPurchases = $suppliers->sum('purchases_sum_total_ttc');
        $averageOrderValue = $totalSuppliers > 0 ? $totalPurchases / $totalSuppliers : 0;

        return view('reports.suppliers', compact(
            'suppliers',
            'totalSuppliers',
            'totalPurchases',
            'averageOrderValue'
        ));
    }

    // Rapport des entrepôts
    public function warehousesReport(Request $request)
    {
        $warehouses = Warehouse::where('is_active', true)
            ->withCount([
                'stocks',
                'sales' => function ($query) {
                    $query->where('status', 'valide');
                }
            ])
            ->withSum([
                'sales' => function ($query) {
                    $query->where('status', 'valide');
                }
            ], 'total_ttc')
            ->get()
            ->map(function ($warehouse) {
                $warehouse->total_stock_value = $warehouse->getTotalStockValue();
                return $warehouse;
            });

        $totalWarehouses = $warehouses->count();
        $totalStockValue = $warehouses->sum('total_stock_value');
        $totalSales = $warehouses->sum('sales_sum_total_ttc');

        return view('reports.warehouses', compact(
            'warehouses',
            'totalWarehouses',
            'totalStockValue',
            'totalSales'
        ));
    }

    // Rapport des produits
    public function productsReport(Request $request)
    {
        $products = Product::where('is_active', true)
            ->with(['category', 'stocks'])
            ->withCount([
                'saleDetails' => function ($query) {
                    $query->whereHas('sale', function ($q) {
                        $q->where('status', 'valide');
                    });
                }
            ])
            ->withSum([
                'saleDetails' => function ($query) {
                    $query->whereHas('sale', function ($q) {
                        $q->where('status', 'valide');
                    });
                }
            ], 'quantity')
            ->get()
            ->map(function ($product) {
                $product->total_stock = $product->getTotalStock();
                $product->total_value = $product->getTotalStockValue();
                return $product;
            });

        $totalProducts = $products->count();
        $totalStockValue = $products->sum('total_value');
        $lowStockCount = $products->filter(function ($product) {
            return $product->isLowStock();
        })->count();

        return view('reports.products', compact(
            'products',
            'totalProducts',
            'totalStockValue',
            'lowStockCount'
        ));
    }

    // Export des rapports
    public function exportSalesReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();

        $sales = Sale::where('status', 'valide')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->with(['customer', 'warehouse', 'user'])
            ->get();

        // Implementation for Excel/PDF export would go here
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}