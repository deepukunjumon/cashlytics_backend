<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;

class PublicSettingsController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = AppSetting::where('is_public', true)
            ->get(['key', 'value', 'type'])
            ->mapWithKeys(function ($setting) {
                $value = match($setting->type) {
                    'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                    'json'    => json_decode($setting->value, true),
                    default   => $setting->value,
                };
                return [$setting->key => $value];
            });

        return $this->successResponse($settings);
    }
}
