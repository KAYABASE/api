<?php

namespace Fabrikod\Repository\Tests;

use Fabrikod\Repository\Contracts\Filter;
use Fabrikod\Repository\Contracts\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FilterTest extends TestCase
{
    /** @test */
    public function it_can_push_a_filter()
    {
        $repository = $this->repository();

        $repository->pushFilter(DummyFilter::class);

        $this->assertInstanceOf(DummyFilter::class, $repository->getFilters()[0]);
    }

    /** @test */
    public function it_can_skipable_the_filter()
    {
        $repository = $this->repository();

        $repository->pushFilter(DummyFilter::class);

        $repository->create(['name' => 'dummy', 'email' => 'dummy@test.com']);
        $repository->create(['name' => 'filtered name']);

        request()->merge(['name' => 'filtered name']);

        $this->assertNotContains('dummy', $repository->all(['name'])->map(fn($p) => $p->name)->flatten()->toArray());

        $repository->skipFilters();

        $this->assertContains('dummy', $repository->all(['name'])->map(fn($p) => $p->name)->flatten()->toArray());
    }

    /** @test */
    public function a_filter_can_works()
    {
        $repository = $this->repository();

        $repository->pushFilter(DummyFilter::class);

        $repository->create(['name' => 'dummy', 'email' => 'dummy@test.com']);

        $repository->create(['name' => 'filtered name', 'email' => 'filter@test.com']);

        request()->merge(['name' => 'filtered name']);

        $this->assertEquals(1, $repository->count());
        $this->assertEquals('filtered name', $repository->first()->name);
        $this->assertEquals('filter@test.com', $repository->first()->email);
        $this->assertEquals(['filter@test.com'], $repository->pluck('email')->toArray());
        $this->assertEquals([['email' => 'filter@test.com']], $repository->get('email')->toArray());
    }
}

class DummyFilter implements Filter
{
    public function __construct(public Request $request)
    {
        # code...
    }

    public function apply(Builder $query, Repository $repository)
    {
        $query->where('name', $this->request->name);
    }
}
