<?php

namespace BagistoPackages\Shop\Http\Controllers;

use BagistoPackages\Shop\Repositories\BookingProductRepository;
use BagistoPackages\Shop\Helpers\DefaultSlot as DefaultSlotHelper;
use BagistoPackages\Shop\Helpers\AppointmentSlot as AppointmentSlotHelper;
use BagistoPackages\Shop\Helpers\RentalSlot as RentalSlotHelper;
use BagistoPackages\Shop\Helpers\EventTicket as EventTicketHelper;
use BagistoPackages\Shop\Helpers\TableSlot as TableSlotHelper;

class BookingProductController extends Controller
{
    /**
     * @return array
     */
    protected $bookingHelpers = [];

    /**
     * Create a new helper instance.
     *
     * @param BookingProductRepository $bookingProductRepository
     * @param DefaultSlotHelper $defaultSlotHelper
     * @param AppointmentSlotHelper $appointmentSlotHelper
     * @param RentalSlotHelper $rentalSlotHelper
     * @param EventTicketHelper $eventTicketHelper
     * @param TableSlotHelper $tableSlotHelper
     */
    public function __construct(
        BookingProductRepository $bookingProductRepository,
        DefaultSlotHelper $defaultSlotHelper,
        AppointmentSlotHelper $appointmentSlotHelper,
        RentalSlotHelper $rentalSlotHelper,
        EventTicketHelper $eventTicketHelper,
        TableSlotHelper $tableSlotHelper
    )
    {
        $this->bookingProductRepository = $bookingProductRepository;

        $this->bookingHelpers['default'] = $defaultSlotHelper;
        $this->bookingHelpers['appointment'] = $appointmentSlotHelper;
        $this->bookingHelpers['rental'] = $rentalSlotHelper;
        $this->bookingHelpers['event'] = $eventTicketHelper;
        $this->bookingHelpers['table'] = $tableSlotHelper;
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index()
    {
        $bookingProduct = $this->bookingProductRepository->find(request('id'));

        return response()->json([
            'data' => $this->bookingHelpers[$bookingProduct->type]->getSlotsByDate($bookingProduct, request()->get('date')),
        ]);
    }
}
