<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use App\Models\Product;
// use App\Models\Material;

class BarcodeController extends Controller
{
    /**
     * Genera una imagen PNG de un código de barras dado.
     * Diseñado para manejar códigos con prefijos como 'PROD-' o 'MAT-'.
     *
     * @param string $barcode El código de barras completo a generar (ej: PROD-UUID, MAT-UUID).
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function generateImage(string $barcode)
    {

        $entity = null;
        $entityFound = false; // Bandera para saber si encontramos una entidad

        if (Str::startsWith($barcode, 'PROD-')) {
            $entity = Product::where('codigo_barras', $barcode)->first();
            if ($entity) {
                $entityFound = true;
            }
        } elseif (Str::startsWith($barcode, 'MAT-')) {
            // $entity = \App\Models\Material::where('codigo_barras', $barcode)->first();
            if ($entity) {
                $entityFound = true;
            }
        }

        if (!$entityFound) {
            return response()->json(['message' => 'Código de barras no válido o no encontrado'], Response::HTTP_NOT_FOUND);
        }

        try {

            $generator = new BarcodeGeneratorPNG();
            // Puedes ajustar el tamaño (ancho de barra, alto, tamaño de fuente) aquí si es necesario.
            // $barcodeData = $generator->getBarcode($barcode, $generator::TYPE_CODE_128, 2, 60); // Ejemplo: ancho 2, alto 60
            $barcodeData = $generator->getBarcode($barcode, $generator::TYPE_CODE_128);

            return response($barcodeData, Response::HTTP_OK)
                    ->header('Content-Type', 'image/png');

        } catch (\Exception $e) {
            // Registra el error en un log o en la respuesta
            return response()->json([
                'message' => 'Error al generar el código de barras',
                'details' => $e->getMessage(),
                'gd_available' => function_exists('gd_info'),
                'gd_info' => function_exists('gd_info') ? gd_info() : null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
