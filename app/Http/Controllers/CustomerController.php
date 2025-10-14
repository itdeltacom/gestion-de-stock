<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customers.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::select('*');
            
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
                ->addColumn('credit_info', function($row){
                    $remaining = $row->getRemainingCredit();
                    $class = $remaining < 1000 ? 'bg-danger' : 'bg-success';
                    return '<span class="badge '.$class.'">'.number_format($remaining, 2).' MAD</span>';
                })
                ->addColumn('credit_usage', function($row){
                    if ($row->credit_limit > 0) {
                        $percentage = ($row->current_credit / $row->credit_limit) * 100;
                        $class = $percentage > 80 ? 'danger' : ($percentage > 50 ? 'warning' : 'success');
                        return '<div class="progress">
                                    <div class="progress-bar bg-'.$class.'" role="progressbar" style="width: '.$percentage.'%" aria-valuenow="'.$percentage.'" aria-valuemin="0" aria-valuemax="100">
                                        '.number_format($percentage, 1).'%
                                    </div>
                                </div>';
                    }
                    return 'N/A';
                })
                ->addColumn('status_badge', function($row){
                    $badge = $row->is_active 
                        ? '<span class="badge bg-success">Actif</span>' 
                        : '<span class="badge bg-danger">Inactif</span>';
                    return $badge;
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group" role="group">';
                    
                    if (auth()->user()->can('customer-view')) {
                        $btn .= '<a href="'.route('customers.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    }
                    
                    if (auth()->user()->can('customer-edit')) {
                        $btn .= '<a href="'.route('customers.edit', $row->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    }
                    
                    if (auth()->user()->can('customer-increase-credit')) {
                        $btn .= '<button type="button" class="btn btn-sm btn-warning increase-credit-btn" data-id="'.$row->id.'" data-current="'.$row->credit_limit.'"><i class="fas fa-arrow-up"></i></button>';
                    }
                    
                    if (auth()->user()->can('customer-delete')) {
                        $btn .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
                    }
                    
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['type_badge', 'credit_info', 'credit_usage', 'status_badge', 'action'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:customers,code',
            'type' => 'required|in:individuel,societe',
            'ice' => 'nullable|string|max:20',
            'raison_sociale' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'credit_limit' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $customer = Customer::create($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Client créé avec succès',
                'data' => $customer
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Customer $customer)
    {
        $customer->load(['sales.creditSchedules']);
        $totalSales = $customer->getTotalSales();
        $remainingCredit = $customer->getRemainingCredit();
        
        // Récupérer toutes les échéances du client
        $allSchedules = \App\Models\CreditSchedule::where('customer_id', $customer->id)
            ->with('sale')
            ->orderBy('due_date', 'asc')
            ->get();
        
        $overdueSchedules = $allSchedules->filter(function($schedule) {
            return $schedule->isOverdue();
        });
        
        return view('customers.show', compact('customer', 'totalSales', 'remainingCredit', 'allSchedules', 'overdueSchedules'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:customers,code,'.$customer->id,
            'type' => 'required|in:individuel,societe',
            'ice' => 'nullable|string|max:20',
            'raison_sociale' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'credit_limit' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $customer->update($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Client modifié avec succès',
                'data' => $customer
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Customer $customer)
    {
        try {
            // Vérifier s'il y a des ventes
            if ($customer->sales()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer un client ayant des ventes'
                ], 400);
            }
            
            DB::beginTransaction();
            
            $customer->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Client supprimé avec succès'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    public function increaseCredit(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'new_credit_limit' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            
            if ($validated['new_credit_limit'] < $customer->current_credit) {
                return response()->json([
                    'success' => false,
                    'message' => 'La nouvelle limite ne peut pas être inférieure au crédit utilisé'
                ], 400);
            }
            
            $customer->credit_limit = $validated['new_credit_limit'];
            $customer->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Limite de crédit augmentée avec succès',
                'data' => $customer
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'augmentation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCreditSchedules(Request $request, Customer $customer)
    {
        if ($request->ajax()) {
            $data = \App\Models\CreditSchedule::where('customer_id', $customer->id)
                ->with('sale')
                ->select('credit_schedules.*');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('sale_reference', function($row){
                    return $row->sale->reference;
                })
                ->addColumn('amount_formatted', function($row){
                    return number_format($row->amount, 2) . ' MAD';
                })
                ->addColumn('paid_amount_formatted', function($row){
                    return number_format($row->paid_amount, 2) . ' MAD';
                })
                ->addColumn('remaining_formatted', function($row){
                    return number_format($row->getRemainingAmount(), 2) . ' MAD';
                })
                ->addColumn('due_date_formatted', function($row){
                    return $row->due_date->format('d/m/Y');
                })
                ->addColumn('status_badge', function($row){
                    $badges = [
                        'en_attente' => '<span class="badge bg-warning">En attente</span>',
                        'paye' => '<span class="badge bg-success">Payé</span>',
                        'retard' => '<span class="badge bg-danger">En retard</span>',
                    ];
                    return $badges[$row->status] ?? $row->status;
                })
                ->addColumn('days_overdue', function($row){
                    if ($row->isOverdue()) {
                        return '<span class="text-danger">'.$row->getDaysOverdue().' jours</span>';
                    }
                    return '<span class="text-success">-</span>';
                })
                ->rawColumns(['status_badge', 'days_overdue'])
                ->make(true);
        }
    }
}