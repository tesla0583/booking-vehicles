<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @OA\Schema(
 *      title="Booking Vehicles Request",
 *      description="Booking data",
 *      type="object",
 *      required={"vehicle_id", "user_id"},
 *      @OA\Property(
 * 		    property="vehicle_id",
 * 		    type="int",
 * 		    example=1
 * 	    ),
 *     @OA\Property(
 * 		    property="user_id",
 * 		    type="int",
 * 		    example=1
 * 	    )
 * )
 */
class BookingVehiclesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'vehicle_id' => 'required|exists:transports,id',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
