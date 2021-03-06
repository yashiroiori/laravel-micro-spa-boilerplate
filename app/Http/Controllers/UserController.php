<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\UserRequest;

use App\Http\Resources\Searchable\UserSearchable;
use BayAreaWebPro\SearchableResource\SearchableResource;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     * @return SearchableResource
     */
    public function index()
    {
        return SearchableResource::make(User::query())->tap(new UserSearchable);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response([
            'entity' => with(new User)->toArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param UserRequest $request
     * @return Response
     */
    public function store(UserRequest $request)
    {
        $data = $request->validated();

        $user = new User($data);

        if (isset($data['role']) && $request->user()->can('updateRole', [$user])) {
            $user->grantRole($data['role']);
        }

        $user->save();

        return response([
            'message' => 'Entity Stored',
            'entity'  => $user,
        ]);
    }

    /**
     * Display the specified resource.
     * @param User $user
     * @return Response
     */
    public function show(User $user)
    {
        return response([
            'entity' => $user->loadMissing(['tokens']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param User $user
     * @return Response
     */
    public function edit(User $user)
    {
        return response([
            'entity' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param UserRequest $request
     * @param User $user
     * @return Response
     */
    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        if (isset($data['role']) && $request->user()->can('updateRole', [$user])) {
            $user->grantRole($data['role']);
        }
        $user->update($data);

        return response([
            'message' => 'Entity Updated',
            'entity'  => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param User $user
     * @return Response
     * @throws \Exception
     */
    public function destroy(Request $request, User $user)
    {
        $this->authorize('disabled:permission');
        $user->delete();
        return response([
            'message' => 'Entity Destroyed',
            'entity'  => $user->only('id'),
        ]);
    }
}
