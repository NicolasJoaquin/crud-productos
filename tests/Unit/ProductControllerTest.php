<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Http\Request;
use Mockery;

class ProductControllerTest extends TestCase
{
    protected $productController;
    protected $mockProductModel;

    public function setUp(): void
    {
        parent::setUp();
        // Mock del modelo Product
        $this->mockProductModel = Mockery::mock(Product::class);
        $this->productController = new ProductController($this->mockProductModel);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_products_returns_products()
    {
        $request = new Request([
            'search' => 'Product 1',
            'per_page' => 2,
            'page' => 1
        ]);

        // Mock de getProducts
        $this->mockProductModel
            ->shouldReceive('getProducts')
            ->once()
            ->with('Product 1')
            ->andReturn([
                ['id' => 1, 'title' => 'Product 1', 'price' => 10],
                ['id' => 2, 'title' => 'Product 2', 'price' => 20],
            ]);

        $response = $this->productController->getProducts($request);
        $responseArray = json_decode($response->getContent(), true);
        $this->assertEquals(2, $responseArray['total']);
        $this->assertEquals(1, $responseArray['current_page']);
        $this->assertCount(2, $responseArray['data']);
    }

    public function test_get_product_returns_product()
    {
        // Mock de getProduct
        $this->mockProductModel
            ->shouldReceive('getProduct')
            ->once()
            ->with(1)
            ->andReturn(['id' => 1, 'title' => 'Product 1', 'price' => 10]);

        $response = $this->productController->getProduct(1);

        $responseArray = json_decode($response->getContent(), true);
        $this->assertEquals(1, $responseArray['id']);
        $this->assertEquals('Product 1', $responseArray['title']);
    }
}
