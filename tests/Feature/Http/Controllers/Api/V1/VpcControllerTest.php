<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VpcControllerTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();
    }

    public function test_list_vpcs()
    {
        $response = $this->getJson(route('api.v1.vpcs'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'vpc_id',
                        'state',
                        'cidr_block',
                    ],
                ],
            ]);
    }
}
