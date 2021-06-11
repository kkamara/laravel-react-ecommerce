<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $user = auth()->user();

        $requestUser = User::where('slug', '=', $slug)->first();

        if(isset($requestUser->id) && $user->id === $requestUser->id)
        {
            return view('user.edit', [
                'title' => 'User  Settings',
                'user'  => $requestUser,
            ]);
        }
        
        return abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function update($slug, Request $request)
    {
        $user = auth()->user();

        $requestUser = User::where('slug', '=', $slug)->first();

        if(isset($requestUser->id) && $user->id === $requestUser->id)
        {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|regex:/^[\pL\s\-]+$/u|max:191',
                'last_name' => 'required|string|regex:/^[\pL\s\-]+$/u|max:191',
                'email' => 'required|string|email|max:101',
                'old_password' => 'required',
                'new_password' => 'nullable|confirmed|min:6',
            ]);

            if(empty($validator->errors()->all()))
            {
                if(! app('hash')->check($data['password'], $requestUser->password))
                {
                    if($request->input('new_password') !== NULL)
                    {
                        $data = array();
                        $data['password'] = bcrypt($request->input('new_password'));

                        $requestUser->update($data);
                    }

                    $data = array();
                    $data['first_name'] = filter_var($request->input('first_name'), FILTER_SANITIZE_STRING);
                    $data['last_name']  = filter_var($request->input('last_name'), FILTER_SANITIZE_STRING);
                    $data['email']      = filter_var($request->input('email'), FILTER_SANITIZE_STRING);

                    $slug = User::generateUniqueSlug($data['first_name'] . ' ' . $data['last_name']);
                    $data['slug'] = Str::slug($slug, '-');

                    $requestUser->update($data);

                    return redirect()->back()->with('flashSuccess', 'Your settings have been successfully updated.');
                }
                else
                {
                    return redirect()->back()->with('errors', ['Password is incorrect.']);
                }
            }
            else
            {
                return redirect()->back()->with('errors', $validator->errors()->all());
            }
        }
        
        return abort(404);
    }
}
