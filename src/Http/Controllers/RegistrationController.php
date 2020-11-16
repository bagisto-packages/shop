<?php

namespace BagistoPackages\Shop\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use BagistoPackages\Shop\Mail\RegistrationEmail;
use BagistoPackages\Shop\Mail\VerificationEmail;
use BagistoPackages\Shop\Repositories\CustomerRepository;
use BagistoPackages\Shop\Repositories\CustomerGroupRepository;
use Cookie;

class RegistrationController extends Controller
{
    /**
     * CustomerRepository object
     *
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * CustomerGroupRepository object
     *
     * @var CustomerGroupRepository
     */
    protected $customerGroupRepository;

    /**
     * Create a new Repository instance.
     *
     * @param CustomerRepository $customerRepository
     * @param CustomerGroupRepository $customerGroupRepository
     */
    public function __construct(
        CustomerRepository $customerRepository,
        CustomerGroupRepository $customerGroupRepository
    )
    {
        $this->customerRepository = $customerRepository;
        $this->customerGroupRepository = $customerGroupRepository;

        parent::__construct();
    }

    /**
     * Opens up the user's sign up form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function show()
    {
        return view('shop::customers.signup.index');
    }

    /**
     * Method to store user's sign up form data to DB.
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create()
    {
        $this->validate(request(), [
            'first_name' => 'string|required',
            'last_name'  => 'string|required',
            'email'      => 'email|required|unique:customers,email',
            'password'   => 'confirmed|min:6|required',
        ]);

        $data = request()->input();

        $data['password'] = bcrypt($data['password']);
        $data['api_token'] = Str::random(80);

        if (core()->getConfigData('customer.settings.email.verification')) {
            $data['is_verified'] = 0;
        } else {
            $data['is_verified'] = 1;
        }

        $data['customer_group_id'] = $this->customerGroupRepository->findOneWhere(['code' => 'general'])->id;

        $verificationData['email'] = $data['email'];
        $verificationData['token'] = md5(uniqid(rand(), true));
        $data['token'] = $verificationData['token'];

        Event::dispatch('customer.registration.before');

        $customer = $this->customerRepository->create($data);

        Event::dispatch('customer.registration.after', $customer);

        if ($customer) {
            if (core()->getConfigData('customer.settings.email.verification')) {
                try {
                    $configKey = 'emails.general.notifications.emails.general.notifications.verification';
                    if (core()->getConfigData($configKey)) {
                        Mail::queue(new VerificationEmail($verificationData));
                    }

                    session()->flash('success', trans('shop::app.customer.signup-form.success-verify'));
                } catch (\Exception $e) {
                    report($e);
                    session()->flash('info', trans('shop::app.customer.signup-form.success-verify-email-unsent'));
                }
            } else {
                try {
                    $configKey = 'emails.general.notifications.emails.general.notifications.registration';
                    if (core()->getConfigData($configKey)) {
                        Mail::queue(new RegistrationEmail(request()->all()));
                    }

                    session()->flash('success', trans('shop::app.customer.signup-form.success-verify')); //customer registered successfully
                } catch (\Exception $e) {
                    report($e);
                    session()->flash('info', trans('shop::app.customer.signup-form.success-verify-email-unsent'));
                }

                session()->flash('success', trans('shop::app.customer.signup-form.success'));
            }

            return redirect()->route('shop.customer.session.index');
        } else {
            session()->flash('error', trans('shop::app.customer.signup-form.failed'));

            return redirect()->back();
        }
    }

    /**
     * Method to verify account
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function verifyAccount($token)
    {
        $customer = $this->customerRepository->findOneByField('token', $token);

        if ($customer) {
            $customer->update(['is_verified' => 1, 'token' => 'NULL']);

            session()->flash('success', trans('shop::app.customer.signup-form.verified'));
        } else {
            session()->flash('warning', trans('shop::app.customer.signup-form.verify-failed'));
        }

        return redirect()->route('shop.customer.session.index');
    }

    /**
     * @param string $email
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function resendVerificationEmail($email)
    {
        $verificationData['email'] = $email;
        $verificationData['token'] = md5(uniqid(rand(), true));

        $customer = $this->customerRepository->findOneByField('email', $email);

        $this->customerRepository->update(['token' => $verificationData['token']], $customer->id);

        try {
            Mail::queue(new VerificationEmail($verificationData));

            if (Cookie::has('enable-resend')) {
                \Cookie::queue(\Cookie::forget('enable-resend'));
            }

            if (Cookie::has('email-for-resend')) {
                \Cookie::queue(\Cookie::forget('email-for-resend'));
            }
        } catch (\Exception $e) {
            report($e);
            session()->flash('error', trans('shop::app.customer.signup-form.verification-not-sent'));

            return redirect()->back();
        }

        session()->flash('success', trans('shop::app.customer.signup-form.verification-sent'));

        return redirect()->back();
    }
}
