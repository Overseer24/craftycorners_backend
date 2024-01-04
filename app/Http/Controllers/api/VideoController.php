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
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVideoRequest $request)
    {
        $videoData = $request->validated();
        $videoData['user_id'] = auth()->user()->id;

        if($request->hasFile('video_photo')) {
            $file = $request->file('video_photo');
            $fileName = auth()->user()->id.'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/videos', $fileName);
            $videoData['video_photo'] = $fileName;
        }
        $video = auth()->user()->videos()->create($videoData);
        return new VideoResource($video);
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $videos)
    {
        return new VideoResource($videos);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVideoRequest $request, Video $videos)
    {
        $data = $request->validated();
        if($request->hasFile('video_photo')) {
            $file = $request->file('video_photo');
            $fileName = auth()->user()->id.'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/videos', $fileName);
            $data['video_photo'] = $fileName;
        }
        $videos->update($data);
        return new VideoResource($videos);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $videos)
    {
        $videos->delete();
        return response()->json([
            'message' => 'Video deleted successfully'
        ], 200);
    }
}
