<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Video;
use App\Traits\ApiResponse;
use App\Jobs\ProcessVideoUpdate;
use App\Jobs\ProcessVideoUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\VideoResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use Symfony\Component\HttpFoundation\Response;

class VideoController extends Controller
{
    // API Response Trait
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $videos = Video::with('categories')->latest()->paginate(9);

            $data = [
                'data' => VideoResource::collection($videos),
                'links'  => [
                    'current_page' => $videos->currentPage(),
                    'last_page' => $videos->lastPage(),
                    'per_page' => $videos->perPage(),
                    'total' => $videos->total(),
                    'next_page_url' => $videos->nextPageUrl(),
                    'prev_page_url' => $videos->previousPageUrl()
                ]
            ];

            return $this->response(
                true,
                'Videos fetched successfully',
                $data,
                'videos'
            );
        } catch (Exception $e) {
            Log::error('Error fetching videos: ' . $e->getMessage());

            return $this->response(
                false,
                'Error fetching videos',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVideoRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('video_file')) {
                $data['video_file'] = $request->file('video_file');
            }

            // Sample user uuid before implementing auth
            $data['user_id'] = 'RDRT5-HGI09-FFDS6-BRA34';

            ProcessVideoUpload::dispatch($data, $data['category_ids']);

            return $this->response(
                true,
                'Video upload initiated successfully',
                null,
                'video',
                Response::HTTP_ACCEPTED
            );
        } catch (Exception $e) {
            Log::error('Error uploading video: ' . $e->getMessage());

            return $this->response(
                false,
                'Error uploading video',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        try {
            return $this->response(
                true,
                'Video fetched successfully',
                new VideoResource($video->load('categories')),
                'video'
            );
        } catch (Exception $e) {
            Log::error('Error fetching video: ' . $e->getMessage());

            return $this->response(
                false,
                'Error uploading video',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVideoRequest $request, Video $video)
    {
        try {
            $data = $request->validated();


            if ($request->hasFile('video_file')) {
                $data['video_file'] = $request->file('video_file');
            }

            ProcessVideoUpdate::dispatch($video, $data, $data['category_ids']);

            return $this->response(
                true,
                'Video updated successfully',
                null,
                'video',
            );
        } catch (Exception $e) {
            Log::error('Error updating video: ' . $e->getMessage());

            return $this->response(
                false,
                'Error updating video',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        try {
            if ($video->video_url) {
                Storage::disk('public')->delete($video->video_url);
            }

            $video->categories()->detach();
            $video->delete();

            return $this->response(
                false,
                'Video deleted successfully'
            );
        } catch (Exception $e) {
            Log::error('Error deleting video: ' . $e->getMessage());

            return $this->response(
                false,
                'Error deleting video',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
