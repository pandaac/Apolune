<?php

namespace Apolune\Account\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Apolune\Core\Http\Controllers\Controller;
use Illuminate\Http\Exception\HttpResponseException;
use Apolune\Account\Http\Requests\Auth\LoginRequest;
use Apolune\Account\Http\Requests\Auth\CreateRequest;

class AuthController extends Controller
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new authentication controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;

        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view('theme::account.auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Apolune\Account\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->only('name', 'password');

        if (! $this->auth->attempt($credentials, $request->has('remember'))) {
            throw new HttpResponseException($request->response([
                'name' => trans('theme::account.login.form.error'),
            ]));
        }

        return redirect()->intended('/account');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('theme::account.auth.create');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Apolune\Account\Http\Requests\Auth\CreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        $account = app('account')->create([
            'name'      => $request->get('name'),
            'email'     => $request->get('email'),
            'password'  => bcrypt($request->get('password')),
        ]);

        $player = app('player')->create([
            'name'          => $request->get('player'),
            'account_id'    => $account->id(),
            'vocation'      => $request->get('vocation'),
            'sex'           => $request->get('sex'),
            'conditions'    => '',
        ]);

        $this->auth->login($account);

        return redirect('/account');
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $this->auth->logout();

        return view('theme::account.auth.logout');
    }
}
