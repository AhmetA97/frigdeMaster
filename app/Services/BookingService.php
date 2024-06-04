<?php

namespace App\Services;

use App\Models\Block;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * Checks if there are enough available blocks for given volume and dates
     * if enough, return true, else false
     *
     * @returns boolean
     */
    public function checkIfBlocksAvailable2($location_id, $rq): bool
    {
        // get all booked blocks ids
        $block_ids = $this->getBookedBlockIds($location_id, $rq);

        // get volume of available blocks
        $volume = DB::table('blocks')
            ->selectRaw("sum(volume) as sum")
            ->where('location_id', $location_id)
            ->whereNotIn('id', $block_ids)
            ->first();

        // if we have enough volume, return true
        if ($volume->sum >= $rq->volume)
            return true;
        // else return false
        return false;
    }


    /**
     * Get available blocks for this booking
     * Books the blocks
     *
     */
    public function bookBlocks2($location_id, $rq)
    {
        // get all booked blocks ids
        $block_ids = $this->getBookedBlockIds($location_id, $rq);

        // get all available blocks
        $blocks = Block::where('location_id', $location_id)
            ->whereNotIn('id', $block_ids)
            ->limit(ceil($rq->volume/2))
            ->orderBy('id')
            ->get();

        // book the blocks
        Booking::create([
            'location_id' => $location_id,
            'user_id' => 1, // in ideal situation, this should be auth()->id()
            'temperature' => $rq->temperature,
            'blocks' => $blocks->count(), // or we can use ceil($rq->volume/2)
            'volume' => $rq->volume,
            'date_start' => $rq->date_start,
            'date_end' => $rq->date_end,
            'price' => $this->calculatePrice($rq),
            'access_code' => $this->generateAccessCode(),
            'block_ids' => implode(',', $blocks->pluck('id')->toArray()),
        ]);
    }


    /**
     * gets booked block ids in given date ranges
     *
     * @param $location_id
     * @param $rq
     * @return array
     */
    private function getBookedBlockIds($location_id, $rq):array
    {
        // get all booked block_ids
        $bookings = DB::table('bookings')
            ->selectRaw('GROUP_CONCAT(DISTINCT block_ids) as block_ids')
            ->where('location_id', $location_id)
            ->whereIn('status', ['booked', 'in_use'])
            ->whereRaw("(date_start between '{$rq->date_start}' and '{$rq->date_end}'
                or date_end between '{$rq->date_start}' and '{$rq->date_end}')")
            ->first();

        // remove duplicates and return
        return array_unique(explode(',', $bookings->block_ids));
    }


    /**
     * Calculates the price of booking
     *
     * @param $rq
     * @return int
     */
    private function calculatePrice($rq):int
    {
        return intval(Carbon::parse($rq->date_start)
                ->diffInDays(Carbon::parse($rq->date_end)) * ceil($rq->volume) * 100);
    }


    /**
     * Generates access code with given length
     *
     * @param int $length
     * @return string
     */
    private function generateAccessCode(int $length = 12):string
    {
        // declare variables
        $alphabet = '123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $accessCode = '';
        $alphabetLength = strlen($alphabet) - 1;

        // for length
        for ($i = 0; $i < $length; $i++) {
            // get random character from alphabet string
            $accessCode .= $alphabet[rand(0, $alphabetLength)];
        }

        return $accessCode;
    }


    /**
     * Checks if there are enough available blocks for given volume and dates
     * if enough, return true, else false
     *
     * @returns boolean
     */
    public function checkIfBlocksAvailable($location_id, $rq): bool
    {
        // get available blocks sum
        $volume = DB::table('blocks')
            ->selectRaw("sum(volume) as sum")
            ->where('location_id', $location_id)
            ->whereNotIn('blocks.id', function ($query) use ($location_id, $rq) {
                return $query->from('block_booking')
                    ->select('block_id')
                    ->join('bookings', 'bookings.id', '=', 'block_booking.booking_id')
                    ->where('bookings.location_id', $location_id)
                    ->whereIn('bookings.status', ['booked', 'in_use'])
                    ->whereBetween('date_start', [$rq->date_start, $rq->date_end])
                    ->orWhereBetween('date_end', [$rq->date_start, $rq->date_end]);
            })->first();

        // if we have enough volume, return true
        if ($volume->sum >= $rq->volume)
            return true;
        // else return false
        return false;
    }


    /**
     * Get available blocks for this booking
     * Books the blocks
     *
     */
    public function bookBlocks($location_id, $rq)
    {
        // get blocks to be booked
        $blocks = Block::where('location_id', $location_id)
            ->whereNotIn('blocks.id', function ($query) use ($location_id, $rq) {
                return $query->from('block_booking')
                    ->select('block_id')
                    ->join('bookings', 'bookings.id', '=', 'block_booking.booking_id')
                    ->where('bookings.location_id', $location_id)
                    ->whereIn('bookings.status', ['booked', 'in_use'])
                    ->whereBetween('date_start', [$rq->date_start, $rq->date_end])
                    ->orWhereBetween('date_end', [$rq->date_start, $rq->date_end]);})
            ->limit(ceil($rq->volume/2))
            ->orderBy('id')
            ->get();


        // book the blocks
        $booking = Booking::create([
            'location_id' => $location_id,
            'user_id' => 1, // in ideal situation, this should be auth()->id()
            'temperature' => $rq->temperature,
            'blocks' => $blocks->count(), // or we can use ceil($rq->volume/2)
            'volume' => $rq->volume,
            'date_start' => $rq->date_start,
            'date_end' => $rq->date_end,
            'price' => $this->calculatePrice($rq),
            'access_code' => $this->generateAccessCode(),
        ]);

        // sync bookings and blocks
        $booking->blocks()->sync($blocks, false);
    }
}
