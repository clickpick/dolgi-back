<?php

namespace App\Http\Controllers;

use App\Services\VkCallback;
use Illuminate\Http\Request;

class VkCallbackController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param $request
     * @return void
     */
    public function __invoke(Request $request)
    {
        return (new VkCallback($request))->handle();
    }
}
