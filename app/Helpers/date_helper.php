<?php

declare(strict_types=1);

if (!function_exists('date_intervals')) {
    /**
     * Generate an array of dates between two given dates.
     *
     * Iterates from the start date to the end date based on a specified interval
     * and returns a sequential array of dates in 'Y-m-d' format.
     *
     * @param string $start_date The starting date (parsable by strtotime).
     * @param string $end_date   The ending date (parsable by strtotime).
     * @param int    $interval   The step interval in days (default is 1).
     * @return array<string>     An array of formatted date strings.
     */
    function date_intervals(string $start_date, string $end_date, int $interval = 1): array
    {
        $dates = [];
        $start_date = strtotime($start_date);
        $end_date = strtotime($end_date);
        while ($start_date <= $end_date) {
            $dates[] = date('Y-m-d', $start_date);
            $start_date = strtotime("+$interval day", $start_date);
        }
        return $dates;
    }
}

if (!function_exists('time_intervals')) {
    /**
     * Generate an array of times between two given times.
     *
     * Iterates from the start time to the end time based on a specified interval
     * and returns a sequential array of times in 'H:i' format.
     *
     * @param string $start_time The starting time (parsable by strtotime).
     * @param string $end_time   The ending time (parsable by strtotime).
     * @param int    $interval   The step interval in minutes (default is 1).
     * @return array<string>     An array of formatted time strings.
     */
    function time_intervals(string $start_time, string $end_time, int $interval = 1): array
    {
        $times = [];
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        while ($start_time <= $end_time) {
            $times[] = date('H:i', $start_time);
            $start_time = strtotime("+$interval minutes", $start_time);
        }
        return $times;
    }
}
