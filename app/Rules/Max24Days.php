<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class Max24Days implements Rule
{
    private $date_start;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($date_start)
    {
        $this->date_start = $date_start;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check difference between dates
        $days = Carbon::parse($this->date_start)->diffInDays(Carbon::parse($value));
        return $days <= 24 and $days >= 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The date range must be between 1 and 24 days';
    }
}
