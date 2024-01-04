<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Resources\VideoResource;
use App\Http\Requests\Video\StoreVideoRequest;
use App\Http\Requests\Video\UpdateVideoRequest;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $videos = Video::with(['user', 'community'])->get();
        return VideoResource::collection($videos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVideoRequest $request)
    {
        $videoData = $request->validated();
        $videoData['user_id'] = auth()->user()->id;
        // if (!auth()->user()->is_admin) {
        //     $videoData['creator'] = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        // }
        if ($request->hasFile('video_photo')) {
            $file = $request->file('video_photo');
            $fileName = auth()->user()->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/videos', $fileName);
            $videoData['video_photo'] = $fileName;
        }
        $video = auth()->user()->videos()->create($videoData);
        return new VideoResource($video);
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        return new VideoResource($video);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVideoRequest $request, Video $video)
    {
        $videoData = $request->validated();
        if($request->hasFile('video_photo')){
            $file = $request->file('video_photo');
            $fileName = auth()->user()->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/videos', $fileName);
            $videoData['video_photo'] = $fileName;
        }
        $video->update($videoData);
        return new VideoResource($video);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        $video->delete();
        return response()->json([
            'message' => 'Video deleted successfully'
        ]);
    }
}
