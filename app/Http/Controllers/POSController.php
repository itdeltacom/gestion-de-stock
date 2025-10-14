<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        // Récupérer les entrepôts de type point de vente
        $warehouses = Warehouse::where('type', 'point_vente')
            ->where('is_active', true)
            ->get();

        return view('pos.index', compact('warehouses'));
    }

    public function screen(Request $request)
    {
        $warehouseId = $request->warehouse_id;

        if (!$warehouseId) {
            return redirect()->route('pos.index')->with('error', 'Veuillez sélectionner un point de vente');
        }

        $warehouse = Warehouse::findOrFail($warehouseId);

        if ($warehouse->type !== 'point_vente') {
            return redirect()->route('pos.index')->with('error', 'L\'entrepôt sélectionné n\'est pas un point de vente');
        }

        // Récupérer les produits disponibles dans ce point de vente
        $products = Product::whereHas('stocks', function ($query) use ($warehouseId) {
            $query->where('warehouse_id', $warehouseId)
                ->where('quantity', '>', 0);
        })
            ->with([
                'stocks' => function ($query) use ($warehouseId) {
                    $query->where('warehouse_id', $warehouseId);
                },
                'category'
            ])
            ->where('is_active', true)
            ->get();

        $customers = Customer::where('is_active', true)->get();

        return view('pos.screen', compact('warehouse', 'products', 'customers'));
    }

    public function getProductsByWarehouse(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        $search = $request->search;

        $query = Product::whereHas('stocks', function ($query) use ($warehouseId) {
            $query->where('warehouse_id', $warehouseId)
                ->where('quantity', '>', 0);
        })
            ->with([
                'stocks' => function ($query) use ($warehouseId) {
                    $query->where('warehouse_id', $warehouseId);
                },
                'category'
            ])
            ->where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function searchProduct(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        $barcode = $request->barcode;

        $product = Product::where(function ($query) use ($barcode) {
            $query->where('barcode', $barcode)
                ->orWhere('code', $barcode)
                ->orWhere('reference', $barcode);
        })
            ->whereHas('stocks', function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId)
                    ->where('quantity', '>', 0);
            })
            ->with([
                'stocks' => function ($query) use ($warehouseId) {
                    $query->where('warehouse_id', $warehouseId);
                },
                'category'
            ])
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produit introuvable ou stock insuffisant'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function checkStock(Request $request)
    {
        $productId = $request->product_id;
        $warehouseId = $request->warehouse_id;
        $quantity = $request->quantity;

        $stock = Stock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        if (!$stock || $stock->quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuffisant',
                'available_quantity' => $stock ? $stock->quantity : 0
            ], 400);
        }

        return response()->json([
            'success' => true,
            'available_quantity' => $stock->quantity
        ]);
    }

    public function createSale(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'nullable|exists:customers,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:espece,cheque,virement,carte,autre',
            'amount_paid' => 'required|numeric|min:0',
            'is_credit' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Client par défaut si non spécifié
            $customerId = $validated['customer_id'] ?? Customer::where('code', 'CLI-DEFAULT')->first()?->id;

            if (!$customerId) {
                // Créer un client par défaut s'il n'existe pas
                $defaultCustomer = Customer::create([
                    'name' => 'Client de passage',
                    'code' => 'CLI-DEFAULT',
                    'type' => 'individuel',
                    'credit_limit' => 0,
                    'is_active' => true,
                ]);
                $customerId = $defaultCustomer->id;
            }

            $customer = Customer::find($customerId);

            // Créer la vente
            $sale = Sale::create([
                'type' => 'facture',
                'customer_id' => $customerId,
                'warehouse_id' => $validated['warehouse_id'],
                'user_id' => auth()->id(),
                'sale_date' => now(),
                'is_credit' => $validated['is_credit'] ?? false,
                'status' => 'en_attente',
                'from_pos' => true,
            ]);

            // Ajouter les détails
            foreach ($validated['products'] as $item) {
                $product = Product::find($item['product_id']);
                $stock = Stock::where('product_id', $item['product_id'])
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->first();

                // Vérifier le stock
                if (!$stock || $stock->quantity < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuffisant pour le produit: {$product->name}"
                    ], 400);
                }

                $costPrice = $stock->average_cost;

                $detail = new SaleDetail([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'cost_price' => $costPrice,
                    'tva_rate' => $product->tva_rate,
                ]);

                $detail->calculateTotals();
                $sale->details()->save($detail);
            }

            // Calculer les totaux
            $sale->calculateTotals();

            // Vérifier la limite de crédit si vente à crédit
            if ($sale->is_credit) {
                if (!$customer->canBuyOnCredit($sale->total_ttc)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Limite de crédit insuffisante. Crédit disponible: ' . number_format($customer->getRemainingCredit(), 2) . ' MAD'
                    ], 400);
                }
            }

            // Valider automatiquement la vente POS
            $sale->status = 'valide';
            $sale->save();

            // Enregistrer le paiement si payé
            if ($validated['amount_paid'] > 0) {
                $sale->addPayment(
                    $validated['amount_paid'],
                    $validated['payment_method'],
                    null,
                    'Paiement POS'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vente enregistrée avec succès',
                'data' => $sale->load('details.product', 'customer'),
                'change' => max(0, $validated['amount_paid'] - $sale->total_ttc)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printReceipt(Sale $sale)
    {
        $sale->load(['customer', 'warehouse', 'details.product']);

        return view('pos.receipt', compact('sale'));
    }

    public function todaySales(Request $request)
    {
        $warehouseId = $request->warehouse_id;

        $sales = Sale::where('warehouse_id', $warehouseId)
            ->where('from_pos', true)
            ->whereDate('sale_date', today())
            ->with(['customer', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSales = $sales->where('status', 'valide')->sum('total_ttc');
        $totalTransactions = $sales->where('status', 'valide')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'sales' => $sales,
                'total_sales' => $totalSales,
                'total_transactions' => $totalTransactions,
            ]
        ]);
    }
}