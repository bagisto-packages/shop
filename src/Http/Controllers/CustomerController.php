<?php

namespace BagistoPackages\Shop\Http\Controllers;

use Hash;
use Illuminate\Support\Facades\Event;
use BagistoPackages\Shop\Repositories\CustomerRepository;
use BagistoPackages\Shop\Repositories\ProductReviewRepository;

class CustomerController extends Controller
{
    /**
     * CustomerRepository object
     *
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * ProductReviewRepository object
     *
     * @var ProductReviewRepository
     */
    protected $productReviewRepository;

    /**
     * Create a new controller instance.
     *
     * @param CustomerRepository $customerRepository
     * @param ProductReviewRepository $productReviewRepository
     */
    public function __construct(CustomerRepository $customerRepository, ProductReviewRepository $productReviewRepository)
    {
        $this->middleware('customer');

        $this->customerRepository = $customerRepository;
        $this->productReviewRepository = $productReviewRepository;
    }

    /**
     * Taking the customer to profile details page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index()
    {
        $customer = $this->customerRepository->find(auth()->guard('customer')->user()->id);

        return view('shop::customers.account.profile.index', compact('customer'));
    }

    /**
     * For loading the edit form page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function edit()
    {
        $customer = $this->customerRepository->find(auth()->guard('customer')->user()->id);

        return view('shop::customers.account.profile.edit', compact('customer'));
    }

    /**
     * Edit function for editing customer profile.
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update()
    {
        $isPasswordChanged = false;
        $id = auth()->guard('customer')->user()->id;

        $this->validate(request(), [
            'first_name' => 'string',
            'last_name' => 'string',
            'gender' => 'required',
            'date_of_birth' => 'date|before:today',
            'email' => 'email|unique:customers,email,' . $id,
            'password' => 'confirmed|min:6|required_with:oldpassword',
            'oldpassword' => 'required_with:password',
            'password_confirmation' => 'required_with:password',
        ]);

        $data = collect(request()->input())->except('_token')->toArray();

        if (isset ($data['date_of_birth']) && $data['date_of_birth'] == "") {
            unset($data['date_of_birth']);
        }

        if (isset ($data['oldpassword'])) {
            if ($data['oldpassword'] != "" || $data['oldpassword'] != null) {
                if (Hash::check($data['oldpassword'], auth()->guard('customer')->user()->password)) {
                    $isPasswordChanged = true;
                    $data['password'] = bcrypt($data['password']);
                } else {
                    session()->flash('warning', trans('shop::app.customer.account.profile.unmatch'));

                    return redirect()->back();
                }
            } else {
                unset($data['password']);
            }
        }

        Event::dispatch('customer.update.before');

        if ($customer = $this->customerRepository->update($data, $id)) {

            if ($isPasswordChanged) {
                Event::dispatch('user.admin.update-password', $customer);
            }

            Event::dispatch('customer.update.after', $customer);

            Session()->flash('success', trans('shop::app.customer.account.profile.edit-success'));

            return redirect()->route('shop.customer.profile.index');
        } else {
            Session()->flash('success', trans('shop::app.customer.account.profile.edit-fail'));

            return redirect()->back('shop.customer.profile.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function destroy($id)
    {
        $id = auth()->guard('customer')->user()->id;

        $data = request()->all();

        $customerRepository = $this->customerRepository->findorFail($id);

        try {
            if (Hash::check($data['password'], $customerRepository->password)) {
                $orders = $customerRepository->all_orders->whereIn('status', ['pending', 'processing'])->first();

                if ($orders) {
                    session()->flash('error', trans('shop::app.response.order-pending', ['name' => 'Customer']));

                    return redirect()->route('shop.customer.profile.index');
                } else {
                    $this->customerRepository->delete($id);

                    session()->flash('success', trans('shop::app.response.delete-success', ['name' => 'Customer']));

                    return redirect()->route('shop.customer.session.index');
                }
            } else {
                session()->flash('error', trans('shop::app.customer.account.address.delete.wrong-password'));

                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash('error', trans('shop::app.response.delete-failed', ['name' => 'Customer']));

            return redirect()->route('shop.customer.profile.index');
        }
    }

    /**
     * Load the view for the customer account panel, showing approved reviews.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function reviews()
    {
        $reviews = $this->productReviewRepository->getCustomerReview();

        return view('shop::customers.account.reviews.index', compact('reviews'));
    }
}
