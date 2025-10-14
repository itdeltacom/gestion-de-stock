<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.index', compact('categories'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::with('category')->select('products.*');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('category_name', function($row){
                    return $row->category->name;
                })
                ->addColumn('total_stock', function($row){
                    $totalStock = $row->getTotalStock();
                    $class = $row->isLowStock() ? 'bg-danger' : 'bg-success';
                    return '<span class="badge '.$class.'">'.$totalStock.'</span>';
                })
                ->addColumn('price_formatted', function($row){
                    return number_format($row->price, 2) . ' MAD';
                })
                ->addColumn('tva_rate_formatted', function($row){
                    return $row->tva_rate . '%';
                })
                ->addColumn('status_badge', function($row){
                    $badge = $row->is_active 
                        ? '<span class="badge bg-success">Actif</span>' 
                        : '<span class="badge bg-danger">Inactif</span>';
                    return $badge;
                })
                ->addColumn('barcode_display', function($row){
                    return $row->barcode ?? '<span class="text-muted">N/A</span>';
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group" role="group">';
                    
                    if (auth()->user()->can('product-view')) {
                        $btn .= '<a href="'.route('products.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    }
                    
                    if (auth()->user()->can('product-edit')) {
                        $btn .= '<a href="'.route('products.edit', $row->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    }
                    
                    if (auth()->user()->can('product-delete')) {
                        $btn .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
                    }
                    
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['total_stock', 'status_badge', 'barcode_display', 'action'])
                ->make(true);
        }
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:products,code',
            'reference' => 'nullable|string|unique:products,reference',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'tva_rate' => 'required|numeric|min:0|max:100',
            'price' => 'required|numeric|min:0',
            'stock_method' => 'required|in:cmup,fifo',
            'alert_stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $product = Product::create($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Produit créé avec succès',
                'data' => $product
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'stocks.warehouse', 'priceHistories.supplier']);
        
        $totalStock = $product->getTotalStock();
        $margin = $product->getMargin();
        $priceWithTVA = $product->getPriceWithTVA();
        
        return view('products.show', compact('product', 'totalStock', 'margin', 'priceWithTVA'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:products,code,'.$product->id,
            'reference' => 'nullable|string|unique:products,reference,'.$product->id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'tva_rate' => 'required|numeric|min:0|max:100',
            'price' => 'required|numeric|min:0',
            'stock_method' => 'required|in:cmup,fifo',
            'alert_stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $product->update($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Produit modifié avec succès',
                'data' => $product
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        try {
            // Vérifier s'il y a du stock
            if ($product->getTotalStock() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer un produit ayant du stock'
                ], 400);
            }
            
            DB::beginTransaction();
            
            $product->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Produit supprimé avec succès'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    public function regenerateBarcode(Product $product)
    {
        try {
            $product->regenerateBarcode();
            
            return response()->json([
                'success' => true,
                'message' => 'Code-barres régénéré avec succès',
                'barcode' => $product->barcode
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la régénération: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPriceHistory(Request $request, Product $product)
    {
        if ($request->ajax()) {
            $data = $product->priceHistories()->with('supplier');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('supplier_name', function($row){
                    return $row->supplier ? $row->supplier->name : 'N/A';
                })
                ->addColumn('purchase_price_formatted', function($row){
                    return number_format($row->purchase_price, 2) . ' MAD';
                })
                ->addColumn('sale_price_formatted', function($row){
                    return number_format($row->sale_price, 2) . ' MAD';
                })
                ->addColumn('margin', function($row){
                    return number_format($row->getMargin(), 2) . '%';
                })
                ->addColumn('date_formatted', function($row){
                    return $row->date->format('d/m/Y');
                })
                ->make(true);
        }
    }

    public function getByWarehouse(Request $request, $warehouseId)
    {
        if ($request->ajax()) {
            $products = Product::whereHas('stocks', function($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId)
                      ->where('quantity', '>', 0);
            })
            ->with(['stocks' => function($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            }, 'category'])
            ->where('is_active', true)
            ->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        }
    }
}