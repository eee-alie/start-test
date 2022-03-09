<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoginRes;
use App\Http\Resources\UserRes;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function createOrLoginUser(Request $request): LoginRes
    {
        $request->validate([
            'mobile' => ['required', 'regex:/^(\+98|0)?9\d{9}$/'],
            'password' => ['required']
        ]);
        $phone = substr($request['mobile'], -10);
        $user = User::where('mobile', $phone)->first();
        if (empty($user)) {
            $user = new User;
            $user['mobile'] = $phone;
            $user['password'] = Hash::make($request['password']);
            $user->save();
        }
        if (!(Hash::check($request['password'], $user['password']))) {
            abort(401, 'wrong password.');
        }
        $user['access_token'] = $user->createToken('pay-star')->plainTextToken;
        return new LoginRes($user);
    }

    public function editUser(Request $request): JsonResponse|UserRes
    {
        $validate = validator(['id' => $request['id']], [
            'id' => ['required', Rule::exists('users'), Rule::in([Auth::user()['id']])]]);
        if ($validate->fails()) return Response::json(['message' => "The given data was invalid.", 'errors' => $validate->errors()], 422);
        $user = User::find($request['id']);
        if (!empty($request['firstname'])) $user['first_name'] = $request['firstname'];
        if (!empty($request['lastname'])) $user['last_name'] = $request['lastname'];
        if (!empty($request['bank_number'])) $user['bank_account_number'] = $request['bank_number'];
        $user->save();
        return new UserRes($user);
    }

    public function getUser(Request $request): JsonResponse|UserRes
    {
        $validate = validator(['id' => $request['id']], ['id' => ['required', Rule::exists('users')]]);
        if ($validate->fails()) return Response::json(['message' => "The given data was invalid.", 'errors' => $validate->errors()], 422);
        return new UserRes(User::find($request['id']));
    }
}
