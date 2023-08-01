<?php
 
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

use Symfony\Component\HttpFoundation\Response as Status;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\AbstractApiController;
use App\Http\Requests\Paginated\UserPaginatedRequest;
use App\Models\User;
 

class UserController extends AbstractApiController
{
    public function __construct()
    {
        // tell Laravel to use UserPolicy to protect access to these methods
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Get a list of users.
     */
    public function index(UserPaginatedRequest $request)
    {
        return $this->paginatedIndex($request, User::query(),
            ['name', 'username', 'email']);
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required',
            'password' => 'required',
            'name' => 'nullable',
            'email' => 'nullable|email',
        ]);
        $user = new User;
        $user->username = $data['username'];
        $user->password = $data['password'];
        if (isset($data['name'])) $user->name = $data['name'];
        if (isset($data['email'])) $user->email = $data['email'];
        $user->save();
        return $user;
    }

    /**
     * Get the specified user.
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $userInfo = $request->validate([
            'username' => ['nullable', 'string',
                Rule::unique('users')->ignore($user)],
            'name' => ['nullable', 'string'],
            'email' => ['nullable', 'string', 'email',
                Rule::unique('users')->ignore($user)],
            'password' => ['nullable', 'string'],
        ]);
        // TODO: This means that users can't empty out fields, e.g. name/email
        // not sure if this is a concern
        if (isset($userInfo['username']) &&
            $userInfo['username'] != $user->username)
        {
            $user->username = $userInfo['username'];
        }
        if (isset($userInfo['name']) && $userInfo['name'] != $user->name) {
            $user->name = $userInfo['name'];
        }
        if (isset($userInfo['email']) && $userInfo['email'] != $user->email) {
            $user->email = $userInfo['email'];
        }
        // only let users change their own passwords
        if (!empty($userInfo['password'])) {
            if ($request->user()->id == $user->id) {
                $user->password = $userInfo['password'];
            }
            else {
                abort(Status::HTTP_FORBIDDEN,
                    'You cannot change password for this user');
            }
        }
        $user->save();
        return $user;

    }

    /**
     * Delete specified user.
     */
    public function destroy(User $user)
    {
        if ($user->delete()) return response()->noContent(); // HTTP 204
        abort(Status::HTTP_CONFLICT, 'User in the process of being deleted or already deleted');
    }
}
