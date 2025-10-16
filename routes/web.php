<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POSController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\StockTransferController;

Route::get('/', [AuthController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit')
    ->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::middleware(['auth'])->group(function () {
    //profile
    Route::get('/profile', [AuthController::class, 'profile'])
        ->name('profile');

    Route::put('/profile', [AuthController::class, 'updateProfile'])
        ->name('profile.update');

    Route::put('/profile/password', [AuthController::class, 'updatePassword'])
        ->name('profile.password');
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard-view');

    Route::get('/dashboard/sales-chart', [DashboardController::class, 'getSalesChart'])
        ->name('dashboard.sales-chart');

    Route::get('/dashboard/stock-by-warehouse', [DashboardController::class, 'getStockByWarehouse'])
        ->name('dashboard.stock-by-warehouse');

    Route::get('/dashboard/top-customers', [DashboardController::class, 'getTopCustomers'])
        ->name('dashboard.top-customers');

    // Warehouses (Entrepôts)
    Route::middleware(['permission:warehouse-view'])->group(function () {
        Route::get('/warehouses', [WarehouseController::class, 'index'])
            ->name('warehouses.index');

        Route::get('/warehouses/data', [WarehouseController::class, 'getData'])
            ->name('warehouses.data');

        Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show'])
            ->name('warehouses.show');

        Route::get('/warehouses/{warehouse}/stock-data', [WarehouseController::class, 'getStockData'])
            ->name('warehouses.stock-data');
    });

    Route::middleware(['permission:warehouse-create'])->group(function () {
        Route::get('/warehouses/create', [WarehouseController::class, 'create'])
            ->name('warehouses.create');

        Route::post('/warehouses', [WarehouseController::class, 'store'])
            ->name('warehouses.store');
    });

    Route::middleware(['permission:warehouse-edit'])->group(function () {
        Route::get('/warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])
            ->name('warehouses.edit');

        Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])
            ->name('warehouses.update');
    });

    Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])
        ->name('warehouses.destroy')
        ->middleware('permission:warehouse-delete');

    // Categories
    Route::middleware(['permission:category-view'])->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])
            ->name('categories.index');

        Route::get('/categories/data', [CategoryController::class, 'getData'])
            ->name('categories.data');
        Route::get('/categories/parents', [CategoryController::class, 'getParentCategories'])
            ->name('categories.parents');
    });

    Route::middleware(['permission:category-create'])->group(function () {
        Route::get('/categories/create', [CategoryController::class, 'create'])
            ->name('categories.create');

        Route::post('/categories', [CategoryController::class, 'store'])
            ->name('categories.store');
    });

    Route::middleware(['permission:category-edit'])->group(function () {
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
            ->name('categories.edit');

        Route::put('/categories/{category}', [CategoryController::class, 'update'])
            ->name('categories.update');
    });

    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->name('categories.destroy')
        ->middleware('permission:category-delete');

    // Products
    Route::middleware(['permission:product-view'])->group(function () {
        Route::middleware(['permission:product-create'])->group(function () {
            Route::get('/products/create', [ProductController::class, 'create'])
                ->name('products.create');

            Route::post('/products', [ProductController::class, 'store'])
                ->name('products.store');
        });
        Route::get('/products', [ProductController::class, 'index'])
            ->name('products.index');

        Route::delete('products/{product}/featured-image', [ProductController::class, 'deleteFeaturedImage'])->name('products.delete-featured-image');
        Route::delete('products/gallery/{image}', [ProductController::class, 'deleteGalleryImage'])->name('products.delete-gallery-image');
        Route::post('products/{product}/reorder-images', [ProductController::class, 'reorderGalleryImages'])->name('products.reorder-images');
        Route::get('/products/data', [ProductController::class, 'getData'])
            ->name('products.data');

        Route::get('/products/{product}', [ProductController::class, 'show'])
            ->name('products.show');

        Route::get('/products/{product}/price-history', [ProductController::class, 'getPriceHistory'])
            ->name('products.price-history');
    });


    Route::middleware(['permission:product-edit'])->group(function () {
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
            ->name('products.edit');

        Route::put('/products/{product}', [ProductController::class, 'update'])
            ->name('products.update');

        Route::post('/products/{product}/regenerate-barcode', [ProductController::class, 'regenerateBarcode'])
            ->name('products.regenerate-barcode');
    });
    Route::get('/products/{product}/print-barcode', [ProductController::class, 'printBarcode'])
        ->name('products.print-barcode');

    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->name('products.destroy')
        ->middleware('permission:product-delete');

    Route::get('/products/by-warehouse/{warehouseId}', [ProductController::class, 'getByWarehouse'])
        ->name('products.by-warehouse');

    // Suppliers
    Route::middleware(['permission:supplier-view'])->group(function () {
        Route::get('/suppliers', [SupplierController::class, 'index'])
            ->name('suppliers.index');

        Route::get('/suppliers/data', [SupplierController::class, 'getData'])
            ->name('suppliers.data');

        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])
            ->name('suppliers.show');
    });

    Route::middleware(['permission:supplier-create'])->group(function () {
        Route::get('/suppliers/create', [SupplierController::class, 'create'])
            ->name('suppliers.create');

        Route::post('/suppliers', [SupplierController::class, 'store'])
            ->name('suppliers.store');
    });

    Route::middleware(['permission:supplier-edit'])->group(function () {
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])
            ->name('suppliers.edit');

        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])
            ->name('suppliers.update');
    });

    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])
        ->name('suppliers.destroy')
        ->middleware('permission:supplier-delete');

    // Customers
    Route::middleware(['permission:customer-view'])->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])
            ->name('customers.index');

        Route::get('/customers/data', [CustomerController::class, 'getData'])
            ->name('customers.data');

        Route::get('/customers/{customer}', [CustomerController::class, 'show'])
            ->name('customers.show');

        Route::get('/customers/{customer}/credit-schedules', [CustomerController::class, 'getCreditSchedules'])
            ->name('customers.credit-schedules');
    });

    Route::middleware(['permission:customer-create'])->group(function () {
        Route::get('/customers/create', [CustomerController::class, 'create'])
            ->name('customers.create');

        Route::post('/customers', [CustomerController::class, 'store'])
            ->name('customers.store');
    });

    Route::middleware(['permission:customer-edit'])->group(function () {
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])
            ->name('customers.edit');

        Route::put('/customers/{customer}', [CustomerController::class, 'update'])
            ->name('customers.update');
    });

    Route::post('/customers/{customer}/increase-credit', [CustomerController::class, 'increaseCredit'])
        ->name('customers.increase-credit')
        ->middleware('permission:customer-increase-credit');

    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])
        ->name('customers.destroy')
        ->middleware('permission:customer-delete');

    // Purchases
    Route::middleware(['permission:purchase-view'])->group(function () {

        Route::middleware(['permission:purchase-create'])->group(function () {
            Route::get('/purchases/create', [PurchaseController::class, 'create'])
                ->name('purchases.create');

            Route::post('/purchases', [PurchaseController::class, 'store'])
                ->name('purchases.store');
        });
        Route::get('/purchases', [PurchaseController::class, 'index'])
            ->name('purchases.index');

        Route::get('/purchases/data', [PurchaseController::class, 'getData'])
            ->name('purchases.data');

        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])
            ->name('purchases.show');
    });

    Route::middleware(['permission:purchase-edit'])->group(function () {
        Route::get('/purchases/{purchase}/edit', [PurchaseController::class, 'edit'])
            ->name('purchases.edit');

        Route::put('/purchases/{purchase}', [PurchaseController::class, 'update'])
            ->name('purchases.update');
    });

    Route::post('/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])
        ->name('purchases.receive')
        ->middleware('permission:purchase-receive');

    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])
        ->name('purchases.destroy')
        ->middleware('permission:purchase-delete');

    // Sales
    Route::middleware(['permission:sale-view'])->group(function () {

        Route::middleware(['permission:sale-create'])->group(function () {
            Route::get('/sales/create', [SaleController::class, 'create'])
                ->name('sales.create');

            Route::post('/sales', [SaleController::class, 'store'])
                ->name('sales.store');
        });

        Route::middleware(['permission:sale-edit'])->group(function () {
            Route::get('/sales/{sale}/edit', [SaleController::class, 'edit'])
                ->name('sales.edit');

            Route::put('/sales/{sale}', [SaleController::class, 'update'])
                ->name('sales.update');
        });

        Route::get('/sales', [SaleController::class, 'index'])
            ->name('sales.index');

        Route::get('/sales/data', [SaleController::class, 'getData'])
            ->name('sales.data');

        // Must come before {sale} route
        Route::get('/sales/products-by-warehouse', [SaleController::class, 'getProductsByWarehouse'])
            ->name('sales.products-by-warehouse');

        Route::get('/sales/{sale}', [SaleController::class, 'show'])
            ->name('sales.show');
        // PDF routes
        Route::get('/sales/{sale}/pdf', [SaleController::class, 'generatePdf'])
            ->name('sales.pdf');
        Route::get('/sales/{sale}/pdf/download', [SaleController::class, 'downloadPdf'])
            ->name('sales.pdf.download');
        // Actions on sales
        Route::middleware(['permission:sale-validate'])->group(function () {
            Route::post('/sales/{sale}/validate', [SaleController::class, 'validate'])
                ->name('sales.validate');
        });

        Route::middleware(['permission:sale-convert'])->group(function () {
            Route::post('/sales/{sale}/convert', [SaleController::class, 'convert'])
                ->name('sales.convert');
        });

        Route::middleware(['permission:sale-payment'])->group(function () {
            Route::post('/sales/{sale}/add-payment', [SaleController::class, 'addPayment'])
                ->name('sales.add-payment');
        });

        Route::middleware(['permission:sale-delete'])->group(function () {
            Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])
                ->name('sales.destroy');
        });
    });

    Route::middleware(['permission:sale-edit'])->group(function () {
        Route::get('/sales/{sale}/edit', [SaleController::class, 'edit'])
            ->name('sales.edit');

        Route::put('/sales/{sale}', [SaleController::class, 'update'])
            ->name('sales.update');
    });

    Route::post('/sales/{sale}/validate', [SaleController::class, 'validate'])
        ->name('sales.validate')
        ->middleware('permission:sale-validate');

    Route::post('/sales/{sale}/convert', [SaleController::class, 'convert'])
        ->name('sales.convert')
        ->middleware('permission:sale-convert');

    Route::post('/sales/{sale}/add-payment', [SaleController::class, 'addPayment'])
        ->name('sales.add-payment')
        ->middleware('permission:sale-payment');

    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])
        ->name('sales.destroy')
        ->middleware('permission:sale-delete');

    Route::get('/sales/products-by-warehouse', [SaleController::class, 'getProductsByWarehouse'])
        ->name('sales.products-by-warehouse');

    // Stock Transfers
    Route::middleware(['permission:transfer-view'])->group(function () {
        Route::middleware(['permission:transfer-create'])->group(function () {
            Route::get('/stock-transfers/create', [StockTransferController::class, 'create'])
                ->name('stock-transfers.create');

            Route::post('/stock-transfers', [StockTransferController::class, 'store'])
                ->name('stock-transfers.store');
        });
        Route::get('/stock-transfers', [StockTransferController::class, 'index'])
            ->name('stock-transfers.index');

        Route::get('/stock-transfers/data', [StockTransferController::class, 'getData'])
            ->name('stock-transfers.data');

        Route::get('/stock-transfers/{stockTransfer}', [StockTransferController::class, 'show'])
            ->name('stock-transfers.show');
    });


    Route::middleware(['permission:transfer-edit'])->group(function () {
        Route::get('/stock-transfers/{stockTransfer}/edit', [StockTransferController::class, 'edit'])
            ->name('stock-transfers.edit');

        Route::put('/stock-transfers/{stockTransfer}', [StockTransferController::class, 'update'])
            ->name('stock-transfers.update');
    });

    Route::post('/stock-transfers/{stockTransfer}/send', [StockTransferController::class, 'send'])
        ->name('stock-transfers.send')
        ->middleware('permission:transfer-send');

    Route::post('/stock-transfers/{stockTransfer}/receive', [StockTransferController::class, 'receive'])
        ->name('stock-transfers.receive')
        ->middleware('permission:transfer-receive');

    Route::delete('/stock-transfers/{stockTransfer}', [StockTransferController::class, 'destroy'])
        ->name('stock-transfers.destroy')
        ->middleware('permission:transfer-delete');

    // POS (Point of Sale)
    Route::middleware(['permission:pos-access'])->group(function () {
        Route::get('/pos', [POSController::class, 'index'])
            ->name('pos.index');

        Route::get('/pos/screen', [POSController::class, 'screen'])
            ->name('pos.screen');

        Route::get('/pos/products-by-warehouse', [POSController::class, 'getProductsByWarehouse'])
            ->name('pos.products-by-warehouse');

        Route::post('/pos/search-product', [POSController::class, 'searchProduct'])
            ->name('pos.search-product');

        Route::post('/pos/check-stock', [POSController::class, 'checkStock'])
            ->name('pos.check-stock');

        Route::post('/pos/create-sale', [POSController::class, 'createSale'])
            ->name('pos.create-sale');

        Route::get('/pos/print-receipt/{sale}', [POSController::class, 'printReceipt'])
            ->name('pos.print-receipt');

        Route::get('/pos/today-sales', [POSController::class, 'todaySales'])
            ->name('pos.today-sales');
    });

    // Reports
    Route::middleware(['permission:report-view'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');

        // Rapport des ventes
        Route::get('/reports/sales', [ReportController::class, 'salesReport'])
            ->name('reports.sales');

        Route::get('/reports/sales/data', [ReportController::class, 'salesReportData'])
            ->name('reports.sales.data');

        // Rapport des achats
        Route::get('/reports/purchases', [ReportController::class, 'purchasesReport'])
            ->name('reports.purchases');

        // Rapport de stock
        Route::get('/reports/stock', [ReportController::class, 'stockReport'])
            ->name('reports.stock');

        Route::get('/reports/stock/data', [ReportController::class, 'stockReportData'])
            ->name('reports.stock.data');

        // Rapport des créances
        Route::get('/reports/credit', [ReportController::class, 'creditReport'])
            ->name('reports.credit');

        Route::get('/reports/credit/data', [ReportController::class, 'creditReportData'])
            ->name('reports.credit.data');

        // Rapport de profit
        Route::get('/reports/profit', [ReportController::class, 'profitReport'])
            ->name('reports.profit');

        // Rapport financier
        Route::get('/reports/financial', [ReportController::class, 'financialReport'])
            ->name('reports.financial');

        // Rapport des clients
        Route::get('/reports/customers', [ReportController::class, 'customersReport'])
            ->name('reports.customers');

        // Rapport des fournisseurs
        Route::get('/reports/suppliers', [ReportController::class, 'suppliersReport'])
            ->name('reports.suppliers');

        // Rapport des entrepôts
        Route::get('/reports/warehouses', [ReportController::class, 'warehousesReport'])
            ->name('reports.warehouses');

        // Rapport des produits
        Route::get('/reports/products', [ReportController::class, 'productsReport'])
            ->name('reports.products');

        // Export des rapports
        Route::post('/reports/sales/export', [ReportController::class, 'exportSalesReport'])
            ->name('reports.sales.export');
    });
});

//require __DIR__.'/auth.php';