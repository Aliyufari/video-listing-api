<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessVideoUpdate implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $video;
    protected $data;
    protected $categoryIds;

    /**
     * Create a new job instance.
     */
    public function __construct(Video $video, array $data, array $categoryIds = [])
    {
        $this->video = $video;
        $this->data = $data;
        $this->categoryIds = $categoryIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (isset($this->data['video_file'])) {
            // Delete old video
            if ($this->video->video_url) {
                $path = str_replace('/storage/', '', $this->video->video_url);
                Storage::disk('public')->delete($path);
            }

            // Upload new video
            $this->data['video_url'] = Storage::disk('public')->put('videos', $this->data['video_file']);
            unset($this->data['video_file']);
        }

        $this->video->update($this->data);
        // Sync category ids if exists
        if (!empty($this->categoryIds)) {
            $this->video->categories()->sync($this->categoryIds);
        }
    }
}
