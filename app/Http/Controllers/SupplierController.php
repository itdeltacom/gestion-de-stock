<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index()
    {
        return view('suppliers.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Supplier::select('*');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('type_badge', function($row){
                    $badge = $row->type === 'societe' 
                        ? '<span class="badge bg-primary">Société</span>' 
                        : '<span class="badge bg-info">Individuel</span>';
                    return $badge;
                })
                ->addColumn('display_name', function($row){
                    return $row->getDisplayName();
                })
                ->addColumn('status_badge', function($row){
                    $badge = $row->is_active 
                        ? '<span class="badge bg-success">Actif</span>' 
                        : '<span class="badge bg-danger">Inactif</span>';
                    return $badge;
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group" role="group">';
                    
                    if (auth()->user()->can('supplier-view')) {
                        $btn .= '<a href="'.route('suppliers.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    }
                    
                    if (auth()->user()->can('supplier-edit')) {
                        $btn .= '<a href="'.route('suppliers.edit', $row->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    }
                    
                    if (auth()->user()->can('supplier-delete')) {
                        $btn .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
                    }
                    
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['type_badge', 'status_badge', 'action'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:suppliers,code',
            'type' => 'required|in:individuel,societe',
            'ice' => 'nullable|string|max:20',
            'raison_sociale' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $supplier = Supplier::create($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Fournisseur créé avec succès',
                'data' => $supplier
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('purchases');
        $totalPurchases = $supplier->getTotalPurchases();
        
        return view('suppliers.show', compact('supplier', 'totalPurchases'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:suppliers,code,'.$supplier->id,
            'type' => 'required|in:individuel,societe',
            'ice' => 'nullable|string|max:20',
            'raison_sociale' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $supplier->update($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Fournisseur modifié avec succès',
                'data' => $supplier
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Supplier $supplier)
    {
        try {
            // Vérifier s'il y a des achats
            if ($supplier->purchases()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer un fournisseur ayant des achats'
                ], 400);
            }
            
            DB::beginTransaction();
            
            $supplier->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Fournisseur supprimé avec succès'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }
}