<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index()
    {
        return view('products.categories');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::with('parent')->withCount('products');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('parent_name', function ($row) {
                    if ($row->parent) {
                        return '<span class="badge badge-sm bg-gradient-info">' . $row->parent->name . '</span>';
                    }
                    return '<span class="badge badge-sm bg-gradient-dark">Parent</span>';
                })
                ->addColumn('full_path', function ($row) {
                    return $row->getFullPath();
                })
                ->addColumn('status_badge', function ($row) {
                    $badge = $row->is_active
                        ? '<span class="badge badge-sm bg-gradient-success">Actif</span>'
                        : '<span class="badge badge-sm bg-gradient-secondary">Inactif</span>';
                    return $badge;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';

                    if (auth()->user()->can('category-edit')) {
                        $btn .= '<a href="' . route('categories.edit', $row->id) . '" class="text-secondary font-weight-bold text-xs me-2 edit-btn" data-id="' . $row->id . '" data-toggle="tooltip" data-original-title="Edit category">
                                    <i class="fas fa-edit"></i>
                                </a>';
                    }

                    if (auth()->user()->can('category-delete')) {
                        $btn .= '<a href="javascript:;" class="text-secondary font-weight-bold text-xs delete-btn" data-id="' . $row->id . '" data-toggle="tooltip" data-original-title="Delete category">
                                    <i class="fas fa-trash"></i>
                                </a>';
                    }

                    return $btn;
                })
                ->rawColumns(['parent_name', 'status_badge', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:categories,code',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $category = Category::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Catégorie créée avec succès',
                'data' => $category
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Category $category)
    {
        // Check if request is AJAX
        if (request()->ajax()) {
            return response()->json($category);
        }

        // For non-AJAX requests, redirect to index
        return redirect()->route('categories.index');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:categories,code,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($category) {
                    // Prevent setting self as parent
                    if ($value == $category->id) {
                        $fail('Une catégorie ne peut pas être son propre parent.');
                    }

                    // Prevent circular reference
                    if ($value) {
                        $parent = Category::find($value);
                        while ($parent) {
                            if ($parent->parent_id == $category->id) {
                                $fail('Cette relation créerait une référence circulaire.');
                                break;
                            }
                            $parent = $parent->parent;
                        }
                    }
                },
            ],
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $category->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Catégorie modifiée avec succès',
                'data' => $category
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Category $category)
    {
        try {
            // Vérifier s'il y a des produits
            if ($category->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer une catégorie contenant des produits'
                ], 400);
            }

            // Vérifier s'il y a des sous-catégories
            if ($category->children()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer une catégorie contenant des sous-catégories'
                ], 400);
            }

            DB::beginTransaction();

            $category->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Catégorie supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get parent categories for dropdown
    public function getParentCategories(Request $request)
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($categories);
    }
}