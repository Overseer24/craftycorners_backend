<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Community;
use App\Http\Resources\CommunityResource;
use App\Http\Requests\Community\StoreCommunityRequest;
use App\Http\Requests\Community\UpdateCommunityRequest;
use Illuminate\Support\Facades\Storage;

class CommunityController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $communities = Community::with(['joined'])->get();
        return CommunityResource::collection($communities);
    }
    // Display the specified resource.
    public function show(Community $community)
    {
        $community->load(['joined', 'posts']);
        return new CommunityResource($community);
    }
    // Store a newly created resource in storage.
    public function store(StoreCommunityRequest $request)
    {
        if (auth()->user()->type != 'admin') {
            return response()->json([
                'message' => 'You are not authorized to create a community'
            ], 403);
        }
        $community = auth()->user()->community()->create($request->validated());

        if ($request->hasFile('community_photo')) {
            $file = $request->file('community_photo');
            $fileName = 'community_photo' . $community->id . '.' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/communities', $fileName);
            $community->community_photo = $fileName;
            $community->save();
        }

        if ($request->hasFile('cover_photo')) {
            $file = $request->file('cover_photo');
            $fileName = 'cover_photo' . $community->id . '.' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/communities', $fileName);
            $community->cover_photo = $fileName;
            $community->save();
        }
        return new CommunityResource($community);

    }

    // Update the specified resource in storage.
    public function update(UpdateCommunityRequest $request, Community $community)
    {

        if (auth()->user()->type != 'admin') {
            return response()->json([
                'message' => 'You are not authorized to update this community'
            ], 403);
        }

        $data = $request->validated();
        if ($request->hasFile('community_photo')) {
            if ($community->community_photo) {
                Storage::delete('public/communities/' . $community->community_photo);
            }
            $file = $request->file('community_photo');
            $fileName = 'community_photo' . $community->id . '.' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/communities', $fileName);
            $data['community_photo'] = $fileName;
        }

        if ($request->hasFile('cover_photo')) {
            if ($community->cover_photo) {
                Storage::delete('public/communities/' . $community->cover_photo);
            }
            $file = $request->file('cover_photo');
            $fileName = 'cover_photo' . $community->id . '.' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/communities', $fileName);
            $data['cover_photo'] = $fileName;
        }
        $community->update($data);
        return new CommunityResource($community);
    }
    // Remove the specified resource from storage.
    public function destroy(Community $community)
    {
        if (auth()->user()->id != $community->user_id) {
            return response()->json([
                'message' => 'You are not authorized to delete this community'
            ], 403);
        }
        $community->delete();
        return response()->json([
            'message' => 'Community deleted successfully'
        ]);
    }

    // public function showCommunityMembers($communityid) {
    //     $community= Community::find($communityid);
    //     $user = $community->users;
    //     return response()->json([
    //         'community' => $community,
    //         'members' => $user

    //     ]);
    // }
}
