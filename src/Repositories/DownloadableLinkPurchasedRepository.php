<?php

namespace BagistoPackages\Shop\Repositories;

use Illuminate\Container\Container as App;
use BagistoPackages\Shop\Eloquent\Repository;
use BagistoPackages\Shop\Contracts\DownloadableLinkPurchased;

class DownloadableLinkPurchasedRepository extends Repository
{

    /**
     * ProductDownloadableLinkRepository object
     *
     * @var ProductDownloadableLinkRepository
     */
    protected $productDownloadableLinkRepository;

    /**
     * Create a new repository instance.
     *
     * @param ProductDownloadableLinkRepository $productDownloadableLinkRepository
     * @param App $app
     */
    public function __construct(
        ProductDownloadableLinkRepository $productDownloadableLinkRepository,
        App $app
    )
    {
        $this->productDownloadableLinkRepository = $productDownloadableLinkRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return DownloadableLinkPurchased::class;
    }

    /**
     * @param \BagistoPackages\Shop\Contracts\OrderItem $orderItem
     * @return void
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function saveLinks($orderItem)
    {
        if (!$this->isValidDownloadableProduct($orderItem)) {
            return;
        }

        foreach ($orderItem->additional['links'] as $linkId) {
            if (!$productDownloadableLink = $this->productDownloadableLinkRepository->find($linkId)) {
                continue;
            }

            $this->create([
                'name' => $productDownloadableLink->title,
                'product_name' => $orderItem->name,
                'url' => $productDownloadableLink->url,
                'file' => $productDownloadableLink->file,
                'file_name' => $productDownloadableLink->file_name,
                'type' => $productDownloadableLink->type,
                'download_bought' => $productDownloadableLink->downloads * $orderItem->qty_ordered,
                'status' => 'pending',
                'customer_id' => $orderItem->order->customer_id,
                'order_id' => $orderItem->order_id,
                'order_item_id' => $orderItem->id,
            ]);
        }
    }

    /**
     * Return true, if ordered item is valid downloadable product with links
     *
     * @param \BagistoPackages\Shop\Contracts\OrderItem $orderItem
     * @return bool
     */
    private function isValidDownloadableProduct($orderItem): bool
    {
        if (stristr($orderItem->type, 'downloadable') !== false && isset($orderItem->additional['links'])) {
            return true;
        }

        return false;
    }

    /**
     * @param \BagistoPackages\Shop\Contracts\OrderItem $orderItem
     * @param string $status
     * @return void
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateStatus($orderItem, $status)
    {
        $purchasedLinks = $this->findByField('order_item_id', $orderItem->id);

        foreach ($purchasedLinks as $purchasedLink) {
            $this->update([
                'status' => $status,
            ], $purchasedLink->id);
        }
    }
}
