<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessVideoUpload implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $categoryIds;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, array $categoryIds = [])
    {
        $this->data = $data;
        $this->categoryIds = $categoryIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (isset($this->data['video_file'])) {
            // Store the video and get URL
            $this->data['video_url'] = Storage::disk('public')->put('videos', $this->data['video_file']);
            unset($this->data['video_file']);
        }

        // Set status to completed
        $this->data['status'] = 'completed';

        $video = Video::create($this->data);
        // Sync category ids if exists
        if (!empty($this->categoryIds)) {
            $video->categories()->sync($this->categoryIds);
        }
    }
}
