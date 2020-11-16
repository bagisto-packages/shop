<?php

namespace BagistoPackages\Shop\Http\Controllers;

//use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
//    use SendsPasswordResetEmails;
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function create()
    {
        return view('shop::customers.signup.forgot-password');
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
                'email' => 'required|email',
            ]);

            $response = $this->broker()->sendResetLink(
                request(['email'])
            );

            if ($response == Password::RESET_LINK_SENT) {
                session()->flash('success', trans('shop::app.forget_password.reset_link_sent'));

                return back();
            }

            return back()
                ->withInput(request(['email']))
                ->withErrors([
                    'email' => trans('shop::app.forget_password.email_not_exist'),
                ]);
        } catch (\Swift_RfcComplianceException $e) {
            session()->flash('success', trans('shop::app.forget_password.reset_link_sent'));

            return redirect()->back();
        } catch (\Exception $e) {
            report($e);
            session()->flash('error', trans($e->getMessage()));

            return redirect()->back();
        }
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
