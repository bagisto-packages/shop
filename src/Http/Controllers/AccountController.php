<?php

namespace BagistoPackages\Shop\Http\Controllers;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('customer');

        parent::__construct();
    }

    public function index()
    {
        return view('shop::customers.account.index');
    }
}
