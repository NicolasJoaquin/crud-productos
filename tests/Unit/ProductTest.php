<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use App\Exceptions\ProductNotFoundException;
use Mockery;

class ProductTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock de File::get() y File::put()
        File::shouldReceive('get')
            ->with(storage_path('products.json'))
            ->andReturn(json_encode([
                [
                    "id" => 1,
                    "title" => "Producto del Challenge",
                    "price" => "2000",
                    "created_at" => "2022-12-13 10:41"
                ],
                [
                    "id" => 2,
                    "title" => "Tuercas de 3/8''",
                    "price" => "50.65",
                    "created_at" => "2024-10-23 20:42"
                ]
            ]));

        File::shouldReceive('put')
            ->with(storage_path('products.json'), Mockery::any())
            ->andReturn(true);
    }

    public function test_get_products()
    {
        $productModel = new Product();
        $products = $productModel->getProducts();

        $this->assertIsArray($products);
        $this->assertCount(2, $products);
        $this->assertEquals('Producto del Challenge', $products[0]['title']);
    }

    public function test_get_product_by_id()
    {
        $productModel = new Product();
        $product = $productModel->getProduct(1);

        $this->assertIsArray($product);
        $this->assertEquals(1, $product['id']);
        $this->assertEquals('Producto del Challenge', $product['title']);
    }

    public function test_get_ProductNotFoundException()
    {
        $this->expectException(ProductNotFoundException::class);

        $productModel = new Product();
        $productModel->getProduct(999);
    }

    public function test_add_product()
    {
        $productModel = new Product();
        $newProductId = $productModel->addProduct([
            'title' => 'Nuevo Producto',
            'price' => '123.45'
        ]);

        $this->assertIsInt($newProductId);
        $this->assertGreaterThan(2, $newProductId);
    }

    public function test_update_product()
    {
        $productModel = new Product();
        $productModel->updateProduct(1, [
            'price' => '2500'
        ]);

        // No se arroja ninguna excepción al hacer update
        $this->assertTrue(true); 
    }

    public function test_update_ProductNotFoundException()
    {
        $this->expectException(ProductNotFoundException::class);

        $productModel = new Product();
        $productModel->updateProduct(999, [
            'price' => '2500'
        ]);
    }

    public function test_delete_product()
    {
        $productModel = new Product();
        $productModel->deleteProduct(1);

        // No se arroja ninguna excepción al hacer delete
        $this->assertTrue(true);
    }

    public function test_delete_ProductNotFoundException()
    {
        $this->expectException(ProductNotFoundException::class);

        $productModel = new Product();
        $productModel->deleteProduct(999);
    }
}
