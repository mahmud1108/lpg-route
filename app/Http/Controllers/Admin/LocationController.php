<?php

namespace App\Http\Controllers\Admin;

use App\Helper\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GetData;
use App\Http\Requests\Admin\StoreLocationRequest;
use App\Http\Requests\Admin\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;
use Nette\Utils\Random;

class LocationController extends Controller
{
    public function add_location(StoreLocationRequest $request)
    {
        $data = $request->validated();

        $location = new Location;
        $location->location_id = 'Loc_' . Random::generate(10, '0-9a-z');
        $location->address = $data['address'];
        $location->holiday = $data['holiday'];
        $location->open_hours = $data['open_hours'];
        $location->inventory = $data['inventory'];
        $location->latitude = $data['latitude'];
        $location->longitude = $data['longitude'];
        $location->photo = FileHelper::instance()->upload($data['photo'], 'location');
        $location->admin_id = auth()->user()->admin_id;
        $location->save();

        return new LocationResource($location);
    }

    public function update($location_id, UpdateLocationRequest $request)
    {
        $data = $request->validated();

        $location = GetData::data_check(Location::where('location_id', $location_id)
            ->where('admin_id', auth()->user()->admin_id)->first());

        if (isset($data['address'])) {
            $location->address = $data['address'];
        }

        if (isset($data['holiday'])) {
            $location->holiday = $data['holiday'];
        }

        if (isset($data['open_hours'])) {
            $location->open_hours = $data['open_hours'];
        }

        if (isset($data['inventory'])) {
            $location->inventory = $data['inventory'];
        }

        if (isset($data['latitude'])) {
            $location->latitude = $data['latitude'];
        }

        if (isset($data['longitude'])) {
            $location->longitude = $data['longitude'];
        }

        if (isset($data['photo'])) {
            FileHelper::instance()->delete($location->photo);
            $location->photo = FileHelper::instance()->upload($data['photo'], 'location');
        }

        $location->save();
        return new LocationResource($location);
    }

    public function get_all(Request $request)
    {
        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $location = Location::query()->where('admin_id', auth()->user()->admin_id)
            ->paginate(perPage: $per_page, page: $page);

        return LocationResource::collection($location);
    }

    public function get_one($location_id)
    {
        $location = GetData::data_check(Location::find($location_id));

        return new LocationResource($location);
    }

    public function delete($location_id)
    {
        $location = GetData::data_check(Location::where('location_id', $location_id)
            ->where('admin_id', auth()->user()->admin_id)->first());

        $location->delete();

        return response()->json([
            'status' => true
        ]);
    }
}
