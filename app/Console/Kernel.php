<?php

namespace App\Console;

use App\ChargeAssignment;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {

            $dates = DB::table('detail_charge_assignments')
                ->join('charge_assignments', 'charge_assignments.id', '=', 'detail_charge_assignments.charge_assignment_id')
                ->select('detail_charge_assignments.finalDate')
                ->where('detail_charge_assignments.finalDate', '<', Carbon::now()->format('Y-m-d'))
                ->get();

            foreach($dates as $date)
            {
               $ids = DB::table('detail_charge_assignments')
                    ->join('charge_assignments', 'charge_assignments.id', '=', 'detail_charge_assignments.charge_assignment_id')
                    ->select('charge_assignments.id')
                    ->where('detail_charge_assignments.finalDate', $date->finalDate)
                    ->get();

                foreach ($ids as $id)
                {
                    ChargeAssignment::findOrFail($id->id)->update(['charge_state_id' => 2]);
                }
             }

        })->everyMinute();
    }

}
