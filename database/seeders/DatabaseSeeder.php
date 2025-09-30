<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use App\Models\OrganizationPhone;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $food = Activity::create(['name' => 'Еда', 'parent_id' => null, 'level' => 1]);
        $meat = Activity::create(['name' => 'Мясная продукция', 'parent_id' => $food->id, 'level' => 2]);
        $milk = Activity::create(['name' => 'Молочная продукция', 'parent_id' => $food->id, 'level' => 2]);

        $cars = Activity::create(['name' => 'Автомобили', 'parent_id' => null, 'level' => 1]);
        $trucks = Activity::create(['name' => 'Грузовые', 'parent_id' => $cars->id, 'level' => 2]);
        $carsLight = Activity::create(['name' => 'Легковые', 'parent_id' => $cars->id, 'level' => 2]);
        $parts = Activity::create(['name' => 'Запчасти', 'parent_id' => $carsLight->id, 'level' => 3]);
        $accessories = Activity::create(['name' => 'Аксессуары', 'parent_id' => $carsLight->id, 'level' => 3]);

        $b1 = Building::create(['address' => 'г. Москва, ул. Ленина 1, офис 3', 'latitude' => 55.7558, 'longitude' => 37.6173]);
        $b2 = Building::create(['address' => 'г. Москва, ул. Тверская 10', 'latitude' => 55.7640, 'longitude' => 37.6050]);

        $o1 = Organization::create(['name' => 'ООО Рога и Копыта', 'building_id' => $b1->id]);
        $o2 = Organization::create(['name' => 'Мясная лавка', 'building_id' => $b2->id]);
        $o3 = Organization::create(['name' => 'Молочная ферма', 'building_id' => $b2->id]);

        $o1->activities()->sync([$food->id]);
        $o2->activities()->sync([$meat->id]);
        $o3->activities()->sync([$milk->id]);

        OrganizationPhone::insert([
            ['organization_id' => $o1->id, 'phone' => '2-222-222', 'created_at' => now(), 'updated_at' => now()],
            ['organization_id' => $o1->id, 'phone' => '3-333-333', 'created_at' => now(), 'updated_at' => now()],
            ['organization_id' => $o1->id, 'phone' => '8-923-666-13-13', 'created_at' => now(), 'updated_at' => now()],
            ['organization_id' => $o2->id, 'phone' => '+7 (495) 111-22-33', 'created_at' => now(), 'updated_at' => now()],
            ['organization_id' => $o3->id, 'phone' => '+7 (495) 444-55-66', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
