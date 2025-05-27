<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Exceptions\ProductCannotBeDeletedException;


class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;

        $this->middleware('auth:api');
        $this->middleware('permission:manage-products');
    }

    /**
     * Muestra una lista paginada de productos, aplicando filtros desde los parámetros de la request.
     * Lee parámetros de paginación y filtro, los mapea y los pasa al servicio.
     * Endpoint: GET /api/productos?status={active|all}&categoria_id={id}&nombre={string}&talla={string}&per_page={cantidad}&page={numero}
     * Protegido por JWT y requiere permiso 'manage-products'.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $filters = $request->only([
            'status', 'categoria_id',
            'nombre', 'talla'
        ]);

        $perPage = $request->query('per_page', 20);
        $page = $request->query('page');

        $productos = $this->productService->listProducts(
            filters: $filters,
            perPage: (int) $perPage,
            page: $page ? (int) $page : null
        );

        return ProductResource::collection($productos);
    }

    /**
     * Muestra los detalles de un producto específico.
     * Endpoint: GET /api/productos/{id}
     * Protegido por JWT y requiere permiso 'manage-products'.
     */
    public function show(int $id): ProductResource|JsonResponse
    {
        $product = $this->productService->getProduct($id, $onlyActive = false);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $product->load(['categoria', 'color']);

        return new ProductResource($product);
    }


    /**
     * Almacena un nuevo producto.
     * Endpoint: POST /api/productos
     * Protegido por JWT y requiere permiso 'manage-products'.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());
        return response()->json(new ProductResource($product), Response::HTTP_CREATED);
    }

    /**
     * Actualiza un producto existente.
     * Endpoint: PUT/PATCH /api/productos/{id}
     * Protegido por JWT y requiere permiso 'manage-products'.
     */
    public function update(UpdateProductRequest $request, int $id): ProductResource|JsonResponse
    {
        $product = $this->productService->updateProduct($id, $request->validated());

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return new ProductResource($product, Response::HTTP_OK);
    }

    /**
     * "Elimina" un producto cambiando su estado a false.
     * Endpoint: DELETE /api/productos/{id}
     * Protegido por JWT y requiere permiso 'manage-products'.
     */
    public function destroy(int $id): JsonResponse
    {
        try {

            $success = $this->productService->deleteProduct($id);

            if (!$success) {
                return response()->json(['message' => 'Producto no encontrado o ya inactivo'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Producto desactivado con éxito'], Response::HTTP_OK);

        } catch (ProductCannotBeDeletedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al desactivar el producto', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     * Restaura un producto cambiando su estado a true.
     * Endpoint: POST /api/productos/{id}/restore
     * Protegido por JWT y requiere permiso 'manage-products'.
     */
    public function restore(int $id): JsonResponse
    {
        try {

            $success = $this->productService->restoreProduct($id);

            if (!$success) {
                return response()->json(['message' => 'Producto no encontrado o ya activo'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Producto restaurado con éxito'], Response::HTTP_OK);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Se produjo un error al restaurar el producto', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
