<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Community\StoreCommunityRequest;
use App\Http\Requests\Community\UpdateCommunityRequest;
use App\Http\Resources\Community\CommunityListResource;
use App\Http\Resources\Community\CommunityResource;
use App\Models\Community;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommunityController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {

        $communities = Community::with('joined')->get();
        return response()->json(
            $communities->map(function ($community) {
                return[
                'is_user_member' => $community->joined->contains('id', auth()->id()),
                  'id' => $community->id,
                  'name' => $community->name,
                   'community_photo' => $community->community_photo,
                  'description' => $community->description,
                    'members_count' => $community->members_count,
                ];
        }));
    }

    public function showListCommunities()
    {
        $communities = Community::with('joined')->orderBy('created_at', 'desc')->paginate(10);


        return CommunityListResource::collection($communities);
    }

    //add subtopics to community
    public function addCommunitySubtopic(Community $community)
    {
        $subtopics = $community->subtopics;
        $subtopics[] = request('subtopic');
        $community->subtopics = $subtopics;
        $community->save();
        return response()->json([
            'message' => 'Subtopic added successfully'
        ]);
    }

    // Display the specified resource.
    public function show(Community $community)
    {
        $community->load(['joined']);
        return new CommunityResource($community);
    }

    public function showCommunitySubtopics(Community $community)
    {
        return response()->json([
            'subtopics' => $community->subtopics
        ]);
    }

    public function deleteCommunitySubtopic(Community $community)
    {
        //remove a subtopic from the community
        $community->subtopics = array_diff($community->subtopics, [request('subtopic')]);
        $community->save();
        return response()->json([
            'message' => 'Subtopic removed successfully'
        ]);
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
