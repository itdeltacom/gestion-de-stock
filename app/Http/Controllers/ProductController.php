<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Jobs\ProcessProductImages;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

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
                ->addColumn('image_thumb', function ($row) {
                    $imageUrl = $this->imageService->getImageUrl($row->featured_image, 'thumbnail');
                    return '<img src="' . $imageUrl . '" class="rounded" style="width: 50px; height: 50px; object-fit: cover;" alt="' . $row->name . '">';
                })
                ->addColumn('category_name', function ($row) {
                    return $row->category->name;
                })
                ->addColumn('total_stock', function ($row) {
                    $totalStock = $row->getTotalStock();
                    $class = $row->isLowStock() ? 'bg-danger' : 'bg-success';
                    return '<span class="badge ' . $class . '">' . $totalStock . '</span>';
                })
                ->addColumn('price_formatted', function ($row) {
                    return number_format($row->price, 2) . ' MAD';
                })
                ->addColumn('tva_rate_formatted', function ($row) {
                    return $row->tva_rate . '%';
                })
                ->addColumn('status_badge', function ($row) {
                    $badge = $row->is_active
                        ? '<span class="badge bg-success">Actif</span>'
                        : '<span class="badge bg-danger">Inactif</span>';
                    return $badge;
                })
                ->addColumn('barcode_display', function ($row) {
                    return $row->barcode ?? '<span class="text-muted">N/A</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';

                    if (auth()->user()->can('product-view')) {
                        $btn .= '<a href="' . route('products.show', $row->id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    }

                    if (auth()->user()->can('product-edit')) {
                        $btn .= '<a href="' . route('products.edit', $row->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    }

                    if (auth()->user()->can('product-delete')) {
                        $btn .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['image_thumb', 'total_stock', 'status_badge', 'barcode_display', 'action'])
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
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Create product first
            $product = Product::create($validated);

            // Store temp files and get FULL PATHS for the job
            $featuredTempPath = null;
            $galleryTempPaths = [];

            if ($request->hasFile('featured_image')) {
                $tempStoragePath = $request->file('featured_image')->store('temp', 'local');
                // Convert to full filesystem path
                $featuredTempPath = Storage::disk('local')->path($tempStoragePath);
            }

            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $tempStoragePath = $image->store('temp', 'local');
                    // Convert to full filesystem path
                    $galleryTempPaths[] = Storage::disk('local')->path($tempStoragePath);
                }
            }

            // Dispatch job for async processing with FULL PATHS
            if ($featuredTempPath || !empty($galleryTempPaths)) {
                ProcessProductImages::dispatch($product->id, $featuredTempPath, $galleryTempPaths);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Produit créé avec succès. Les images sont en cours de traitement.',
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
        $product->load(['category', 'stocks.warehouse', 'priceHistories.supplier', 'images']);

        $totalStock = $product->getTotalStock();
        $margin = $product->getMargin();
        $priceWithTVA = $product->getPriceWithTVA();

        return view('products.show', compact('product', 'totalStock', 'margin', 'priceWithTVA'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $product->load('images');
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:products,code,' . $product->id,
            'reference' => 'nullable|string|unique:products,reference,' . $product->id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'tva_rate' => 'required|numeric|min:0|max:100',
            'price' => 'required|numeric|min:0',
            'stock_method' => 'required|in:cmup,fifo',
            'alert_stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Handle featured image upload
            if ($request->hasFile('featured_image')) {
                // Delete old featured image
                if ($product->featured_image) {
                    $this->imageService->deleteImage($product->featured_image);
                }

                $paths = $this->imageService->processFeaturedImage($request->file('featured_image'), 92);
                $validated['featured_image'] = $paths['original'];
            }

            $product->update($validated);

            // Handle gallery images upload
            if ($request->hasFile('gallery_images')) {
                $currentMaxOrder = $product->images()->max('sort_order') ?? -1;
                foreach ($request->file('gallery_images') as $index => $image) {
                    $paths = $this->imageService->processGalleryImage($image, 90);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $paths['original'],
                        'sort_order' => $currentMaxOrder + $index + 1,
                    ]);
                }
            }

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

    public function deleteFeaturedImage(Product $product)
    {
        try {
            if ($product->featured_image) {
                $this->imageService->deleteImage($product->featured_image);
                $product->featured_image = null;
                $product->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Image à la une supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteGalleryImage(ProductImage $image)
    {
        try {
            $this->imageService->deleteImage($image->image_path);
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reorderGalleryImages(Request $request, Product $product)
    {
        try {
            $order = $request->input('order', []);

            foreach ($order as $index => $imageId) {
                ProductImage::where('id', $imageId)
                    ->where('product_id', $product->id)
                    ->update(['sort_order' => $index]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ordre des images mis à jour'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réorganisation: ' . $e->getMessage()
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

    public function getByWarehouse(Request $request, $warehouseId)
    {
        if ($request->ajax()) {
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
    }

    public function printBarcode(Product $product)
    {
        if (!$product->barcode) {
            return response()->json([
                'success' => false,
                'message' => 'Ce produit n\'a pas de code-barres'
            ], 400);
        }

        return view('products.print-barcode', compact('product'));
    }
}