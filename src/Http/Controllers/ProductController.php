<?php

namespace BagistoPackages\Shop\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use BagistoPackages\Shop\Repositories\ProductRepository;
use BagistoPackages\Shop\Repositories\ProductAttributeValueRepository;
use BagistoPackages\Shop\Repositories\ProductDownloadableSampleRepository;
use BagistoPackages\Shop\Repositories\ProductDownloadableLinkRepository;

class ProductController extends Controller
{
    /**
     * ProductRepository object
     *
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * ProductAttributeValueRepository object
     *
     * @var ProductAttributeValueRepository
     */
    protected $productAttributeValueRepository;

    /**
     * ProductDownloadableSampleRepository object
     *
     * @var ProductDownloadableSampleRepository
     */
    protected $productDownloadableSampleRepository;

    /**
     * ProductDownloadableLinkRepository object
     *
     * @var ProductDownloadableLinkRepository
     */
    protected $productDownloadableLinkRepository;

    /**
     * Create a new controller instance.
     *
     * @param ProductRepository $productRepository
     * @param ProductAttributeValueRepository $productAttributeValueRepository
     * @param ProductDownloadableSampleRepository $productDownloadableSampleRepository
     * @param ProductDownloadableLinkRepository $productDownloadableLinkRepository
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductAttributeValueRepository $productAttributeValueRepository,
        ProductDownloadableSampleRepository $productDownloadableSampleRepository,
        ProductDownloadableLinkRepository $productDownloadableLinkRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->productAttributeValueRepository = $productAttributeValueRepository;
        $this->productDownloadableSampleRepository = $productDownloadableSampleRepository;
        $this->productDownloadableLinkRepository = $productDownloadableLinkRepository;
    }

    /**
     * Download image or file
     *
     * @param  int  $productId
     * @param  int  $attributeId
     * @return \Illuminate\Http\Response
     */
    public function download($productId, $attributeId)
    {
        $productAttribute = $this->productAttributeValueRepository->findOneWhere([
            'product_id'   => $productId,
            'attribute_id' => $attributeId,
        ]);

        return Storage::download($productAttribute['text_value']);
    }

    /**
     * Download the for the specified resource.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadSample()
    {
        try {
            if (request('type') == 'link') {
                $productDownloadableLink = $this->productDownloadableLinkRepository->findOrFail(request('id'));

                if ($productDownloadableLink->sample_type == 'file') {
                    return Storage::download($productDownloadableLink->sample_file);
                } else {
                    $fileName = $name = substr($productDownloadableLink->sample_url, strrpos($productDownloadableLink->sample_url, '/') + 1);

                    $tempImage = tempnam(sys_get_temp_dir(), $fileName);

                    copy($productDownloadableLink->sample_url, $tempImage);

                    return response()->download($tempImage, $fileName);
                }
            } else {
                $productDownloadableSample = $this->productDownloadableSampleRepository->findOrFail(request('id'));

                if ($productDownloadableSample->type == 'file') {
                    return Storage::download($productDownloadableSample->file);
                } else {
                    $fileName = $name = substr($productDownloadableSample->url, strrpos($productDownloadableSample->url, '/') + 1);

                    $tempImage = tempnam(sys_get_temp_dir(), $fileName);

                    copy($productDownloadableSample->url, $tempImage);

                    return response()->download($tempImage, $fileName);
                }
            }
        } catch(\Exception $e) {
            abort(404);
        }
    }
}
