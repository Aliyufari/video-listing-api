<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Video;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            $videos = Video::latest()->paginate(9);

            $data = [
                // 'videos' => VideoResource::collection($videos),
                'data' => $videos,
                'links'  => [
                    'current_page' => $videos->currentPage(),
                    'last_page'    => $videos->lastPage(),
                    'per_page'     => $videos->perPage(),
                    'total'        => $videos->total(),
                    'next_page_url' => $videos->nextPageUrl(),
                    'prev_page_url' => $videos->previousPageUrl(),
                ],
            ];

            return $this->response(
                true,
                'Videos fetched successfully',
                $data,
                'videos',
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

            // Used transaction to rollback if something goes wrong
            DB::beginTransaction();

            if ($request->hasFile('video_file')) {
                $data['video_url'] = Storage::disk('public')->put('videos', $request->video_file);
            }

            // Sample user uuid before implementing auth
            $data['user_id'] = 'RDRT5-HGI09-FFDS6-BRA34';

            $video = Video::create($data);
            $video->categories()->sync($request->input('category_ids'));

            DB::commit();

            return $this->response(
                true,
                'Video uploaded successfully',
                $video,
                'video',
                Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            DB::rollBack();
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
                $video,
                'video',
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

            DB::beginTransaction();

            if ($request->hasFile('video_file')) {
                if ($video->video_url) {
                    Storage::disk('public')->delete($video->video_url);
                }

                $data['video_url'] = Storage::disk('public')->put('videos', $request->video_file);
            }

            $video->update($data);
            $video->categories()->sync($request->input('category_ids'));

            DB::commit();

            return $this->response(
                true,
                'Video updated successfully',
                $video,
                'video',
            );
        } catch (Exception $e) {
            DB::rollBack();
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
