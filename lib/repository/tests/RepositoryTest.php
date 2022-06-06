<?php

namespace Fabrikod\Repository\Tests;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\WithFaker;

class RepositoryTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function it_ability_to_create()
    {
        $repository = $this->repository();

        $repository->create(['name' => 'test']);

        $this->assertDatabaseHas('repository_test_model', ['name' => 'test']);
    }

    /** @test */
    public function it_ability_to_update()
    {
        $repository = $this->repository();

        $resource = $repository->create(['name' => 'test']);

        $this->assertDatabaseHas('repository_test_model', ['name' => 'test']);

        $repository->update(['name' => 'test2'], $resource->id);

        $this->assertDatabaseHas('repository_test_model', [
            'id' => $resource->id,
            'name' => 'test2'
        ]);
    }

    /** @test */
    public function it_ability_to_delete()
    {
        $repository = $this->repository();

        $resource = $repository->create(['name' => 'test']);

        $this->assertDatabaseHas('repository_test_model', ['name' => 'test']);

        $repository->delete($resource->id);

        $this->assertDatabaseMissing('repository_test_model', ['id' => $resource->id]);
    }

    /** @test */
    public function it_ability_to_update_or_create()
    {
        $repository = $this->repository();

        $resource = $repository->updateOrCreate(['email' => 'test@test.com'], ['name' => 'test2']);

        $this->assertDatabaseHas('repository_test_model', ['name' => 'test2', 'email' => 'test@test.com']);

        $this->assertEquals($resource->id, 1);

        $resource = $repository->updateOrCreate(['email' => 'test@test.com'], ['name' => 'Updated name']);

        $this->assertDatabaseHas('repository_test_model', ['name' => 'Updated name', 'email' => 'test@test.com']);
        $this->assertEquals($resource->id, 1);
    }

    /** @test */
    public function it_ability_to_pluck()
    {
        $repository = $this->repository();

        $repository->create(['name' => 'test', 'email' => 'test@test.com']);

        $this->assertEquals(['test'], $repository->pluck('name')->toArray());
        $this->assertEquals(['test@test.com'], $repository->pluck('email')->toArray());
    }

    /** @test */
    public function it_ability_to_pagination()
    {
        $repository = $this->repository();
        foreach (range(1, 5) as $i) {
            $repository->create([

                'name' => $this->faker->name,
                'email' => $this->faker->unique()->email,
            ]);
        }

        $this->assertEquals(5, $repository->paginate()->total());
        $this->assertEquals(config('repository.pagination.limit'), $repository->paginate()->perPage());

        request()->merge(['perPage' => 2]);
        // Request parameter
        $this->assertEquals(2, $repository->paginate()->perPage());
    }

    /** @test */
    public function it_ability_to_get_count()
    {
        $repository = $this->repository();

        $this->assertEquals(0, $repository->count());

        $repository->create(['name' => 'test']);

        $this->assertEquals(1, $repository->count());
    }

    /** @test */
    public function it_ability_to_get_all()
    {
        $repository = $this->repository();

        $this->assertEmpty($repository->all());
        $this->assertEmpty($repository->get());

        $repository->create(['name' => 'test']);

        $this->assertNotEmpty($resources = $repository->all());

        $this->assertArrayHasKey('name', $resources[0]->toArray());
        $this->assertContains('test', $resources[0]->toArray());

        // Same to get method
        $this->assertNotEmpty($resources = $repository->get());

        $this->assertArrayHasKey('name', $resources[0]->toArray());
        $this->assertContains('test', $resources[0]->toArray());

        // By columns
        $this->assertNotEmpty($resources = $repository->get(['id']));

        $this->assertArrayHasKey('id', $resources[0]->toArray());
        $this->assertArrayNotHasKey('name', $resources[0]->toArray());
        $this->assertNotContains('test', $resources[0]->toArray());
    }

    /** @test */
    public function it_ability_to_not_found_by_id()
    {
        $repository = $this->repository();

        $this->expectException(ModelNotFoundException::class);

        $repository->find(1);
    }

    /** @test */
    public function it_ability_to_get_by_id()
    {
        $repository = $this->repository();

        $repository->create(['name' => 'test']);

        $this->assertNotEmpty($resource = $repository->find(1));

        $this->assertEquals('test', $resource->name);
    }
}
