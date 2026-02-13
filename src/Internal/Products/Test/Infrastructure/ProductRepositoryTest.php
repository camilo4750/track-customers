<?php

namespace Internal\Products\Test\Infrastructure;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Internal\Products\Infrastructure\Repositories\ProductRepository;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProductRepository $repository;

    private function validProductData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Producto Test',
            'sku' => 'SKU-TEST-001',
            'price' => 99.99,
            'category' => 'Electrónica',
        ], $overrides);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductRepository();
    }

    public function test_get_table_name_returns_products(): void
    {
        $this->assertSame('products', $this->repository->getTableName());
    }

    public function test_it_can_create_a_product(): void
    {
        $data = $this->validProductData();

        $id = $this->repository->create($data);

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        $this->assertDatabaseHas('products', [
            'id' => $id,
            'name' => 'Producto Test',
            'sku' => 'SKU-TEST-001',
            'price' => 99.99,
            'category' => 'Electrónica',
        ]);
    }

    public function test_it_can_find_product_by_id(): void
    {
        $id = $this->repository->create($this->validProductData());

        $product = $this->repository->findById($id);

        $this->assertNotNull($product);
        $this->assertSame((int) $id, (int) $product->id);
        $this->assertSame('Producto Test', $product->name);
        $this->assertSame('SKU-TEST-001', $product->sku);
        $this->assertEquals(99.99, (float) $product->price);
        $this->assertSame('Electrónica', $product->category);
    }

    public function test_it_returns_null_when_product_not_found_by_id(): void
    {
        $product = $this->repository->findById(999);
        $this->assertNull($product);
    }

    public function test_exist_by_id_returns_true_when_product_exists(): void
    {
        $id = $this->repository->create($this->validProductData());

        $this->assertTrue($this->repository->existById($id));
    }

    public function test_exist_by_id_returns_false_when_product_not_found(): void
    {
        $this->assertFalse($this->repository->existById(999));
    }

    public function test_exist_sku_returns_true_when_sku_exists(): void
    {
        $this->repository->create($this->validProductData(['sku' => 'SKU-UNIQUE-001']));

        $this->assertTrue($this->repository->existSku('SKU-UNIQUE-001'));
    }

    public function test_exist_sku_returns_false_when_sku_not_found(): void
    {
        $this->assertFalse($this->repository->existSku('SKU-NONEXISTENT'));
    }

    public function test_exist_sku_excludes_id_when_exclude_id_provided(): void
    {
        $id1 = $this->repository->create($this->validProductData(['sku' => 'SKU-ONE']));
        $id2 = $this->repository->create($this->validProductData(['sku' => 'SKU-TWO']));

        $this->assertTrue($this->repository->existSku('SKU-TWO', $id1));
        $this->assertFalse($this->repository->existSku('SKU-ONE', $id1));
    }

    public function test_it_can_find_all_products_without_filters(): void
    {
        $this->repository->create($this->validProductData(['name' => 'A', 'sku' => 'SKU-A']));
        $this->repository->create($this->validProductData(['name' => 'B', 'sku' => 'SKU-B']));

        $products = $this->repository->findAll();

        $this->assertIsArray($products);
        $this->assertCount(2, $products);
        $this->assertInstanceOf(\stdClass::class, $products[0]);
        $this->assertNotEmpty($products[0]->name);
        $this->assertNotEmpty($products[0]->sku);
    }

    public function test_it_can_list_products_with_category_filter(): void
    {
        $this->repository->create($this->validProductData(['name' => 'A', 'sku' => 'SKU-A', 'category' => 'Electrónica']));
        $this->repository->create($this->validProductData(['name' => 'B', 'sku' => 'SKU-B', 'category' => 'Hogar']));

        $electronicProducts = $this->repository->findAll(['category' => 'Electrónica']);

        $this->assertCount(1, $electronicProducts);
        $this->assertSame('Electrónica', $electronicProducts[0]->category);
        $this->assertSame('A', $electronicProducts[0]->name);
    }

    public function test_it_can_update_a_product(): void
    {
        $id = $this->repository->create($this->validProductData(['name' => 'Old']));

        $updated = $this->repository->update($id, $this->validProductData([
            'name' => 'New',
            'sku' => 'SKU-NEW',
            'price' => 149.99,
            'category' => 'Hogar',
        ]));

        $this->assertTrue($updated);
        $this->assertDatabaseHas('products', [
            'id' => $id,
            'name' => 'New',
            'sku' => 'SKU-NEW',
            'price' => 149.99,
            'category' => 'Hogar',
        ]);
    }

    public function test_it_can_delete_a_product(): void
    {
        $id = $this->repository->create($this->validProductData());

        $this->assertDatabaseHas('products', ['id' => $id]);

        $deleted = $this->repository->delete($id);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('products', ['id' => $id]);
    }

    public function test_it_returns_false_when_deleting_nonexistent_product(): void
    {
        $this->assertFalse($this->repository->delete(999));
    }
}
