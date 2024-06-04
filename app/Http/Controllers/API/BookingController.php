<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Location;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    /**
     * Books blocks for given dates
     *
     */
    public function book(Location $location, BookingRequest $request, BookingService $bookingService)
    {
        // check if blocks are available for that day
        if(!$bookingService->checkIfBlocksAvailable2($location->id, $request))
            return response(['message' => 'We are sorry. You are little late. Someone just booked available blocks. Try different dates.'], 409);

        // begin to booking
        $bookingService->bookBlocks2($location->id, $request);

        return response(['message' => 'You have successfully booked blocks.']);



        /**
         * Normalized version, deprecated

        // check if blocks are available for that day
        if(!$bookingService->checkIfBlocksAvailable($location->id, $request))
            return response(['message' => 'We are sorry. You are little late. Someone just booked available blocks. Try different dates.'], 409);

        // begin to booking
        $bookingService->bookBlocks($location->id, $request);

        return response(['message' => 'You have successfully booked blocks.']);

         */
    }
}
