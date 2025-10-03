<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function withKey(array $headers = []): array
    {
        return array_merge(['X-API-Key' => config('app.api_key')], $headers);
    }

    public function test_api_key_required(): void
    {
        $this->getJson('/api/v1/activities')->assertStatus(401);
    }

    public function test_list_activities_tree(): void
    {
        $resp = $this->getJson('/api/v1/activities', $this->withKey())
            ->assertOk()
            ->json();

        // есть хотя бы один корневой элемент и у него есть дети (ограничение до 3 уровней)
        $this->assertIsArray($resp);
        $this->assertNotEmpty($resp);
        $hasChildren = false;
        foreach ($resp as $node) {
            if (($node['parent_id'] ?? null) === null && ! empty($node['children'])) {
                $hasChildren = true;
                break;
            }
        }
        $this->assertTrue($hasChildren);
    }

    public function test_get_organization_by_id(): void
    {
        $org = Organization::first();
        $this->getJson("/api/v1/organizations/{$org->id}", $this->withKey())
            ->assertOk()
            ->assertJsonPath('id', $org->id)
            ->assertJsonPath('name', $org->name);
    }

    public function test_search_organizations_by_name(): void
    {
        $sample = Organization::first();
        $needle = mb_substr($sample->name, 0, 3);
        $this->getJson('/api/v1/organizations?name='.urlencode($needle), $this->withKey())
            ->assertOk()
            ->assertJsonFragment(['name' => $sample->name]);
    }

    public function test_list_organizations_by_building(): void
    {
        $building = Building::first();
        $this->getJson("/api/v1/buildings/{$building->id}/organizations", $this->withKey())
            ->assertOk();
    }

    public function test_list_organizations_by_activity_with_nested(): void
    {
        $activity = Activity::whereNull('parent_id')->first();
        $this->getJson("/api/v1/activities/{$activity->id}/organizations", $this->withKey())
            ->assertOk();
    }

    public function test_near_radius_search(): void
    {
        $b = Building::first();
        $this->getJson('/api/v1/organizations/near?lat='.($b->latitude).'&lng='.($b->longitude).'&radius_km=2', $this->withKey())
            ->assertOk();
    }

    public function test_in_rect_search(): void
    {
        $b = Building::first();
        $latMin = $b->latitude - 0.01; $latMax = $b->latitude + 0.01;
        $lngMin = $b->longitude - 0.01; $lngMax = $b->longitude + 0.01;
        $this->getJson('/api/v1/organizations/in-rect?lat_min='.$latMin.'&lat_max='.$latMax.'&lng_min='.$lngMin.'&lng_max='.$lngMax, $this->withKey())
            ->assertOk();
    }
}
