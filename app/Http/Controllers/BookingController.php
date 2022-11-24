<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingVehiclesRequest;
use App\Models\UserVehicle;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * @OA\Post(
     *      path="/booking",
     *      operationId="bookingVehicles",
     *      tags={"Booking"},
     *      summary="Booking",
     *      description="Booking Vehicles",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/BookingVehiclesRequest")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Success",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      )
     * )
     */

    public function booking(BookingVehiclesRequest $request)
    {
        $vehicleId = $request->get('vehicle_id');
        $userId = $request->get('user_id');

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
            return response()->json('Vehicle successfully booked', 204);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @OA\Post(
     *      path="/unBooking",
     *      operationId="bookingVehicles",
     *      tags={"unBooking"},
     *      summary="unBooking",
     *      description="unBooking Vehicles",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/BookingVehiclesRequest")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Success",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      )
     * )
     */
    public function unBooking(BookingVehiclesRequest $request)
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

        return response()->json('Vehicle successfully unBooked', 204);
    }
}
