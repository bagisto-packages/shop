<?php

namespace BagistoPackages\Shop\Listeners;

use BagistoPackages\Shop\Repositories\BookingRepository;
use BagistoPackages\Shop\Repositories\CartRuleCouponRepository;
use BagistoPackages\Shop\Repositories\CartRuleCouponUsageRepository;
use BagistoPackages\Shop\Repositories\CartRuleCustomerRepository;
use BagistoPackages\Shop\Repositories\CartRuleRepository;

class Order
{
    /**
     * BookingRepository Object
     *
     * @var BookingRepository
     */
    protected $bookingRepository;

    /**
     * CartRuleRepository object
     *
     * @var CartRuleRepository
     */
    protected $cartRuleRepository;

    /**
     * CartRuleCustomerRepository object
     *
     * @var CartRuleCustomerRepository
     */
    protected $cartRuleCustomerRepository;

    /**
     * CartRuleCouponRepository object
     *
     * @var CartRuleCouponRepository
     */
    protected $cartRuleCouponRepository;

    /**
     * CartRuleCouponUsageRepository object
     *
     * @var CartRuleCouponUsageRepository
     */
    protected $cartRuleCouponUsageRepository;

    /**
     * Create a new listener instance.
     *
     * @param BookingRepository $bookingRepository
     * @param CartRuleRepository $cartRuleRepository
     * @param CartRuleCustomerRepository $cartRuleCustomerRepository
     * @param CartRuleCouponRepository $cartRuleCouponRepository
     * @param CartRuleCouponUsageRepository $cartRuleCouponUsageRepository
     */
    public function __construct(
        BookingRepository $bookingRepository,
        CartRuleRepository $cartRuleRepository,
        CartRuleCustomerRepository $cartRuleCustomerRepository,
        CartRuleCouponRepository $cartRuleCouponRepository,
        CartRuleCouponUsageRepository $cartRuleCouponUsageRepository
    )
    {
        $this->bookingRepository = $bookingRepository;
        $this->cartRuleRepository = $cartRuleRepository;
        $this->cartRuleCustomerRepository = $cartRuleCustomerRepository;
        $this->cartRuleCouponRepository = $cartRuleCouponRepository;
        $this->cartRuleCouponUsageRepository = $cartRuleCouponUsageRepository;
    }

    /**
     * After sales order creation, add entry to bookings table
     *
     * @param \BagistoPackages\Shop\Contracts\Order $order
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function afterPlaceOrder($order)
    {
        $this->bookingRepository->create(['order' => $order]);
    }

    /**
     * Save cart rule and cart rule coupon properties after place order
     *
     * @param \BagistoPackages\Shop\Contracts\Order $order
     * @return void
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function manageCartRule($order)
    {
        if (!$order->discount_amount) {
            return;
        }

        $cartRuleIds = explode(',', $order->applied_cart_rule_ids);

        $cartRuleIds = array_unique($cartRuleIds);

        foreach ($cartRuleIds as $ruleId) {
            $rule = $this->cartRuleRepository->find($ruleId);

            if (!$rule) {
                continue;
            }

            $rule->update(['times_used' => $rule->times_used + 1]);

            if (!$order->customer_id) {
                continue;
            }

            $ruleCustomer = $this->cartRuleCustomerRepository->findOneWhere([
                'customer_id' => $order->customer_id,
                'cart_rule_id' => $ruleId,
            ]);

            if ($ruleCustomer) {
                $this->cartRuleCustomerRepository->update(['times_used' => $ruleCustomer->times_used + 1], $ruleCustomer->id);
            } else {
                $this->cartRuleCustomerRepository->create([
                    'customer_id' => $order->customer_id,
                    'cart_rule_id' => $ruleId,
                    'times_used' => 1,
                ]);
            }
        }

        if (!$order->coupon_code) {
            return;
        }

        $coupon = $this->cartRuleCouponRepository->findOneByField('code', $order->coupon_code);

        if ($coupon) {
            $this->cartRuleCouponRepository->update(['times_used' => $coupon->times_used + 1], $coupon->id);

            if ($order->customer_id) {
                $couponUsage = $this->cartRuleCouponUsageRepository->findOneWhere([
                    'customer_id' => $order->customer_id,
                    'cart_rule_coupon_id' => $coupon->id,
                ]);

                if ($couponUsage) {
                    $this->cartRuleCouponUsageRepository->update(['times_used' => $couponUsage->times_used + 1], $couponUsage->id);
                } else {
                    $this->cartRuleCouponUsageRepository->create([
                        'customer_id' => $order->customer_id,
                        'cart_rule_coupon_id' => $coupon->id,
                        'times_used' => 1,
                    ]);
                }
            }
        }
    }
}
