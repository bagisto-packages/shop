<?php

namespace BagistoPackages\Shop\Helpers\Exchange;

use BagistoPackages\Shop\Repositories\CurrencyRepository;
use BagistoPackages\Shop\Repositories\ExchangeRateRepository;

class ExchangeRates extends ExchangeRate
{
    /**
     * API endpoint
     *
     * @var string
     */
    protected $apiEndPoint;

    /**
     * Holds CurrencyRepository instance
     *
     * @var CurrencyRepository
     */
    protected $currencyRepository;

    /**
     * Holds ExchangeRateRepository instance
     *
     * @var ExchangeRateRepository
     */
    protected $exchangeRateRepository;

    /**
     * Create a new helper instance.
     *
     * @param CurrencyRepository $currencyRepository
     * @param ExchangeRateRepository $exchangeRateRepository
     * @return void
     */
    public function __construct(CurrencyRepository $currencyRepository, ExchangeRateRepository $exchangeRateRepository)
    {
        $this->currencyRepository = $currencyRepository;
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->apiEndPoint = 'https://api.exchangeratesapi.io/latest';
    }

    /**
     * Fetch rates and updates in currency_exchange_rates table
     *
     * @return \Exception|void
     * @throws \Prettus\Validator\Exceptions\ValidatorException|\GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function updateRates()
    {
        $client = new \GuzzleHttp\Client();

        foreach ($this->currencyRepository->all() as $currency) {
            if ($currency->code == config('app.currency')) {
                continue;
            }

            $result = $client->request('GET', $this->apiEndPoint . '?base=' . config('app.currency') . '&symbols=' . $currency->code);

            $result = json_decode($result->getBody()->getContents(), true);

            if (isset($result['success']) && !$result['success']) {
                throw new \Exception(
                    isset($result['error']['info'])
                        ? $result['error']['info']
                        : $result['error']['type'], 1);
            }

            if ($exchangeRate = $currency->exchange_rate) {
                $this->exchangeRateRepository->update([
                    'rate' => $result['rates'][$currency->code],
                ], $exchangeRate->id);
            } else {
                $this->exchangeRateRepository->create([
                    'rate' => $result['rates'][$currency->code],
                    'target_currency' => $currency->id,
                ]);
            }
        }
    }
}
