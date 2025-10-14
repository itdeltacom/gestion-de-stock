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
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;


class SaleController extends Controller
{
    public function index()
    {
        return view('sales.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Sale::with(['customer', 'warehouse', 'user'])->select('sales.*');

            if ($request->has('type') && $request->type !== 'all') {
                $data->where('type', $request->type);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->getDisplayName();
                })
                ->addColumn('warehouse_name', function ($row) {
                    return $row->warehouse->name;
                })
                ->addColumn('type_badge', function ($row) {
                    $badges = [
                        'devis' => '<span class="badge bg-info">Devis</span>',
                        'bon_commande' => '<span class="badge bg-warning">Bon de commande</span>',
                        'facture' => '<span class="badge bg-primary">Facture</span>',
                    ];
                    return $badges[$row->type] ?? $row->type;
                })
                ->addColumn('total_ttc_formatted', function ($row) {
                    return number_format($row->total_ttc, 2) . ' MAD';
                })
                ->addColumn('sale_date_formatted', function ($row) {
                    return $row->sale_date->format('d/m/Y');
                })
                ->addColumn('payment_status_badge', function ($row) {
                    $badges = [
                        'non_paye' => '<span class="badge bg-danger">Non payé</span>',
                        'partiel' => '<span class="badge bg-warning">Partiel</span>',
                        'paye' => '<span class="badge bg-success">Payé</span>',
                    ];
                    return $badges[$row->payment_status] ?? $row->payment_status;
                })
                ->addColumn('status_badge', function ($row) {
                    $badges = [
                        'en_attente' => '<span class="badge bg-warning">En attente</span>',
                        'valide' => '<span class="badge bg-success">Validé</span>',
                        'annule' => '<span class="badge bg-danger">Annulé</span>',
                    ];
                    return $badges[$row->status] ?? $row->status;
                })
                ->addColumn('credit_badge', function ($row) {
                    if ($row->is_credit) {
                        return '<span class="badge bg-warning"><i class="fas fa-clock"></i> Crédit</span>';
                    }
                    return '';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';

                    if (auth()->user()->can('sale-view')) {
                        $btn .= '<a href="' . route('sales.show', $row->id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    }

                    if (auth()->user()->can('sale-edit') && $row->status === 'en_attente') {
                        $btn .= '<a href="' . route('sales.edit', $row->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    }

                    if (auth()->user()->can('sale-validate') && $row->status === 'en_attente') {
                        $btn .= '<button type="button" class="btn btn-sm btn-success validate-btn" data-id="' . $row->id . '"><i class="fas fa-check"></i> Valider</button>';
                    }

                    if (auth()->user()->can('sale-convert') && $row->canBeConverted()) {
                        $btn .= '<button type="button" class="btn btn-sm btn-warning convert-btn" data-id="' . $row->id . '"><i class="fas fa-exchange-alt"></i> Facturer</button>';
                    }

                    if (auth()->user()->can('sale-payment') && $row->payment_status !== 'paye' && $row->status === 'valide') {
                        $btn .= '<button type="button" class="btn btn-sm btn-success payment-btn" data-id="' . $row->id . '"><i class="fas fa-money-bill"></i> Paiement</button>';
                    }

                    if (auth()->user()->can('sale-delete') && $row->canBeDeleted()) {
                        $btn .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['type_badge', 'payment_status_badge', 'status_badge', 'credit_badge', 'action'])
                ->make(true);
        }
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->with(['category', 'stocks'])->get();

        return view('sales.create', compact('customers', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:devis,bon_commande,facture',
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'sale_date' => 'required|date',
            'is_credit' => 'boolean',
            'note' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'number_of_installments' => 'nullable|integer|min:1',
            'first_due_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $customer = Customer::find($validated['customer_id']);

            // Créer la vente
            $sale = Sale::create([
                'type' => $validated['type'],
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'user_id' => auth()->id(),
                'sale_date' => $validated['sale_date'],
                'is_credit' => $validated['is_credit'] ?? false,
                'status' => 'en_attente',
                'note' => $validated['note'] ?? null,
            ]);

            // Ajouter les détails
            foreach ($validated['products'] as $item) {
                $product = Product::find($item['product_id']);
                $stock = Stock::where('product_id', $item['product_id'])
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->first();

                $costPrice = $stock ? $stock->average_cost : $product->current_average_cost;

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

            // Si vente à crédit, vérifier la limite et créer les échéances
            if ($sale->is_credit) {
                if (!$customer->canBuyOnCredit($sale->total_ttc)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Limite de crédit insuffisante. Crédit disponible: ' . number_format($customer->getRemainingCredit(), 2) . ' MAD'
                    ], 400);
                }

                // Créer les échéances si spécifiées
                if (isset($validated['number_of_installments']) && isset($validated['first_due_date'])) {
                    $sale->createCreditSchedules(
                        $validated['number_of_installments'],
                        $validated['first_due_date']
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vente créée avec succès',
                'data' => $sale,
                'redirect' => route('sales.show', $sale->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'warehouse', 'user', 'details.product', 'payments', 'creditSchedules']);

        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        if ($sale->status !== 'en_attente') {
            return redirect()->route('sales.show', $sale->id)
                ->with('error', 'Seules les ventes en attente peuvent être modifiées');
        }

        $customers = Customer::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->with(['category', 'stocks'])->get();
        $sale->load(['details.product', 'creditSchedules']);

        return view('sales.edit', compact('sale', 'customers', 'warehouses', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        if ($sale->status !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Seules les ventes en attente peuvent être modifiées'
            ], 400);
        }

        $validated = $request->validate([
            'type' => 'required|in:devis,bon_commande,facture',
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'sale_date' => 'required|date',
            'is_credit' => 'boolean',
            'note' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'number_of_installments' => 'nullable|integer|min:1',
            'first_due_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $customer = Customer::find($validated['customer_id']);

            // Mettre à jour la vente
            $sale->update([
                'type' => $validated['type'],
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'sale_date' => $validated['sale_date'],
                'is_credit' => $validated['is_credit'] ?? false,
                'note' => $validated['note'] ?? null,
            ]);

            // Supprimer les anciens détails
            $sale->details()->delete();

            // Ajouter les nouveaux détails
            foreach ($validated['products'] as $item) {
                $product = Product::find($item['product_id']);
                $stock = Stock::where('product_id', $item['product_id'])
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->first();

                $costPrice = $stock ? $stock->average_cost : $product->current_average_cost;

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

            // Recalculer les totaux
            $sale->calculateTotals();

            // Si vente à crédit, vérifier la limite et recréer les échéances
            if ($sale->is_credit) {
                if (!$customer->canBuyOnCredit($sale->total_ttc)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Limite de crédit insuffisante. Crédit disponible: ' . number_format($customer->getRemainingCredit(), 2) . ' MAD'
                    ], 400);
                }

                // Recréer les échéances si spécifiées
                if (isset($validated['number_of_installments']) && isset($validated['first_due_date'])) {
                    $sale->createCreditSchedules(
                        $validated['number_of_installments'],
                        $validated['first_due_date']
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vente modifiée avec succès',
                'data' => $sale,
                'redirect' => route('sales.show', $sale->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Sale $sale)
    {
        if (!$sale->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Seules les ventes en attente peuvent être supprimées'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $sale->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vente supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validate(Sale $sale)
    {
        if ($sale->status !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Cette vente ne peut pas être validée'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Vérifier le stock avant validation
            foreach ($sale->details as $detail) {
                $stock = Stock::where('product_id', $detail->product_id)
                    ->where('warehouse_id', $sale->warehouse_id)
                    ->first();

                if (!$stock || $stock->quantity < $detail->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuffisant pour le produit: {$detail->product->name}"
                    ], 400);
                }
            }

            $sale->status = 'valide';
            $sale->save();

            // Le stock sera mis à jour automatiquement via l'event du model

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vente validée et stock mis à jour avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function convert(Sale $sale)
    {
        if (!$sale->canBeConverted()) {
            return response()->json([
                'success' => false,
                'message' => 'Cette vente ne peut pas être convertie en facture'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $invoice = $sale->convertToInvoice();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vente convertie en facture avec succès',
                'redirect' => route('sales.show', $invoice->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la conversion: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addPayment(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:espece,cheque,virement,carte,autre',
            'transaction_reference' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            if ($validated['amount'] > $sale->remaining_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le montant du paiement ne peut pas dépasser le montant restant'
                ], 400);
            }

            $payment = $sale->addPayment(
                $validated['amount'],
                $validated['payment_method'],
                $validated['transaction_reference'] ?? null,
                $validated['note'] ?? null
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paiement enregistré avec succès',
                'data' => $payment
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProductsByWarehouse(Request $request)
    {
        $warehouseId = $request->warehouse_id;

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

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }


    /**
     * Generate PDF for viewing
     */
    public function generatePdf(Sale $sale)
    {
        $sale->load(['customer', 'warehouse', 'user', 'details.product', 'payments', 'creditSchedules']);

        $pdf = Pdf::loadView('sales.pdf', compact('sale'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans'
            ]);

        return $pdf->stream($sale->reference . '.pdf');
    }

    /**
     * Download PDF
     */
    public function downloadPdf(Sale $sale)
    {
        $sale->load(['customer', 'warehouse', 'user', 'details.product', 'payments', 'creditSchedules']);

        $pdf = Pdf::loadView('sales.pdf', compact('sale'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans'
            ]);

        return $pdf->download($sale->reference . '.pdf');
    }
}