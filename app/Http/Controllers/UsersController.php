<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreUsersRequest;
use App\Http\Requests\Users\UpdateUsersRequest;
use App\Http\Resources\UsersCollection;
use App\Http\Resources\UsersResource;
use App\Models\SystemLogger;
use App\Models\Users2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = Users2::paginate(10);

        return new UsersCollection($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUsersRequest $request)
    {
        $users = DB::transaction(function () use ($request): Users2 {
            $user = Users2::create($request->validated());
            $this->logActivity($request, 'user.created', "Création du compte {$user->email}", $user);

            return $user;
        });

        return new UsersResource($users);
    }

    /**
     * Display the specified resource.
     */
    public function show(Users2 $users)
    {
        return new UsersResource($users);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Users2 $users)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUsersRequest $request, Users2 $users)
    {
        DB::transaction(function () use ($request, $users): void {
            $users->update($request->validated());
            $this->logActivity($request, 'user.updated', "Mise à jour du compte {$users->email}", $users);
        });

        return new UsersResource($users);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Users2 $users)
    {
        DB::transaction(function () use ($users): void {
            $this->logActivity(request(), 'user.deleted', "Suppression du compte {$users->email}", $users);
            $users->delete();
        });

        return response()->noContent();
    }

    private function logActivity(Request $request, string $action, string $description, Users2 $subject): void
    {
        SystemLogger::create([
            'user_id' => $request->user()->getAuthIdentifier(),
            'action' => $action,
            'description' => $description,
            'adresse_ip' => $request->ip(),
            'subject_type' => Users2::class,
            'subject_id' => $subject->id,
        ]);
    }
}
