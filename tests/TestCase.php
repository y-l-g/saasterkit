<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->fakeStripe();
        $this->setTestPrices();
    }

    private function fakeStripe(): void
    {
        $fake = new class extends \Stripe\StripeClient
        {
            public object $customers;

            public object $checkout;

            public object $billingPortal;

            public function __construct()
            {
                $this->customers = new class
                {
                    public function create(array $options = [], array $requestOptions = []): \Stripe\Customer
                    {
                        return \Stripe\Customer::constructFrom(['id' => 'cus_test']);
                    }

                    public function update(string $id, array $options = []): \Stripe\Customer
                    {
                        return \Stripe\Customer::constructFrom(['id' => $id]);
                    }

                    public function retrieve(string $id, array $options = []): \Stripe\Customer
                    {
                        return \Stripe\Customer::constructFrom(['id' => $id]);
                    }
                };

                $this->checkout = (object) [
                    'sessions' => new class
                    {
                        public function create(array $options = []): \Stripe\Checkout\Session
                        {
                            return \Stripe\Checkout\Session::constructFrom([
                                'id' => 'cs_test',
                                'url' => 'https://checkout.stripe.com/test-session',
                            ]);
                        }
                    },
                ];

                $this->billingPortal = (object) [
                    'sessions' => new class
                    {
                        /**
                         * @return array{url: string}
                         */
                        public function create(array $options = []): array
                        {
                            return ['url' => 'https://billing.stripe.com/test-session'];
                        }
                    },
                ];
            }
        };

        $this->app->bind(\Stripe\StripeClient::class, fn (): \Stripe\StripeClient => $fake);
    }

    private function setTestPrices(): void
    {
        config([
            'cashier.secret' => 'sk_test_fake',
            'prices.stripe_price_pro_month' => 'price_pro_month_test',
            'prices.stripe_price_pro_year' => 'price_pro_year_test',
            'prices.stripe_price_premium_month' => 'price_premium_month_test',
            'prices.stripe_price_premium_year' => 'price_premium_year_test',
        ]);
    }
}
