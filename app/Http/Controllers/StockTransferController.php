<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockTransferDetail;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockTransferController extends Controller
{
    public function index()
    {
        return view('stock_transfers.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'user'])->select('stock_transfers.*');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('from_warehouse_name', function($row){
                    return $row->fromWarehouse->name;
                })
                ->addColumn('to_warehouse_name', function($row){
                    return $row->toWarehouse->name;
                })
                ->addColumn('user_name', function($row){
                    return $row->user->name;
                })
                ->addColumn('transfer_date_formatted', function($row){
                    return $row->transfer_date->format('d/m/Y');
                })
                ->addColumn('status_badge', function($row){
                    $badges = [
                        'en_attente' => '<span class="badge bg-warning">En attente</span>',
                        'envoye' => '<span class="badge bg-info">Envoyé</span>',
                        'recu' => '<span class="badge bg-success">Reçu</span>',
                        'annule' => '<span class="badge bg-danger">Annulé</span>',
                    ];
                    return $badges[$row->status] ?? $row->status;
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group" role="group">';
                    
                    if (auth()->user()->can('transfer-view')) {
                        $btn .= '<a href="'.route('stock-transfers.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    }
                    
                    if (auth()->user()->can('transfer-edit') && $row->status === 'en_attente') {
                        $btn .= '<a href="'.route('stock-transfers.edit', $row->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    }
                    
                    if (auth()->user()->can('transfer-send') && $row->canBeSent()) {
                        $btn .= '<button type="button" class="btn btn-sm btn-info send-btn" data-id="'.$row->id.'"><i class="fas fa-shipping-fast"></i> Envoyer</button>';
                    }
                    
                    if (auth()->user()->can('transfer-receive') && $row->canBeReceived()) {
                        $btn .= '<button type="button" class="btn btn-sm btn-success receive-btn" data-id="'.$row->id.'"><i class="fas fa-check"></i> Recevoir</button>';
                    }
                    
                    if (auth()->user()->can('transfer-delete') && $row->canBeDeleted()) {
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
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->with(['category', 'stocks'])->get();
        
        return view('stock_transfers.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date' => 'required|date',
            'note' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();
            
            // Vérifier le stock disponible
            foreach ($validated['products'] as $item) {
                $stock = Stock::where('product_id', $item['product_id'])
                    ->where('warehouse_id', $validated['from_warehouse_id'])
                    ->first();
                
                if (!$stock || $stock->quantity < $item['quantity']) {
                    $product = Product::find($item['product_id']);
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuffisant pour le produit: {$product->name}"
                    ], 400);
                }
            }
            
            // Créer le transfert
            $transfer = StockTransfer::create([
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id' => $validated['to_warehouse_id'],
                'user_id' => auth()->id(),
                'transfer_date' => $validated['transfer_date'],
                'status' => 'en_attente',
                'note' => $validated['note'] ?? null,
            ]);

            // Ajouter les détails
            foreach ($validated['products'] as $item) {
                $transfer->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transfert créé avec succès',
                'data' => $transfer,
                'redirect' => route('stock-transfers.show', $transfer->id)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['fromWarehouse', 'toWarehouse', 'user', 'details.product']);
        
        return view('stock_transfers.show', compact('stockTransfer'));
    }

    public function edit(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'en_attente') {
            return redirect()->route('stock-transfers.show', $stockTransfer->id)
                ->with('error', 'Seuls les transferts en attente peuvent être modifiés');
        }
        
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->with(['category', 'stocks'])->get();
        $stockTransfer->load('details.product');
        
        return view('stock_transfers.edit', compact('stockTransfer', 'warehouses', 'products'));
    }

    public function update(Request $request, StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Seuls les transferts en attente peuvent être modifiés'
            ], 400);
        }

        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date' => 'required|date',
            'note' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();
            
            // Vérifier le stock disponible
            foreach ($validated['products'] as $item) {
                $stock = Stock::where('product_id', $item['product_id'])
                    ->where('warehouse_id', $validated['from_warehouse_id'])
                    ->first();
                
                if (!$stock || $stock->quantity < $item['quantity']) {
                    $product = Product::find($item['product_id']);
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuffisant pour le produit: {$product->name}"
                    ], 400);
                }
            }
            
            // Mettre à jour le transfert
            $stockTransfer->update([
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id' => $validated['to_warehouse_id'],
                'transfer_date' => $validated['transfer_date'],
                'note' => $validated['note'] ?? null,
            ]);

            // Supprimer les anciens détails
            $stockTransfer->details()->delete();

            // Ajouter les nouveaux détails
            foreach ($validated['products'] as $item) {
                $stockTransfer->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transfert modifié avec succès',
                'data' => $stockTransfer,
                'redirect' => route('stock-transfers.show', $stockTransfer->id)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        if (!$stockTransfer->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Seuls les transferts en attente peuvent être supprimés'
            ], 400);
        }

        try {
            DB::beginTransaction();
            
            $stockTransfer->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transfert supprimé avec succès'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    public function send(StockTransfer $stockTransfer)
    {
        if (!$stockTransfer->canBeSent()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce transfert ne peut pas être envoyé'
            ], 400);
        }

        try {
            DB::beginTransaction();
            
            $stockTransfer->status = 'envoye';
            $stockTransfer->save();
            
            // Le stock sera réduit automatiquement via l'event du model
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transfert envoyé et stock source réduit avec succès'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function receive(StockTransfer $stockTransfer)
    {
        if (!$stockTransfer->canBeReceived()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce transfert ne peut pas être reçu'
            ], 400);
        }

        try {
            DB::beginTransaction();
            
            $stockTransfer->status = 'recu';
            $stockTransfer->save();
            
            // Le stock sera ajouté automatiquement via l'event du model
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transfert reçu et stock destination mis à jour avec succès'
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