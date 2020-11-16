<?php

namespace BagistoPackages\Shop\Http\Controllers;

use PDF;
use BagistoPackages\Shop\Repositories\OrderRepository;
use BagistoPackages\Shop\Repositories\InvoiceRepository;

class OrderController extends Controller
{
    /**
     * OrderRepository object
     *
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * InvoiceRepository object
     *
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * Create a new controller instance.
     *
     * @param OrderRepository $orderRepository
     * @param InvoiceRepository $invoiceRepository
     */
    public function __construct(OrderRepository $orderRepository, InvoiceRepository $invoiceRepository)
    {
        $this->middleware('customer');

        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    */
    public function index()
    {
        return view('shop::customers.account.orders.index');
    }

    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function view($id)
    {
        $order = $this->orderRepository->findOneWhere([
            'customer_id' => auth()->guard('customer')->user()->id,
            'id'          => $id,
        ]);

        if (! $order) {
            abort(404);
        }

        return view('shop::customers.account.orders.view', compact('order'));
    }

    /**
     * Print and download the for the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function print($id)
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        $pdf = PDF::loadView('shop::customers.account.orders.pdf', compact('invoice'))->setPaper('a4');

        return $pdf->download('invoice-' . $invoice->created_at->format('d-m-Y') . '.pdf');
    }

    /**
     * Cancel action for the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function cancel($id)
    {
        $result = $this->orderRepository->cancel($id);

        if ($result) {
            session()->flash('success', trans('admin::app.response.cancel-success', ['name' => 'Order']));
        } else {
            session()->flash('error', trans('admin::app.response.cancel-error', ['name' => 'Order']));
        }

        return redirect()->back();
    }
}
