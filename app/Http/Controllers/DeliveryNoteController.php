<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\DeliveryNoteDetail;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryNoteController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->get();
        return view('delivery-notes.index', compact('warehouses', 'customers'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = DeliveryNote::with(['customer', 'warehouse', 'user'])
                ->select('delivery_notes.*');

            // Filters
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            if ($request->has('warehouse_id') && $request->warehouse_id != '') {
                $query->where('warehouse_id', $request->warehouse_id);
            }

            if ($request->has('customer_id') && $request->customer_id != '') {
                $query->where('customer_id', $request->customer_id);
            }

            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('delivery_date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('delivery_date', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->name;
                })
                ->addColumn('warehouse_name', function ($row) {
                    return $row->warehouse->name;
                })
                ->addColumn('delivery_date_formatted', function ($row) {
                    return $row->delivery_date->format('d/m/Y');
                })
                ->addColumn('status_badge', function ($row) {
                    return $row->status_badge;
                })
                ->addColumn('quantities', function ($row) {
                    $ordered = $row->getTotalQuantityOrdered();
                    $delivered = $row->getTotalQuantityDelivered();
                    $class = $ordered === $delivered ? 'success' : 'warning';
                    return "<span class='badge bg-{$class}'>{$delivered}/{$ordered}</span>";
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';

                    if (auth()->user()->can('delivery-note-view')) {
                        $btn .= '<a href="' . route('delivery-notes.show', $row->id) . '" class="btn btn-sm btn-info" title="Voir"><i class="fas fa-eye"></i></a>';
                        $btn .= '<a href="' . route('delivery-notes.pdf', $row->id) . '" target="_blank" class="btn btn-sm btn-secondary" title="PDF"><i class="fas fa-file-pdf"></i></a>';
                    }

                    if (auth()->user()->can('delivery-note-edit') && $row->status !== 'livre' && $row->status !== 'annule') {
                        $btn .= '<a href="' . route('delivery-notes.edit', $row->id) . '" class="btn btn-sm btn-primary" title="Modifier"><i class="fas fa-edit"></i></a>';
                    }

                    if (auth()->user()->can('delivery-note-delete') && $row->status === 'en_attente') {
                        $btn .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" title="Supprimer"><i class="fas fa-trash"></i></button>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'quantities', 'action'])
                ->make(true);
        }
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $sales = Sale::where('status', 'valide')->whereDoesntHave('deliveryNote')->get();

        return view('delivery-notes.create', compact('customers', 'warehouses', 'sales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'sale_id' => 'nullable|exists:sales,id',
            'delivery_date' => 'required|date',
            'delivery_address' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'driver_name' => 'nullable|string',
            'vehicle' => 'nullable|string',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity_ordered' => 'required|integer|min:1',
            'products.*.quantity_delivered' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $deliveryNote = DeliveryNote::create([
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'sale_id' => $validated['sale_id'] ?? null,
                'user_id' => auth()->id(),
                'delivery_date' => $validated['delivery_date'],
                'delivery_address' => $validated['delivery_address'],
                'contact_person' => $validated['contact_person'],
                'contact_phone' => $validated['contact_phone'],
                'driver_name' => $validated['driver_name'],
                'vehicle' => $validated['vehicle'],
                'notes' => $validated['notes'],
                'status' => 'en_attente',
            ]);

            foreach ($validated['products'] as $item) {
                DeliveryNoteDetail::create([
                    'delivery_note_id' => $deliveryNote->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_delivered' => $item['quantity_delivered'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bon de livraison créé avec succès',
                'data' => $deliveryNote
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['customer', 'warehouse', 'user', 'sale', 'details.product']);
        return view('delivery-notes.show', compact('deliveryNote'));
    }

    public function edit(DeliveryNote $deliveryNote)
    {
        if ($deliveryNote->status === 'livre' || $deliveryNote->status === 'annule') {
            return redirect()->route('delivery-notes.index')
                ->with('error', 'Impossible de modifier un BL livré ou annulé');
        }

        $deliveryNote->load('details.product');
        $customers = Customer::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('delivery-notes.edit', compact('deliveryNote', 'customers', 'warehouses'));
    }

    public function update(Request $request, DeliveryNote $deliveryNote)
    {
        if ($deliveryNote->status === 'livre' || $deliveryNote->status === 'annule') {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de modifier un BL livré ou annulé'
            ], 400);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'delivery_date' => 'required|date',
            'delivery_address' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'driver_name' => 'nullable|string',
            'vehicle' => 'nullable|string',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity_ordered' => 'required|integer|min:1',
            'products.*.quantity_delivered' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $deliveryNote->update([
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'delivery_date' => $validated['delivery_date'],
                'delivery_address' => $validated['delivery_address'],
                'contact_person' => $validated['contact_person'],
                'contact_phone' => $validated['contact_phone'],
                'driver_name' => $validated['driver_name'],
                'vehicle' => $validated['vehicle'],
                'notes' => $validated['notes'],
            ]);

            // Delete old details
            $deliveryNote->details()->delete();

            // Create new details
            foreach ($validated['products'] as $item) {
                DeliveryNoteDetail::create([
                    'delivery_note_id' => $deliveryNote->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_delivered' => $item['quantity_delivered'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bon de livraison modifié avec succès',
                'data' => $deliveryNote
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(DeliveryNote $deliveryNote)
    {
        if ($deliveryNote->status !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Seuls les BL en attente peuvent être supprimés'
            ], 400);
        }

        try {
            $deliveryNote->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bon de livraison supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAsDelivered(Request $request, DeliveryNote $deliveryNote)
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string',
            'recipient_signature' => 'nullable|string', // Base64 image
        ]);

        try {
            $signaturePath = null;

            if ($request->has('recipient_signature')) {
                // Save signature
                $signature = $request->recipient_signature;
                $signature = str_replace('data:image/png;base64,', '', $signature);
                $signature = str_replace(' ', '+', $signature);
                $signatureName = 'signature_' . $deliveryNote->reference . '_' . time() . '.png';
                \Storage::disk('public')->put('signatures/' . $signatureName, base64_decode($signature));
                $signaturePath = 'signatures/' . $signatureName;
            }

            $deliveryNote->markAsDelivered($validated['recipient_name'], $signaturePath);

            return response()->json([
                'success' => true,
                'message' => 'Bon de livraison marqué comme livré'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generatePdf(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['customer', 'warehouse', 'user', 'details.product']);

        $pdf = Pdf::loadView('delivery-notes.pdf', compact('deliveryNote'));

        return $pdf->stream('BL_' . $deliveryNote->reference . '.pdf');
    }

    public function downloadPdf(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['customer', 'warehouse', 'user', 'details.product']);

        $pdf = Pdf::loadView('delivery-notes.pdf', compact('deliveryNote'));

        return $pdf->download('BL_' . $deliveryNote->reference . '.pdf');
    }

    public function createFromSale(Sale $sale)
    {
        if ($sale->status !== 'valide') {
            return redirect()->back()->with('error', 'Seules les ventes validées peuvent générer un BL');
        }

        if ($sale->hasDeliveryNote()) {
            return redirect()->route('delivery-notes.show', $sale->deliveryNote->id)
                ->with('info', 'Un bon de livraison existe déjà pour cette vente');
        }

        $sale->load(['customer', 'warehouse', 'details.product']);

        return view('delivery-notes.create-from-sale', compact('sale'));
    }
}