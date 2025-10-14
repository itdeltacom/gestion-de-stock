<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Models\CreditSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $totalProducts = Product::where('is_active', true)->count();
        $totalCustomers = Customer::where('is_active', true)->count();
        $totalSuppliers = Supplier::where('is_active', true)->count();
        $totalWarehouses = Warehouse::where('is_active', true)->count();

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

        // Produits en rupture ou faible stock
        $lowStockProducts = Product::where('is_active', true)
            ->whereHas('stocks')
            ->get()
            ->filter(function ($product) {
                return $product->isLowStock();
            })
            ->take(10);

        // Top 10 des produits les plus vendus ce mois
        $topProducts = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('products.name', 'products.code', DB::raw('SUM(sale_details.quantity) as total_quantity'))
            ->where('sales.status', 'valide')
            ->whereMonth('sales.sale_date', Carbon::now()->month)
            ->whereYear('sales.sale_date', Carbon::now()->year)
            ->groupBy('products.id', 'products.name', 'products.code')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // Échéances en retard
        $overdueSchedules = CreditSchedule::where('status', 'retard')
            ->orWhere(function ($query) {
                $query->where('status', 'en_attente')
                    ->where('due_date', '<', now());
            })
            ->with(['customer', 'sale'])
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();

        // Ventes par jour sur les 7 derniers jours
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last7Days[] = [
                'date' => $date->format('d/m'),
                'total' => Sale::where('status', 'valide')
                    ->whereDate('sale_date', $date)
                    ->sum('total_ttc')
            ];
        }

        // Valeur totale du stock
        $totalStockValue = Stock::with('product')->get()->sum(function ($stock) {
            return $stock->quantity * $stock->average_cost;
        });

        // Créances clients
        $totalCredit = Sale::where('is_credit', true)
            ->where('status', 'valide')
            ->where('payment_status', '!=', 'paye')
            ->sum('remaining_amount');

        // Ventes du jour
        $salesToday = Sale::where('status', 'valide')
            ->whereDate('sale_date', today())
            ->sum('total_ttc');

        return view('dashboard.index', compact(
            'totalProducts',
            'totalCustomers',
            'totalSuppliers',
            'totalWarehouses',
            'salesThisMonth',
            'purchasesThisMonth',
            'lowStockProducts',
            'topProducts',
            'overdueSchedules',
            'last7Days',
            'totalStockValue',
            'totalCredit',
            'salesToday'
        ));
    }

    public function getSalesChart(Request $request)
    {
        $period = $request->period ?? 'month'; // day, week, month, year

        $data = [];

        switch ($period) {
            case 'day':
                // Ventes par heure aujourd'hui
                for ($i = 0; $i < 24; $i++) {
                    $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $data[] = [
                        'label' => $hour . 'h',
                        'value' => Sale::where('status', 'valide')
                            ->whereDate('sale_date', today())
                            ->whereTime('created_at', '>=', $hour . ':00:00')
                            ->whereTime('created_at', '<', ($i + 1) . ':00:00')
                            ->sum('total_ttc')
                    ];
                }
                break;

            case 'week':
                // Ventes par jour sur les 7 derniers jours
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $data[] = [
                        'label' => $date->format('d/m'),
                        'value' => Sale::where('status', 'valide')
                            ->whereDate('sale_date', $date)
                            ->sum('total_ttc')
                    ];
                }
                break;

            case 'month':
                // Ventes par jour du mois en cours
                $daysInMonth = Carbon::now()->daysInMonth;
                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $date = Carbon::now()->startOfMonth()->addDays($i - 1);
                    $data[] = [
                        'label' => $i,
                        'value' => Sale::where('status', 'valide')
                            ->whereDate('sale_date', $date)
                            ->sum('total_ttc')
                    ];
                }
                break;

            case 'year':
                // Ventes par mois de l'année en cours
                $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
                for ($i = 1; $i <= 12; $i++) {
                    $data[] = [
                        'label' => $months[$i - 1],
                        'value' => Sale::where('status', 'valide')
                            ->whereMonth('sale_date', $i)
                            ->whereYear('sale_date', Carbon::now()->year)
                            ->sum('total_ttc')
                    ];
                }
                break;
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getStockByWarehouse()
    {
        $warehouses = Warehouse::where('is_active', true)
            ->with('stocks')
            ->get();

        $data = $warehouses->map(function ($warehouse) {
            return [
                'name' => $warehouse->name,
                'value' => $warehouse->getTotalStockValue()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getTopCustomers(Request $request)
    {
        $limit = $request->limit ?? 10;

        $customers = Customer::withSum([
            'sales' => function ($query) {
                $query->where('status', 'valide');
            }
        ], 'total_ttc')
            ->orderBy('sales_sum_total_ttc', 'desc')
            ->limit($limit)
            ->get();

        $data = $customers->map(function ($customer) {
            return [
                'name' => $customer->getDisplayName(),
                'value' => $customer->sales_sum_total_ttc ?? 0
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}