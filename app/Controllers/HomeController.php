<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    /**
     * Homepage - Landing Page
     */
    public function index(): string
    {
        return view('home/index');
    }
}
