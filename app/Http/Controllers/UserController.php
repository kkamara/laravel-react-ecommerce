<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /** @property User */
    protected $user;
    
    public function __construct() {
        $this->user = new User;
        $this->middleware('auth');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  String  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        /** @var User */
        $authUser = auth()->user();
        $this->user = $this->user->where('slug', '=', $slug)->first();

        if(
            null === $this->user->id || 
            $authUser->id !== $this->user->id
        ) {
            return abort(404);
        }

        return view('user.edit', [
            'title' => 'User  Settings',
            'user'  => $this->user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  String                    $slug
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($slug, Request $request)
    {
        /** @var User */
        $authUser = auth()->user();
        $this->user = $this->user->where('slug', '=', $slug)->first();

        if(null === $authUser || null === $this->user) {
            return abort(404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|regex:/^[\pL\s\-]+$/u|max:191',
            'last_name' => 'required|string|regex:/^[\pL\s\-]+$/u|max:191',
            'email' => 'required|string|email|max:101',
            'old_password' => 'required',
            'new_password' => 'nullable|confirmed|min:6',
        ]);

        if(false === empty($validator->errors()->all()))
        {
            return redirect()->back()->with('errors', $validator->errors()->all());
        }

        if(false === app('hash')->check($request->input('password'), $this->user->password))
        {
            return redirect()->back()->with('errors', ['Password is incorrect.']);
        }

        if($request->input('new_password') !== NULL)
        {
            $this->user->update(array(
                'password' => bcrypt($request->input('new_password')),
            ));
        }

        $data = array(
            'first_name' => filter_var($request->input('first_name'), FILTER_SANITIZE_STRING),
            'last_name'  => filter_var($request->input('last_name'), FILTER_SANITIZE_STRING),
            'email'      => filter_var($request->input('email'), FILTER_SANITIZE_STRING),
        );
        $data['slug'] = $this->user->generateUniqueSlug(
            $data['first_name'] . ' ' . $data['last_name']
        );
        $this->user->update($data);

        return redirect()
            ->back()
            ->with(
                'flashSuccess', 
                'Your settings have been successfully updated.'
            );
    }
}
