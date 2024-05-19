<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Http\JsonResponse;

class SessionController extends Controller
{

    public function generate(): JsonResponse {
        $session = new Session();
        $session->save();

        return response()->json($session);
    }

}
