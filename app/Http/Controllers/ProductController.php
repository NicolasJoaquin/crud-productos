<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\InvalidRequestException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Support\Facades\Log;
use Mockery\MockInterface;
use Mockery\LegacyMockInterface;

class ProductController extends Controller
{
    protected $productModel;

    public function __construct(Product | MockInterface | LegacyMockInterface $productModel = null)
    {
        if(!empty($productModel)) 
            $this->productModel = $productModel;
        else
            $this->productModel = new Product();
    }

    public function getProducts(Request $request)
    {
        return $this->handleRequest(function () use ($request) {
            $validator = Validator::make($request->all(), [
                'search' => 'sometimes|string|max:500',
                'per_page' => 'sometimes|numeric',
                'page' => 'sometimes|numeric',
            ]);

            if($validator->fails()) {
                throw new InvalidRequestException('Filtros inválidos: ' . $validator->errors()->first());
            }

            // Parámetros de búsqueda y paginación
            $search = $request->input('search', null);
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            
            $products = $this->productModel->getProducts($search);
            // Paginación
            $total = count($products);
            $offset = ($page - 1) * $perPage;
            $pagedProducts = array_slice($products, $offset, $perPage);

            $response = [
                'data' => array_values($pagedProducts), // array_values para resetear los índices del array
                'current_page' => (int)$page,
                'per_page' => (int)$perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ];
            return response()->json($response);

        });
    }

    public function getProduct(int $id)
    {
        return $this->handleRequest(function () use ($id) {
            return response()->json($this->productModel->getProduct($id));
        });
    }

    public function store(Request $request)
    {
        return $this->handleRequest(function () use ($request) {
            if(auth()->user->role !== 'admin') {
                Log::error('Usuario sin permiso para agregar productos.', [
                    'usuario' => auth()->user,
                    'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
                ]);
                throw new UnauthorizedException('El usuario no tiene permiso para agregar productos.');
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:500',
                'price' => 'required|numeric',
            ]);

            if($validator->fails()) {
                throw new InvalidRequestException('Datos inválidos: ' . $validator->errors()->first());
            }

            $id = $this->productModel->addProduct($request->all());
            return response()->json([
                'data' => [
                    'id' => $id,
                ],
                'message' => 'Producto agregado con éxito'
            ]);
        });
    }

    public function update(int $id, Request $request)
    {
        return $this->handleRequest(function () use ($id, $request) {
            if(auth()->user->role !== 'admin') {
                Log::error('Usuario sin permiso para editar productos.', [
                    'usuario' => auth()->user,
                    'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
                ]);
                throw new UnauthorizedException('El usuario no tiene permiso para editar productos.');
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:500',
                'price' => 'sometimes|numeric',
            ]);

            if($validator->fails()) {
                throw new InvalidRequestException('Datos inválidos: ' . $validator->errors()->first());
            }

            $this->productModel->updateProduct($id, $request->all());
            return response()->json(['message' => 'Producto actualizado con éxito']);
        });
    }

    public function delete(int $id)
    {
        return $this->handleRequest(function () use ($id) {
            if(auth()->user->role !== 'admin') {
                Log::error('Usuario sin permiso para eliminar productos.', [
                    'usuario' => auth()->user,
                    'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
                ]);
                throw new UnauthorizedException('El usuario no tiene permiso para eliminar productos.');
            }

            $this->productModel->deleteProduct($id);
            return response()->json(['message' => 'Producto eliminado con éxito']);
        });
    }
}
