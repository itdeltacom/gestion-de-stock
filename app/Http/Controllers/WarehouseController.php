<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class WarehouseController extends Controller
{
    public function index()
    {
        return view('warehouses.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Warehouse::select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('type_badge', function ($row) {
                    $badge = $row->type === 'depot'
                        ? '<span class="badge badge-sm bg-gradient-primary">Dépôt</span>'
                        : '<span class="badge badge-sm bg-gradient-info">Point de vente</span>';
                    return $badge;
                })
                ->addColumn('status_badge', function ($row) {
                    $badge = $row->is_active
                        ? '<span class="badge badge-sm bg-gradient-success">Actif</span>'
                        : '<span class="badge badge-sm bg-gradient-secondary">Inactif</span>';
                    return $badge;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';

                    if (auth()->user()->can('warehouse-view')) {
                        $btn .= '<a href="' . route('warehouses.show', $row->id) . '" class="text-secondary font-weight-bold text-xs me-2 view-btn" data-toggle="tooltip" data-original-title="View warehouse">
                                    <i class="fas fa-eye"></i>
                                </a>';
                    }

                    if (auth()->user()->can('warehouse-edit')) {
                        $btn .= '<a href="' . route('warehouses.edit', $row->id) . '" class="text-secondary font-weight-bold text-xs me-2 edit-btn" data-id="' . $row->id . '" data-toggle="tooltip" data-original-title="Edit warehouse">
                                    <i class="fas fa-edit"></i>
                                </a>';
                    }

                    if (auth()->user()->can('warehouse-delete')) {
                        $btn .= '<a href="javascript:;" class="text-secondary font-weight-bold text-xs delete-btn" data-id="' . $row->id . '" data-toggle="tooltip" data-original-title="Delete warehouse">
                                    <i class="fas fa-trash"></i>
                                </a>';
                    }

                    return $btn;
                })
                ->rawColumns(['type_badge', 'status_badge', 'action'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:warehouses,code',
            'type' => 'required|in:depot,point_vente',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $warehouse = Warehouse::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Entrepôt créé avec succès',
                'data' => $warehouse
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['stocks.product', 'purchases', 'sales']);

        $totalStockValue = $warehouse->getTotalStockValue();
        $totalProducts = $warehouse->stocks()->count();
        $lowStockProducts = $warehouse->stocks()
            ->with('product')
            ->get()
            ->filter(function ($stock) {
                return $stock->isLowStock();
            });

        return view('warehouses.show', compact('warehouse', 'totalStockValue', 'totalProducts', 'lowStockProducts'));
    }

    public function edit(Warehouse $warehouse)
    {
        // Check if request is AJAX
        if (request()->ajax()) {
            return response()->json($warehouse);
        }

        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:warehouses,code,' . $warehouse->id,
            'type' => 'required|in:depot,point_vente',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $warehouse->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Entrepôt modifié avec succès',
                'data' => $warehouse
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Warehouse $warehouse)
    {
        try {
            // Vérifier s'il y a du stock
            if ($warehouse->stocks()->where('quantity', '>', 0)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer un entrepôt contenant du stock'
                ], 400);
            }

            DB::beginTransaction();

            $warehouse->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Entrepôt supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStockData(Request $request, Warehouse $warehouse)
    {
        if ($request->ajax()) {
            $data = $warehouse->stocks()->with(['product.category']);

            return DataTables::of($data)
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
                ->addColumn('quantity_badge', function ($row) {
                    $class = $row->isLowStock() ? 'bg-danger' : 'bg-success';
                    return '<span class="badge ' . $class . '">' . $row->quantity . '</span>';
                })
                ->addColumn('value', function ($row) {
                    return number_format($row->getTotalValue(), 2) . ' MAD';
                })
                ->rawColumns(['quantity_badge'])
                ->make(true);
        }
    }
}