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
use App\Models\User;
use App\Rules\BoolStr;
 

class UserController extends Controller
{
    /**
     * Get a list of users.
     */
    public function index(Request $request)
    {
        $data = $request->validate([
            'per_page' => 'integer|nullable|max:100|min:1',
            'sort_by' => Rule::in(['id', 'username', 'name', 'email',
                                   'created_at', 'updated_at']),
            'descending' => ['nullable', new BoolStr],
            'filter' => 'string|nullable',
        ]);
        // set default values for empty params
        $perPage = $data['per_page'] ?? config('ipeer.paginate.perPage');
        $sortBy = $data['sort_by'] ?? config('ipeer.paginate.sortBy');
        $descending = toBoolean($data['descending'] ??
                                config('ipeer.paginate.descending'));
        $sortDir = $descending ? 'desc' : 'asc';
        $filter = $data['filter'] ?? '';

        $users = User::orderBy($sortBy, $sortDir);
        if ($filter) {
            $term = '%' . escapeLike($filter) . '%';
            $users = $users->where(function ($query) use ($term) {
                $query->where('name', 'LIKE', $term)
                      ->orWhere('username', 'LIKE', $term)
                      ->orWhere('email', 'LIKE', $term);
            });
        }
        $users = $users->paginate($perPage);
        return array_merge($users->withQueryString()->toArray(), 
            // additional params for Quasar pagination
            ['sort_by' => $sortBy, 'descending' => $descending]);
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
    public function show(string $id)
    {
        $user = User::find($id);
        if ($user) return $user;
        abort(Status::HTTP_NOT_FOUND, 'User not found');
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
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
            if ($request->user()->id == $id) {
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
    public function destroy(string $id)
    {
        $user = User::find($id);
        if ($user) {
            if ($user->delete()) return response()->noContent(); // HTTP 204
            abort(Status::HTTP_CONFLICT, 'User in the process of being deleted or already deleted');
        }
        abort(Status::HTTP_NOT_FOUND, 'User already deleted / does not exist');
    }
}
