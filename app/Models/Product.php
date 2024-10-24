<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Exceptions\JsonReadException;
use App\Exceptions\JsonWriteException;
use App\Exceptions\ProductNotFoundException;

class Product
{
    public function getProducts(string $filter = null)
    {
        try {
            $products = json_decode(File::get(storage_path('products.json')), true);
            // Filtrar productos
            if(!empty($filter)) {
                $products = array_filter($products, function ($product) use ($filter) {
                    return stripos($product['title'], $filter) !== false ||
                        stripos($product['price'], $filter) !== false ||
                        stripos($product['created_at'], $filter) !== false;
                });
            }

            return $products;
        } catch (Exception $e) {
            Log::error('Hubo un error al leer el archivo de productos.', [
                'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
            ]);
            throw new JsonReadException('Error al leer el archivo de productos.');
        }
    }

    public function getProduct(int $id)
    {
        $products = $this->getProducts();
        foreach($products as $product) {
            if($product['id'] === $id)
                return $product;
        }
        Log::warning("No se encontró el producto buscado.", [
            'ID de producto' => $id,
            'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
        ]);
        throw new ProductNotFoundException("Producto con ID $id no encontrado.");
    }

    public function saveProducts(array $products)
    {
        try {
            File::put(storage_path('products.json'), json_encode($products, JSON_PRETTY_PRINT));
        } catch (Exception $e) {
            Log::error('Hubo un error al guardar información en el archivo de productos.', [
                'productos' => $products,
                'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
            ]);
            throw new JsonWriteException('Error al guardar los productos.');
        }
    }

    public function addProduct(array $newProduct)
    {
        $products = $this->getProducts();

        $newProduct['id'] = end($products)['id'] + 1;
        $newProduct['created_at'] = date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i'); ;

        $products[] = $newProduct;
        $this->saveProducts($products);

        Log::info('Producto creado exitosamente', [
            'producto' => $newProduct,
            'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
        ]);
        return $newProduct['id'];
    }

    public function updateProduct(int $id, array $newData)
    {
        $products = $this->getProducts();
        $newProduct = null;
        foreach($products as &$product) {
            if($product['id'] === $id)
                $newProduct = $product = array_merge($product, $newData);
        }
        $this->saveProducts($products);

        if(!$newProduct) {
            Log::warning("Intento de editar un producto que no existe.", [
                'ID de producto' => $id,
                'Nuevos datos' => $newData,
                'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
            ]);
            throw new ProductNotFoundException("Producto con ID $id no encontrado. No se pudo editar.");    
        }
        Log::info('Producto modificado exitosamente', [
            'producto' => $newProduct,
            'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
        ]);
    }

    public function deleteProduct(int $id)
    {
        $products = $this->getProducts();
        $foundProduct = null;

        $newProducts = array_filter($products, function($product) use ($id, &$foundProduct) {
            if($product['id'] === $id) {
                $foundProduct = $product;
                return false;
            }
            return true;
        });

        if(!$foundProduct) {
            Log::warning("Intento de eliminar un producto que no existe.", [
                'ID de producto' => $id,
                'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
            ]);
            throw new ProductNotFoundException("Producto con ID $id no encontrado. No se pudo eliminar.");
        }

        $this->saveProducts($newProducts);
        Log::info('Producto eliminado exitosamente', [
            'ID de producto' => $id,
            'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
        ]);
    }
}
