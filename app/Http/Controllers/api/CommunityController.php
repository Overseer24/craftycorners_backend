<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Community\AddCommunitySubtopicRequest;
use App\Http\Requests\Community\StoreCommunityRequest;
use App\Http\Requests\Community\UpdateCommunityRequest;
use App\Http\Resources\Community\CommunityListResource;
use App\Http\Resources\Community\CommunityResource;
use App\Models\Community;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function showUserJoinedCommunities()
    {
        $user = auth()->user();
        $communities = $user->communities()->with('joined')->get();
        return response()->json(
            $communities->map(function ($community) {
                return[
                  'id' => $community->id,
                  'name' => $community->name,
                ];
        }));
    }

    public function showListCommunities()
    {
        $communities = Community::with('joined')->orderBy('created_at', 'desc')->paginate(10);


        return CommunityListResource::collection($communities);
    }

    //add subtopics to community
    public function addCommunitySubtopic(Community $community, AddCommunitySubtopicRequest $request)
    {

        //admin can add subtopics

        if (auth()->user()->type != 'admin') {
            return response()->json([
                'message' => 'You are not authorized to add a subtopic'
            ], 403);
        }

        $validatedData = $request->validated();
        $newSubtopics = $validatedData['subtopics'];

        // Convert existing subtopics JSON string to an array
        $existingSubtopics = json_decode($community->subtopics, true) ?? [];
        $uniqueSubtopics = array_diff($newSubtopics, $existingSubtopics);
        $mergedSubtopics = array_merge($existingSubtopics, $uniqueSubtopics);
        $community->update([
            'subtopics' => json_encode($mergedSubtopics)
        ]);
        return response()->json([
            'message' => 'Subtopics added successfully'
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
            'subtopics' => json_decode($community->subtopics, true) ?? []
        ]);
    }

    public function deleteCommunitySubtopic(Community $community)
    {

        //admin can delete subtopics

        if (auth()->user()->type != 'admin') {
            return response()->json([
                'message' => 'You are not authorized to delete a subtopic'
            ], 403);
        }

        //remove a subtopic from the community
        $subtopicToRemove= request('subtopics');

//        if (empty(json_decode($community->subtopics))) {
//            return response()->json([
//                'message' => 'No subtopics found in this community'
//            ], 404);
//        }
        $existingSubtopics = json_decode($community->subtopics, true) ?? [];
        $updatedSubtopics = array_values(array_diff($existingSubtopics, [$subtopicToRemove]));

        // Update the community's subtopics attribute with the modified array
        $community->update([
            'subtopics' => json_encode($updatedSubtopics)
        ]);

        // Return success response
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
        //check if the admin adds subtopics
        if ($request->has('subtopics')) {
            $newSubtopics = array_unique($request->subtopics);
            $community->update(['subtopics' => json_encode($newSubtopics)]);
        }

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

        //check if the admin adds subtopics
        $newSubtopics = $data['subtopics'];
        $existingSubtopics = json_decode($community->subtopics, true) ?? [];
        $uniqueSubtopics = array_diff($newSubtopics, $existingSubtopics);
        $mergedSubtopics = array_merge($existingSubtopics, $uniqueSubtopics);
        $data['subtopics'] = json_encode($mergedSubtopics);

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


    //use collaborative filtering
    public function recommendCommunities()
    {
        $user = auth()->user();

        // Get the ids of all communities the user has joined
        $userCommunityIds = $user->communities()->pluck('community_id');



        // Find other users who have joined the same communities
        $similarUsers = DB::table('community_members')
            ->whereIn('community_id', $userCommunityIds)
            ->where('user_id', '!=', $user->id)
            ->select('user_id', DB::raw('count(*) as similarity_score'))
            ->groupBy('user_id')
            ->orderBy('similarity_score', 'desc')
            ->get();


        // Get the communities these similar users have joined
        $recommendedCommunities = DB::table('community_members')
            ->whereIn('community_members.user_id', $similarUsers->pluck('user_id'))
            ->whereNotIn('community_id', $userCommunityIds)
            ->join('communities', 'communities.id', '=', 'community_members.community_id')
            ->select('communities.*')
            ->distinct()
            ->take(5)  // Limit the results to the top 5 communities
            ->get();

        return response()->json([
            'recommended_communities' => $recommendedCommunities->map(function ($community) {
                return [
                    'id' => $community->id,
                    'name' => $community->name,
                    'description' => $community->description,
                    'members_count' => $community->members_count,
                    'user_id'=> $community->user_id,
                    'community_photo' => $community->community_photo,
                ];
            })
        ]);
    }
}
