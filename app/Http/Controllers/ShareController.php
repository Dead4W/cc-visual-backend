<?php

namespace App\Http\Controllers;

use App\Models\Share;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShareController {

    public function save(Request $request): JsonResponse {
        $request->validate([
            "json" => "required|string",
        ]);

        $share = Share::query()
            ->where("json", $request->get("json"))
            ->first();

        if ($share === null) {
            $share = new Share();
            $share->secret = Str::uuid();
            $share->json = $request->get("json");
            $share->ip = $request->ip();
            $share->save();
        }

        return new JsonResponse([
            "id" => $share->id,
            "secret" => $share->secret,
        ]);
    }

    public function get(Request $request, string $uuid): JsonResponse {
        $request->validate([
            "secret" => "required|uuid",
        ]);

        $share = Share::query()
            ->where("secret", $uuid)
            ->firstOrFail();

        return new JsonResponse([
            "json" => $share->json,
        ]);
    }

}
