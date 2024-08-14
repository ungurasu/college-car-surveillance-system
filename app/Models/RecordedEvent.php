<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RecordedEvent extends Model
{
    use HasFactory;

    public function filterRecordedEvents($start_datetime, $end_datetime)
    {
        $results = DB::select(
            "SELECT *
                   FROM videos
                   WHERE (created_at BETWEEN ? AND ?)
                   ORDER BY created_at DESC",
            [
                $start_datetime,
                $end_datetime
            ]
        );

        foreach ($results as $k => $result) {
            $results[$k] = (array) $result;
        }

        return $results;
    }

    public function getRecordedEventById($event_id)
    {
        $event = DB::table('videos')->where('video_id', $event_id)->first();

        return $event;
    }

    public function deleteRecordedEvent($event_id)
    {
        DB::table('videos')->where('video_id', $event_id)->delete();
    }

    public function getCurrentTimeDB()
    {
        $currentTime = DB::select("SELECT CURRENT_TIMESTAMP()");

        return $currentTime[0]->{'CURRENT_TIMESTAMP()'};
    }
}
