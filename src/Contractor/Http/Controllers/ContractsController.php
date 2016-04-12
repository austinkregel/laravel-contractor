<?php

namespace Kregel\Contractor\Http\Controllers;


class ContractsController extends Controller
{
    public function create(){
        return 'want a contract?';
    }

    public function home(){
        return view('contractor::home');
    }
}