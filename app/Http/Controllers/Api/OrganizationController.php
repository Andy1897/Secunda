<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $query = Organization::query()->with(['building', 'activities']);

        if ($request->filled('name')) {
            $name = $request->string('name');
            $query->where('name', 'like', "%{$name}%");
        }

        return response()->json($query->paginate(20));
    }

    public function show($id)
    {
        $org = Organization::with(['building', 'activities', 'phones'])->findOrFail((int) $id);

        return response()->json($org);
    }

    public function byBuilding(Building $building)
    {
        $orgs = Organization::with(['activities', 'phones'])
            ->where('building_id', $building->id)
            ->paginate(20);

        return response()->json($orgs);
    }

    public function byActivity(Activity $activity)
    {
        $ids = $this->collectActivityIdsRecursive($activity, 3 - $activity->level + 1);
        $orgs = Organization::with(['building', 'phones'])
            ->whereHas('activities', function ($q) use ($ids) {
                $q->whereIn('activities.id', $ids);
            })
            ->paginate(20);

        return response()->json($orgs);
    }

    public function search(Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string'],
            'activity' => ['nullable', 'integer', 'exists:activities,id'],
        ]);

        $query = Organization::query()->with(['building', 'activities']);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->string('name').'%');
        }

        if ($request->filled('activity')) {
            $activity = Activity::findOrFail((int) $request->input('activity'));
            $ids = $this->collectActivityIdsRecursive($activity, 3 - $activity->level + 1);
            $query->whereHas('activities', fn ($q) => $q->whereIn('activities.id', $ids));
        }

        return response()->json($query->paginate(20));
    }

    public function near(Request $request)
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'radius_km' => ['required_without:rect', 'numeric', 'min:0'],
        ]);

        $lat = (float) $data['lat'];
        $lng = (float) $data['lng'];
        $radius = (float) ($data['radius_km'] ?? 0);

        // грубая фильтрация по bbox на основе 1 градуса ~ 111км
        $delta = $radius / 111.0;
        $orgs = Organization::with(['building', 'activities'])
            ->whereHas('building', function ($q) use ($lat, $lng, $delta) {
                $q->whereBetween('latitude', [$lat - $delta, $lat + $delta])
                    ->whereBetween('longitude', [$lng - $delta, $lng + $delta]);
            })
            ->paginate(20);

        return response()->json($orgs);
    }

    public function inRect(Request $request)
    {
        $data = $request->validate([
            'lat_min' => ['required', 'numeric', 'between:-90,90'],
            'lat_max' => ['required', 'numeric', 'between:-90,90'],
            'lng_min' => ['required', 'numeric', 'between:-180,180'],
            'lng_max' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $orgs = Organization::with(['building', 'activities'])
            ->whereHas('building', function ($q) use ($data) {
                $q->whereBetween('latitude', [min($data['lat_min'], $data['lat_max']), max($data['lat_min'], $data['lat_max'])])
                    ->whereBetween('longitude', [min($data['lng_min'], $data['lng_max']), max($data['lng_min'], $data['lng_max'])]);
            })
            ->paginate(20);

        return response()->json($orgs);
    }

    private function collectActivityIdsRecursive(Activity $root, int $depth): array
    {
        $ids = [$root->id];
        if ($depth <= 1) {
            return $ids;
        }
        foreach ($root->children as $child) {
            $ids = array_merge($ids, $this->collectActivityIdsRecursive($child, $depth - 1));
        }

        return array_values(array_unique($ids));
    }
}
