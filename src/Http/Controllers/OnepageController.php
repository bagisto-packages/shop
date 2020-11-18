<?php

namespace BagistoPackages\Shop\Http\Controllers;

use Illuminate\Support\Facades\Event;
use BagistoPackages\Shop\Facades\Cart;
use BagistoPackages\Shop\Facades\Shipping;
use BagistoPackages\Shop\Facades\Payment;
use BagistoPackages\Shop\Http\Requests\CustomerAddressForm;
use BagistoPackages\Shop\Repositories\OrderRepository;
use BagistoPackages\Shop\Repositories\CustomerRepository;

class OnepageController extends Controller
{
    /**
     * OrderRepository object
     *
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * customerRepository instance object
     *
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * Create a new controller instance.
     *
     * @param OrderRepository $orderRepository
     * @param CustomerRepository $customerRepository
     * @return void
     */
    public function __construct(OrderRepository $orderRepository, CustomerRepository $customerRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        Event::dispatch('checkout.load.index');

        if (!auth()->guard('customer')->check()
            && !core()->getConfigData('catalog.products.guest-checkout.allow-guest-checkout')) {
            return redirect()->route('shop.customer.session.index');
        }

        if (Cart::hasError()) {
            return redirect()->route('shop.checkout.cart.index');
        }

        $cart = Cart::getCart();

        if (!auth()->guard('customer')->check() && $cart->hasDownloadableItems()) {
            return redirect()->route('shop.customer.session.index');
        }

        if (!auth()->guard('customer')->check() && !$cart->hasGuestCheckoutItems()) {
            return redirect()->route('shop.customer.session.index');
        }

        Cart::collectTotals();

        return view('shop::checkout.onepage', compact('cart'));
    }

    /**
     * Return order short summary
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function summary()
    {
        $cart = Cart::getCart();

        return response()->json([
            'html' => view('shop::checkout.total.summary', compact('cart'))->render(),
        ]);
    }

    /**
     * Saves customer address.
     *
     * @param CustomerAddressForm $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveAddress(CustomerAddressForm $request)
    {
        $data = request()->all();

        if (!auth()->guard('customer')->check() && !Cart::getCart()->hasGuestCheckoutItems()) {
            return response()->json(['redirect_url' => route('shop.customer.session.index')], 403);
        }

        $data['billing']['address1'] = implode(PHP_EOL, array_filter($data['billing']['address1']));
        $data['shipping']['address1'] = implode(PHP_EOL, array_filter($data['shipping']['address1']));

        if (Cart::hasError() || !Cart::saveCustomerAddress($data)) {
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        } else {
            $cart = Cart::getCart();

            Cart::collectTotals();

            if ($cart->haveStockableItems()) {
                if (!$rates = Shipping::collectRates()) {
                    return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
                } else {
                    return response()->json($rates);
                }
            } else {
                return response()->json(Payment::getSupportedPaymentMethods());
            }
        }
    }

    /**
     * Saves shipping method.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveShipping()
    {
        $shippingMethod = request()->get('shipping_method');

        if (Cart::hasError() || !$shippingMethod || !Cart::saveShippingMethod($shippingMethod)) {
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        }

        Cart::collectTotals();

        return response()->json(Payment::getSupportedPaymentMethods());
    }

    /**
     * Saves payment method.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function savePayment()
    {
        $payment = request()->get('payment');

        if (Cart::hasError() || !$payment || !Cart::savePaymentMethod($payment)) {
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        }

        Cart::collectTotals();

        $cart = Cart::getCart();

        return response()->json([
            'jump_to_section' => 'review',
            'html' => view('shop::checkout.onepage.review', compact('cart'))->render(),
        ]);
    }

    /**
     * Saves order.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function saveOrder()
    {
        if (Cart::hasError()) {
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        }

        Cart::collectTotals();

        $this->validateOrder();

        $cart = Cart::getCart();

        if ($redirectUrl = Payment::getRedirectUrl($cart)) {
            return response()->json([
                'success' => true,
                'redirect_url' => $redirectUrl,
            ]);
        }

        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        Cart::deActivateCart();

        session()->flash('order', $order);

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Order success page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function success()
    {
        if (!$order = session('order')) {
            return redirect()->route('shop.checkout.cart.index');
        }

        return view('shop::checkout.success', compact('order'));
    }

    /**
     * Validate order before creation
     *
     * @return void|\Exception
     * @throws \Exception
     */
    public function validateOrder()
    {
        $cart = Cart::getCart();

        $minimumOrderAmount = (int)core()->getConfigData('sales.orderSettings.minimum-order.minimum_order_amount') ?? 0;

        if (!($cart->base_sub_total > $minimumOrderAmount)) {
            throw new \Exception(trans('shop::app.checkout.cart.minimum-order-message', ['amount' => $minimumOrderAmount]));
        }

        if ($cart->haveStockableItems() && !$cart->shipping_address) {
            throw new \Exception(trans('Please check shipping address.'));
        }

        if (!$cart->billing_address) {
            throw new \Exception(trans('Please check billing address.'));
        }

        if ($cart->haveStockableItems() && !$cart->selected_shipping_rate) {
            throw new \Exception(trans('Please specify shipping method.'));
        }

        if (!$cart->payment) {
            throw new \Exception(trans('Please specify payment method.'));
        }
    }

    /**
     * Check Customer is exist or not
     *
     * @return string
     */
    public function checkExistCustomer()
    {
        $customer = $this->customerRepository->findOneWhere([
            'email' => request()->email,
        ]);

        if (!is_null($customer)) {
            return 'true';
        }

        return 'false';
    }

    /**
     * Login for checkout
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function loginForCheckout()
    {
        $this->validate(request(), [
            'email' => 'required|email'
        ]);

        if (!auth()->guard('customer')->attempt(request(['email', 'password']))) {
            return response()->json(['error' => trans('shop::app.customer.login-form.invalid-creds')]);
        }

        Cart::mergeCart();

        return response()->json(['success' => 'Login successfully']);
    }

    /**
     * To apply couponable rule requested
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function applyCoupon()
    {
        $this->validate(request(), [
            'code' => 'string|required',
        ]);

        $code = request()->input('code');

        $result = $this->coupon->apply($code);

        if ($result) {
            Cart::collectTotals();

            return response()->json([
                'success' => true,
                'message' => trans('shop::app.checkout.total.coupon-applied'),
                'result' => $result,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => trans('shop::app.checkout.total.cannot-apply-coupon'),
                'result' => null,
            ], 422);
        }

        return $result;
    }

    /**
     * Initiates the removal of couponable cart rule
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeCoupon()
    {
        $result = $this->coupon->remove();

        if ($result) {
            Cart::collectTotals();

            return response()->json([
                'success' => true,
                'message' => trans('admin::app.promotion.status.coupon-removed'),
                'data' => [
                    'grand_total' => core()->currency(Cart::getCart()->grand_total),
                ],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => trans('admin::app.promotion.status.coupon-remove-failed'),
                'data' => null,
            ], 422);
        }
    }
}
