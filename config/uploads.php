<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Activity video upload limits (1st / 2nd Activity Video only)
    |--------------------------------------------------------------------------
    | Max size per video file in kilobytes. Typical ~1 min MP4 fits under this.
    | Testimonials are not limited by this setting.
    | Keep below PHP upload_max_filesize / post_max_size (currently ~40M).
    */
    'video_max_kb' => (int) env('VIDEO_MAX_KB', 20480), // 20 MB

];
