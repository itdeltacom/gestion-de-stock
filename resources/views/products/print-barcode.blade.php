<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code-Barres - {{ $product->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: auto;
                margin: 5mm;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .print-container {
            background: white;
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }

        .header h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .barcode-item {
            text-align: center;
            padding: 25px;
            border: 2px dashed #ddd;
            margin-bottom: 20px;
            page-break-inside: avoid;
            background: #fafafa;
            border-radius: 8px;
        }

        .product-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .product-code {
            font-size: 12px;
            color: #888;
            margin-bottom: 15px;
        }

        .product-price {
            font-size: 24px;
            font-weight: bold;
            color: #2196F3;
            margin: 15px 0;
        }

        .barcode-svg {
            margin: 15px auto;
            display: block;
        }

        .barcode-number {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            color: #666;
            margin-top: 8px;
            letter-spacing: 2px;
        }

        .print-buttons {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
        }

        .btn {
            padding: 12px 30px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
            font-weight: 600;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-print {
            background: #2196F3;
            color: white;
        }

        .btn-print:hover {
            background: #1976D2;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(33, 150, 243, 0.3);
        }

        .btn-close {
            background: #666;
            color: white;
        }

        .btn-close:hover {
            background: #444;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .quantity-selector {
            margin: 20px 0 30px 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            color: white;
            text-align: center;
        }

        .quantity-selector label {
            font-weight: bold;
            margin-right: 15px;
            font-size: 16px;
        }

        .quantity-selector input {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            width: 100px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .quantity-selector button {
            padding: 10px 25px;
            margin-left: 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .quantity-selector button:hover {
            background: #45a049;
            transform: scale(1.05);
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .info-box p {
            color: #1976D2;
            font-size: 14px;
            line-height: 1.6;
        }

        @media screen and (max-width: 768px) {
            .print-container {
                padding: 15px;
            }

            .quantity-selector {
                padding: 15px;
            }

            .quantity-selector label,
            .quantity-selector input,
            .quantity-selector button {
                display: block;
                margin: 10px auto;
            }

            .btn {
                display: block;
                width: 100%;
                margin: 10px 0;
            }
        }
    </style>
</head>

<body>
    <div class="print-container">
        <div class="header no-print">
            <h1><i class="fas fa-barcode"></i> Impression Code-Barres</h1>
            <p>{{ $product->name }}</p>
        </div>

        <div class="no-print info-box">
            <p>
                <i class="fas fa-info-circle"></i>
                <strong>Instructions:</strong> Sélectionnez le nombre d'étiquettes à imprimer et cliquez sur "Générer".
                Ensuite, utilisez le bouton "Imprimer" ou Ctrl+P pour lancer l'impression.
            </p>
        </div>

        <div class="no-print quantity-selector">
            <label for="quantity"><i class="fas fa-layer-group"></i> Nombre d'étiquettes :</label>
            <input type="number" id="quantity" value="1" min="1" max="100">
            <button onclick="generateBarcodes()">
                <i class="fas fa-sync-alt"></i> Générer
            </button>
        </div>

        <div id="barcodesContainer"></div>

        <div class="print-buttons no-print">
            <button class="btn btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
            <button class="btn btn-close" onclick="window.close()">
                <i class="fas fa-times"></i> Fermer
            </button>
        </div>
    </div>

    <script>
        const productData = {
            name: "{{ $product->name }}",
            code: "{{ $product->code }}",
            barcode: "{{ $product->barcode }}",
            price: "{{ number_format($product->getPriceWithTVA(), 2) }}"
        };

        function generateBarcodes() {
            const quantity = parseInt(document.getElementById('quantity').value) || 1;
            const container = document.getElementById('barcodesContainer');
            container.innerHTML = '';

            if (quantity > 100) {
                alert('Le nombre maximum d\'étiquettes est de 100');
                document.getElementById('quantity').value = 100;
                return;
            }

            if (quantity < 1) {
                alert('Le nombre minimum d\'étiquettes est de 1');
                document.getElementById('quantity').value = 1;
                return;
            }

            for (let i = 0; i < quantity; i++) {
                const barcodeItem = document.createElement('div');
                barcodeItem.className = 'barcode-item';
                barcodeItem.innerHTML = `
                    <div class="product-name">${productData.name}</div>
                    <div class="product-code">Code: ${productData.code}</div>
                    <svg class="barcode-svg barcode-${i}"></svg>
                    <div class="barcode-number">${productData.barcode}</div>
                    <div class="product-price">${productData.price} DH TTC</div>
                `;
                container.appendChild(barcodeItem);

                // Générer le code-barres EAN-13
                try {
                    JsBarcode(`.barcode-${i}`, productData.barcode, {
                        format: "EAN13",
                        width: 2,
                        height: 100,
                        displayValue: false,
                        margin: 10,
                        fontSize: 14,
                        textMargin: 5,
                        background: "#fafafa",
                        lineColor: "#000000"
                    });
                } catch (error) {
                    console.error('Erreur lors de la génération du code-barres:', error);
                    barcodeItem.innerHTML = `
                        <div class="product-name">${productData.name}</div>
                        <div style="color: red; padding: 20px;">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Erreur: Code-barres invalide (${productData.barcode})
                        </div>
                    `;
                }
            }
        }

        // Générer un code-barres par défaut au chargement
        window.onload = function () {
            generateBarcodes();
        };

        // Permettre la génération avec la touche Entrée
        document.getElementById('quantity').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                generateBarcodes();
            }
        });
    </script>
</body>

</html>