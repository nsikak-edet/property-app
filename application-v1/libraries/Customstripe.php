<?php
include_once FCPATH . "application/third_party/stripe/vendor/autoload.php";

class Customstripe
{
    private $CI;
    private $stripe;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Plan_model');
        $this->CI->load->model('User_model');
        $this->CI->load->model('Account_model');
        $this->CI->load->model('Settings_model');
        $config = $this->CI->Settings_model->get_settings();
        $this->stripe = new \Stripe\StripeClient($config['stripe_private_key']);

    }

    public function getStripe()
    {
        return $this->stripe;
    }

    public function createPaymentIntent($stripeCustomerId)
    {
        $intent = $this->stripe->setupIntents->create(['customer' => $stripeCustomerId]);
        return $intent;
    }

    public function createProducts()
    {
        $products = $this->CI->Plan_model->get_plans();
        foreach ($products as $product) {
            $name = strtoupper($product->type . " >> " . $product->name);
            if ($product->stripe_product_id == null) {
                $response = $this->stripe->products->create([
                    'name' => $name
                ]);
                $updateData = array(
                    'stripe_product_id' => $response->id
                );
                $this->CI->Plan_model->update_plan($product->plan_id, $updateData);
            } else {
                $this->stripe->products->update($product->stripe_product_id, ['name' => $name]);
            }
            $product = (object)$this->CI->Plan_model->get_plan($product->plan_id);
            $this->createPrices($product);
        }

    }

    public function initializeProducts()
    {
        $products = $this->CI->Plan_model->get_plans();
        foreach ($products as $product) {
            $name = strtoupper($product->type . " >> " . $product->name);
            if ($product->stripe_product_id == null) {
                $response = $this->stripe->products->create([
                    'name' => $name
                ]);
                $updateData = array(
                    'stripe_product_id' => $response->id
                );
                $this->CI->Plan_model->update_plan($product->plan_id, $updateData);
            } else {
                $this->stripe->products->update($product->stripe_product_id, ['name' => $name]);
            }
            $product = (object)$this->CI->Plan_model->get_plan($product->plan_id);
            $this->initializePrices($product);
        }

    }

    private function initializePrices($product)
    {
        //monthly price
        if ($product->stripe_monthly_price_id == null) {
            $response = $this->stripe->prices->create([
                'currency' => 'USD',
                'product' => $product->stripe_product_id,
                'recurring' => ['interval' => 'month', 'interval_count' => 1],
                'unit_amount' => intval($product->monthly_price * 100)
            ]);
            $updateData = array(
                'stripe_monthly_price_id' => $response->id
            );
            $this->CI->Plan_model->update_plan($product->plan_id, $updateData);
        }

        //six months monthly price
        if ($product->stripe_six_monthly_price_id == null) {
            $response = $this->stripe->prices->create([
                'currency' => 'USD',
                'product' => $product->stripe_product_id,
                'recurring' => ['interval' => 'month', 'interval_count' => 6],
                'unit_amount' => intval(($product->six_monthly_price * 6 * 100))
            ]);
            $updateData = array(
                'stripe_six_monthly_price_id' => $response->id
            );
            $this->CI->Plan_model->update_plan($product->plan_id, $updateData);
        }

        //yearly 'monthly' price
        if ($product->stripe_six_monthly_price_id == null) {
            $response = $this->stripe->prices->create([
                'currency' => 'USD',
                'product' => $product->stripe_product_id,
                'recurring' => ['interval' => 'month', 'interval_count' => 12],
                'unit_amount' => intval(($product->year_monthly_price * 12 * 100))
            ]);
            $updateData = array(
                'stripe_year_monthly_price_id' => $response->id
            );
            $this->CI->Plan_model->update_plan($product->plan_id, $updateData);
        }
    }

    function updatePrices($product)
    {
        //monthly price
        if ($product->stripe_monthly_price_id == null) {
            $response = $this->stripe->prices->create([
                'currency' => 'USD',
                'product' => $product->stripe_product_id,
                'recurring' => ['interval' => 'month', 'interval_count' => 1],
                'unit_amount' => intval($product->monthly_price * 100)
            ]);
            $updateData = array(
                'stripe_monthly_price_id' => $response->id
            );
            $this->CI->Plan_model->update_plan($product->plan_id, $updateData);
        } else {
            $this->stripe->plans->delete($product->stripe_monthly_price_id,[]);
            $this->stripe->plans->create([
                'id' => $product->stripe_monthly_price_id,
                'currency' => 'usd',
                'product' => $product->stripe_product_id,
                'interval' => 'month',
                'interval_count' => 1,
                'amount' => intval($product->monthly_price * 100)
            ]);
        }

        //six months monthly price
        if ($product->stripe_six_monthly_price_id == null) {
            $response = $this->stripe->prices->create([
                'currency' => 'USD',
                'product' => $product->stripe_product_id,
                'recurring' => ['interval' => 'month', 'interval_count' => 6],
                'unit_amount' => intval(($product->six_monthly_price * 6 * 100))
            ]);
            $updateData = array(
                'stripe_six_monthly_price_id' => $response->id
            );
            $this->CI->Plan_model->update_plan($product->plan_id, $updateData);
        } else {
            $this->stripe->plans->delete($product->stripe_six_monthly_price_id,[]);
            $this->stripe->plans->create([
                'id' => $product->stripe_six_monthly_price_id,
                'currency' => 'usd',
                'product' => $product->stripe_product_id,
                'interval' => 'month',
                'interval_count' => 6,
                'amount' => intval($product->six_monthly_price * 6 * 100)
            ]);
        }

        //yearly 'monthly' price
        if ($product->stripe_six_monthly_price_id == null) {
            $response = $this->stripe->prices->create([
                'currency' => 'USD',
                'product' => $product->stripe_product_id,
                'recurring' => ['interval' => 'month', 'interval_count' => 12],
                'unit_amount' => intval(($product->year_monthly_price * 12 * 100))
            ]);
            $updateData = array(
                'stripe_year_monthly_price_id' => $response->id
            );
            $this->CI->Plan_model->update_plan($product->plan_id, $updateData);
        } else {
            $this->stripe->plans->delete($product->stripe_year_monthly_price_id,[]);
            $this->stripe->plans->create([
                'id' => $product->stripe_year_monthly_price_id,
                'currency' => 'usd',
                'product' => $product->stripe_product_id,
                'interval' => 'month',
                'interval_count' => 12,
                'amount' => intval($product->year_monthly_price * 12 * 100)
            ]);
        }
    }

    function getCustomer()
    {
        $user = $this->CI->session->userdata('user');
        $account = $this->CI->User_model->getAccountDetailsByUserId($user->user_id);
        if ($account != null) {
            if ($account->stripe_customer_id == null) {
                $customerData = [
                    'email' => $account->email,
                    'name' => $account->company_name
                ];
                $response = $this->stripe->customers->create($customerData);
                $this->CI->Account_model->updateAccountDetails(['stripe_customer_id' => $response->id], $account->account_detail_id);
                $account = $this->CI->User_model->getAccountDetailsByUserId($user->user_id);
            }

        }

        return $account;
    }

    function saveCustomerPaymentId($paymentMethodId)
    {
        $user = $this->CI->session->userdata('user');
        $account = $this->CI->User_model->getAccountDetailsByUserId($user->user_id);
        if ($account != null) {
            $this->CI->Account_model->updateAccountDetails(['stripe_payment_method_id' => $paymentMethodId], $account->account_detail_id);
            $account = $this->CI->User_model->getAccountDetailsByUserId($user->user_id);

        }
        return $account;
    }

    function getPaymentMethod()
    {
        $customer = $this->getCustomer();
        if (($customer != null) && ($customer->stripe_payment_method_id != null)) {
            $paymentMethod = $this->stripe->paymentMethods->retrieve($customer->stripe_payment_method_id);
            return $paymentMethod;
        }

        return null;
    }

    function cancelSubscription($stripeSubscriptionId){
        try {
            $subscription = $this->stripe->subscriptions->retrieve(
                $stripeSubscriptionId
            );
            $subscription->delete();
        }catch(Exception $e){
            return $e->getMessage();
        }
        return $subscription;
    }

    function getPrices($products, $stripeInterval){
        $prices = [];
        foreach($products as $product){
            $stripePrices = $this->stripe->prices->all([
                'product' => $product->stripe_product_id
            ]);

            $productPrices = $stripePrices->data;
            if((is_array($productPrices)) && (sizeof($productPrices) > 0)){
                foreach($productPrices as $price){
                    if($stripeInterval == $price->recurring->interval_count){
                        $prices[] = (object)[
                            'price_id' => $price->id,
                            'title' => ucwords($product->type) . ": " . ucwords($product->name) . " -> " . $price->recurring->interval_count . " month(s)",
                        ];
                    }
                }
            }


        }

        return $prices;

    }

    function updateSubscription($stripeSubscriptionId, $newPriceId){
        try {
            $subscription = $this->stripe->subscriptions->retrieve(
                $stripeSubscriptionId
            );

            $prorationDate = $this->CI->session->userdata('proration_date');
            $updatedSubscription = $this->stripe->subscriptions->update($stripeSubscriptionId,[
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'price' => $newPriceId,
                    ],
                ],
                'proration_date' => $prorationDate,
                'proration_behavior' => 'always_invoice',
                'billing_cycle_anchor' => 'now'
            ]);
        }catch(Exception $e){
            return $e->getMessage();
        }

        return $updatedSubscription;
    }

    function createCoupon($params){
        $couponData = [
            'id' => $params['coupon_code'],
            'currency' => 'USD',
            'duration' => 'once',
            'redeem_by' => strtotime($params['end_date']),
        ];

        //value in cents
        if($params['type'] == CouponTypes::AMOUNT_OFF){
            $couponData['amount_off'] = floatval($params['coupon_value']) * 100;
        }else{
            $couponData['percent_off'] = (floatval($params['coupon_value']) > 100) ? 0 : floatval($params['coupon_value']);
        }

        try{
            $coupon = $this->stripe->coupons->create($couponData);
            return $coupon;
        }catch(Exception $e){

        }
    }

    function deleteCoupon($couponCode){
        try{
            $this->stripe->coupons->delete($couponCode,[]);
        }catch(Exception $e){

        }
    }

    function getCoupon($couponCode){
        try{
            $coupon = $this->stripe->coupons->retrieve($couponCode,[]);
            return $coupon;
        }catch(Exception $e){
            return false;
        }
    }

    function getProratedPrice($stripeSubId, $customerId, $newPriceId){
        $subscription = $this->stripe->subscriptions->retrieve(
            $stripeSubId
        );

        $prorationDate = time();
        $this->CI->session->set_userdata('proration_date', $prorationDate);

        $invoice = $this->stripe->invoices->upcoming([
            "customer" => $customerId,
            "subscription" => $stripeSubId,
            "subscription_proration_date" => $prorationDate,
            "subscription_proration_behavior" => "always_invoice",
            "subscription_billing_cycle_anchor" => "now",
            "subscription_items" => [
                [
                    'id' => $subscription->items->data[0]->id,
                    'price' => $newPriceId
                ],
            ]
        ]);

        return $invoice;
    }

    function createPaymentSession($priceId, $customerId)
    {
        $successURl = base_url("plan/payment_success");
        $cancelURl = base_url("plan/payment_cancel");
        $session = $this->stripe->checkout->sessions->create([
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => $successURl,
            'cancel_url' => $cancelURl,
        ]);
        return $session;
    }
}