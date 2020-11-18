<?php

namespace BagistoPackages\Shop\Http\Controllers;

use BagistoPackages\Shop\Facades\Cart;
use BagistoPackages\Shop\Repositories\OrderRepository;
use BagistoPackages\Shop\Helpers\Ipn;

class StandardController extends Controller
{
    /**
     * OrderRepository object
     *
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * Ipn object
     *
     * @var Ipn
     */
    protected $ipnHelper;

    /**
     * Create a new controller instance.
     *
     * @param OrderRepository $orderRepository
     * @param Ipn $ipnHelper
     * @return void
     */
    public function __construct(OrderRepository $orderRepository, Ipn $ipnHelper)
    {
        $this->orderRepository = $orderRepository;
        $this->ipnHelper = $ipnHelper;
    }

    /**
     * Redirects to the paypal.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function redirect()
    {
        return view('shop::standard-redirect');
    }

    /**
     * Cancel payment from paypal.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel()
    {
        session()->flash('error', 'Paypal payment has been canceled.');

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Success payment
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function success()
    {
        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        Cart::deActivateCart();

        session()->flash('order', $order);

        return redirect()->route('shop.checkout.success');
    }

    /**
     * Paypal Ipn listener
     *
     * @return void
     * @throws \Exception
     */
    public function ipn()
    {
        $this->ipnHelper->processIpn(request()->all());
    }
}
