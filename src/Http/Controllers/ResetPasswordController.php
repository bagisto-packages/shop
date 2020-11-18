<?php

namespace BagistoPackages\Shop\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param string|null $token
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function create($token = null)
    {
        return view('shop::customers.signup.reset-password')->with(
            ['token' => $token, 'email' => request('email')]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        try {
            $this->validate(request(), [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:6',
            ]);

            $response = $this->broker()->reset(
                request(['email', 'password', 'password_confirmation', 'token']), function ($customer, $password) {
                $this->resetPassword($customer, $password);
            }
            );

            if ($response == Password::PASSWORD_RESET) {
                return redirect()->route('shop.customer.profile.index');
            }

            return back()
                ->withInput(request(['email']))
                ->withErrors([
                    'email' => trans($response),
                ]);
        } catch (\Exception $e) {
            session()->flash('error', trans($e->getMessage()));

            return redirect()->back();
        }
    }

    /**
     * Reset the given customer password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword $customer
     * @param string $password
     * @return void
     */
    protected function resetPassword($customer, $password)
    {
        $customer->password = Hash::make($password);

        $customer->setRememberToken(Str::random(60));

        $customer->save();

        event(new PasswordReset($customer));

        auth()->guard('customer')->login($customer);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('customers');
    }
}
