<?php

namespace BagistoPackages\Shop\Http\Controllers;

use BagistoPackages\Shop\Repositories\CustomerAddressRepository;
use BagistoPackages\Shop\Rules\VatIdRule;

class AddressController extends Controller
{
    /**
     * CustomerAddressRepository object
     *
     * @var CustomerAddressRepository
     */
    protected $customerAddressRepository;

    /**
     * Create a new controller instance.
     *
     * @param CustomerAddressRepository $customerAddressRepository
     * @return void
     */
    public function __construct(CustomerAddressRepository $customerAddressRepository)
    {
        $this->middleware('customer');

        $this->customerAddressRepository = $customerAddressRepository;
    }

    /**
     * Address Route index page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function index()
    {
        return view('shop::customers.account.address.index')
            ->with('addresses', auth()->guard('customer')->user()->addresses); // $this->customer->addresses
    }

    /**
     * Show the address create form
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function create()
    {
        return view('shop::customers.account.address.create', [
            'defaultCountry' => config('app.default_country'),
        ]);
    }

    /**
     * Create a new address for customer.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store()
    {
        request()->merge(['address1' => implode(PHP_EOL, array_filter(request()->input('address1')))]);

        $data = collect(request()->input())->except('_token')->toArray();

        $this->validate(request(), [
            'company_name' => 'string',
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'address1' => 'string|required',
            'country' => 'string|required',
            'state' => 'string|required',
            'city' => 'string|required',
            'postcode' => 'required',
            'phone' => 'required',
            'vat_id' => new VatIdRule(),
        ]);

        $cust_id['customer_id'] = auth()->guard('customer')->user()->id;
        $cust_id['first_name'] = auth()->guard('customer')->user()->first_name;
        $cust_id['last_name'] = auth()->guard('customer')->user()->last_name;
        $data = array_merge($cust_id, $data);

        if (auth()->guard('customer')->user()->addresses->count() == 0) {
            $data['default_address'] = 1;
        }

        if ($this->customerAddressRepository->create($data)) {
            session()->flash('success', trans('shop::app.customer.account.address.create.success'));

            return redirect()->route('shop.customer.address.index');
        } else {
            session()->flash('error', trans('shop::app.customer.account.address.create.error'));

            return redirect()->back();
        }
    }

    /**
     * For editing the existing addresses of current logged in customer
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function edit($id)
    {
        $address = $this->customerAddressRepository->findOneWhere([
            'id' => $id,
            'customer_id' => auth()->guard('customer')->user()->id,
        ]);

        if (!$address) {
            abort(404);
        }

        return view('shop::customers.account.address.edit', array_merge(compact('address'), [
            'defaultCountry' => config('app.default_country')
        ]));
    }

    /**
     * Edit's the premade resource of customer called Address.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update($id)
    {
        request()->merge(['address1' => implode(PHP_EOL, array_filter(request()->input('address1')))]);

        $this->validate(request(), [
            'company_name' => 'string',
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'address1' => 'string|required',
            'country' => 'string|required',
            'state' => 'string|required',
            'city' => 'string|required',
            'postcode' => 'required',
            'phone' => 'required',
            'vat_id' => new VatIdRule(),
        ]);

        $data = collect(request()->input())->except('_token')->toArray();

        $addresses = auth()->guard('customer')->user()->addresses;

        foreach ($addresses as $address) {
            if ($id == $address->id) {
                session()->flash('success', trans('shop::app.customer.account.address.edit.success'));

                $this->customerAddressRepository->update($data, $id);

                return redirect()->route('shop.customer.address.index');
            }
        }

        session()->flash('warning', trans('shop::app.security-warning'));

        return redirect()->route('shop.customer.address.index');
    }

    /**
     * To change the default address or make the default address,
     * by default when first address is created will be the default address
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function makeDefault($id)
    {
        if ($default = auth()->guard('customer')->user()->default_address) {
            $this->customerAddressRepository->find($default->id)->update(['default_address' => 0]);
        }

        if ($address = $this->customerAddressRepository->find($id)) {
            $address->update(['default_address' => 1]);
        } else {
            session()->flash('success', trans('shop::app.customer.account.address.index.default-delete'));
        }

        return redirect()->back();
    }

    /**
     * Delete address of the current customer
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $address = $this->customerAddressRepository->findOneWhere([
            'id' => $id,
            'customer_id' => auth()->guard('customer')->user()->id,
        ]);

        if (!$address) {
            abort(404);
        }

        $this->customerAddressRepository->delete($id);

        session()->flash('success', trans('shop::app.customer.account.address.delete.success'));

        return redirect()->route('shop.customer.address.index');
    }
}
