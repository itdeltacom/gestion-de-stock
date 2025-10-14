<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('purchases.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Purchase::with(['supplier', 'warehouse', 'user'])->select('purchases.*');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('supplier_name', function($row){
                    return $row->supplier->getDisplayName();
                })
                ->addColumn('warehouse_name', function($row){
                    return $row->warehouse->name;
                })
                ->addColumn('user_name', function($row){
                    return $row->user->name;
                })
                ->addColumn('total_ttc_formatted', function($row){
                    return number_format($row->total_ttc, 2) . ' MAD';
                })
                ->addColumn('purchase_date_formatted', function($row){
                    return $row->purchase_date->format('d/m/Y');
                })
                ->addColumn('status_badge', function($row){
                    $badges = [
                        'en_attente' => '<span class="badge bg-warning">En attente</span>',
                        'recu' => '<span class="badge bg-success">Reçu</span>',
                        'annule' => '<span class="badge bg-danger">Annulé</span>',
                    ];
                    return $badges[$row->status] ?? $row->status;
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group" role="group">';
                    
                    if (auth()->user()->can('purchase-view')) {
                        $btn .= '<a href="'.route('purchases.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    }
                    
                    if (auth()->user()->can('purchase-edit') && $row->status === 'en_attente') {
                        $btn .= '<a href="'.route('purchases.edit', $row->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    }
                    
                    if (auth()->user()->can('purchase-receive') && $row->status === 'en_attente') {
                        $btn .= '<button type="button" class="btn btn-sm btn-success receive-btn" data-id="'.$row->id.'"><i class="fas fa-check"></i> Recevoir</button>';
                    }
                    
                    if (auth()->user()->can('purchase-delete') && $row->canBeDeleted()) {
                        $btn .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
                    }
                    
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->with('category')->get();
        
        return view('purchases.create', compact('suppliers', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_date' => 'required|date',
            'note' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            
            // Créer l'achat
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'user_id' => auth()->id(),
                'purchase_date' => $validated['purchase_date'],
                'status' => 'en_attente',
                'note' => $validated['note'] ?? null,
            ]);

            // Ajouter les détails
            foreach ($validated['products'] as $item) {
                $product = Product::find($item['product_id']);
                
                $detail = new PurchaseDetail([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tva_rate' => $product->tva_rate,
                ]);
                
                $detail->calculateTotals();
                $purchase->details()->save($detail);
            }

            // Calculer les totaux
            $purchase->calculateTotals();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Achat créé avec succès',
                'data' => $purchase,
                'redirect' => route('purchases.show', $purchase->id)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'warehouse', 'user', 'details.product']);
        
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        if ($purchase->status !== 'en_attente') {
            return redirect()->route('purchases.show', $purchase->id)
                ->with('error', 'Seuls les achats en attente peuvent être modifiés');
        }
        
        $suppliers = Supplier::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->with('category')->get();
        $purchase->load('details.product');
        
        return view('purchases.edit', compact('purchase', 'suppliers', 'warehouses', 'products'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->status !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Seuls les achats en attente peuvent être modifiés'
            ], 400);
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_date' => 'required|date',
            'note' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            
            // Mettre à jour l'achat
            $purchase->update([
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'purchase_date' => $validated['purchase_date'],
                'note' => $validated['note'] ?? null,
            ]);

            // Supprimer les anciens détails
            $purchase->details()->delete();

            // Ajouter les nouveaux détails
            foreach ($validated['products'] as $item) {
                $product = Product::find($item['product_id']);
                
                $detail = new PurchaseDetail([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tva_rate' => $product->tva_rate,
                ]);
                
                $detail->calculateTotals();
                $purchase->details()->save($detail);
            }

            // Recalculer les totaux
            $purchase->calculateTotals();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Achat modifié avec succès',
                'data' => $purchase,
                'redirect' => route('purchases.show', $purchase->id)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Purchase $purchase)
    {
        if (!$purchase->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Seuls les achats en attente peuvent être supprimés'
            ], 400);
        }

        try {
            DB::beginTransaction();
            
            $purchase->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Achat supprimé avec succès'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    public function receive(Purchase $purchase)
    {
        if ($purchase->status !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Cet achat ne peut pas être reçu'
            ], 400);
        }

        try {
            DB::beginTransaction();
            
            $purchase->status = 'recu';
            $purchase->save();
            
            // Le stock sera mis à jour automatiquement via l'event du model
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Achat reçu et stock mis à jour avec succès'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réception: ' . $e->getMessage()
            ], 500);
        }
    }
}