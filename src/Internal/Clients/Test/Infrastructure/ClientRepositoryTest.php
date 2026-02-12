<?php

namespace Internal\Clients\Test\Infrastructure;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Internal\Clients\Infrastructure\Repositories\ClientRepository;
use Tests\TestCase;

class ClientRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ClientRepository $repository;

    private function validClientData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Acme Inc',
            'email' => 'contact@acme.test',
            'phone' => '123456',
            'status' => 'active',
            'tags' => ['vip'],
        ], $overrides);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ClientRepository();
    }

    public function test_get_table_name_returns_clients(): void
    {
        $this->assertSame('clients', $this->repository->getTableName());
    }

    public function test_it_can_create_a_client(): void
    {
        $data = $this->validClientData();

        $id = $this->repository->create($data);

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        $this->assertDatabaseHas('clients', [
            'id' => $id,
            'name' => 'Acme Inc',
            'email' => 'contact@acme.test',
            'status' => 'active',
        ]);
    }

    public function test_it_can_find_client_by_id(): void
    {
        $id = $this->repository->create($this->validClientData());

        $client = $this->repository->findById($id);

        $this->assertNotNull($client);
        $this->assertSame((int) $id, (int) $client->id);
        $this->assertSame('Acme Inc', $client->name);
        $this->assertSame('contact@acme.test', $client->email);
        $this->assertSame('123456', $client->phone);
        $this->assertSame('active', $client->status);
    }

    public function test_it_returns_null_when_client_not_found_by_id(): void
    {
        $client = $this->repository->findById(999);
        $this->assertNull($client);
    }

    public function test_exist_by_id_returns_true_when_client_exists(): void
    {
        $id = $this->repository->create($this->validClientData());

        $this->assertTrue($this->repository->existById($id));
    }

    public function test_exist_by_id_returns_false_when_client_not_found(): void
    {
        $this->assertFalse($this->repository->existById(999));
    }

    public function test_exist_email_returns_true_when_email_exists(): void
    {
        $this->repository->create($this->validClientData(['email' => 'unique@test.com']));

        $this->assertTrue($this->repository->existEmail('unique@test.com'));
    }

    public function test_exist_email_returns_false_when_email_not_found(): void
    {
        $this->assertFalse($this->repository->existEmail('nonexistent@test.com'));
    }

    public function test_it_can_find_all_clients_without_filters(): void
    {
        $this->repository->create($this->validClientData(['name' => 'A', 'email' => 'a@test.com']));
        $this->repository->create($this->validClientData(['name' => 'B', 'email' => 'b@test.com']));

        $clients = $this->repository->findAll();

        $this->assertIsArray($clients);
        $this->assertCount(2, $clients);
        $this->assertInstanceOf(\stdClass::class, $clients[0]);
        $this->assertNotEmpty($clients[0]->name);
        $this->assertNotEmpty($clients[0]->status);
    }

    public function test_it_can_list_clients_with_status_filter(): void
    {
        $this->repository->create($this->validClientData(['name' => 'A', 'email' => 'a@test.com', 'status' => 'active']));
        $this->repository->create($this->validClientData(['name' => 'B', 'email' => 'b@test.com', 'status' => 'inactive']));

        $activeClients = $this->repository->findAll(['status' => 'active']);

        $this->assertCount(1, $activeClients);
        $this->assertSame('active', $activeClients[0]->status);
        $this->assertSame('A', $activeClients[0]->name);
    }

    public function test_it_can_update_a_client(): void
    {
        $id = $this->repository->create($this->validClientData(['name' => 'Old']));

        $updated = $this->repository->update($id, $this->validClientData([
            'name' => 'New',
            'email' => 'new@acme.test',
            'phone' => '789',
            'status' => 'inactive',
            'tags' => ['premium'],
        ]));

        $this->assertTrue($updated);
        $this->assertDatabaseHas('clients', [
            'id' => $id,
            'name' => 'New',
            'email' => 'new@acme.test',
            'status' => 'inactive',
        ]);
    }

    public function test_it_can_delete_a_client(): void
    {
        $id = $this->repository->create($this->validClientData());

        $this->assertDatabaseHas('clients', ['id' => $id]);

        $deleted = $this->repository->delete($id);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('clients', ['id' => $id]);
    }

    public function test_it_returns_false_when_deleting_nonexistent_client(): void
    {
        $this->assertFalse($this->repository->delete(999));
    }
}

