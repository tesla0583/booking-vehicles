<?php

namespace App\Http\Controllers;

use App\Models\UserVehicle;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function booking(Request $request)
    {
        $vehicleId = $request->get('vehicleId');
        $userId = $request->get('userId');

        try {
            DB::transaction(function () use($vehicleId, $userId) {
               $item = Vehicle::query()
                   ->lockForUpdate()
                   ->findOrFail($vehicleId);

               if(!empty($item->user_id)) {
                   return response()->json('You can not to book this vehicle!', 200);
               }
               $item->user_id = $userId;
               $item->save();

               UserVehicle::query()->create([
                  'user_id' => $userId,
                  'vehicle_id' => $vehicleId,
                  'is_active' => true
               ]);
            });
            return response()->json('Vehicle successfully booked', 200);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 200);
        }
    }

    public function unBooking(Request $request)
    {
        $userId = $request->get('userId');
        $vehicleId = $request->get('vehicleId');

        $item = Vehicle::query()
            ->where('user_id', $userId)
            ->first();

        $item->user_id = null;
        $item->save();

        $history = UserVehicle::query()
            ->where('user_id', $userId)
            ->where('vehicle_id', $vehicleId)
            ->orderByDesc('id')
            ->first();

        if(!empty($history->is_active)) {
            $history->is_active = null;
            $history->save();
        }

        return response()->json('Vehicle successfully unBooked', 200);
    }
}
