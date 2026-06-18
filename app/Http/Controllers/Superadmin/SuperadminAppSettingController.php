<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuperadminAppSettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = AppSetting::orderBy('group')->orderBy('key')->get();

        $grouped = $settings->groupBy('group')->map(function ($group) {
            return $group->mapWithKeys(function ($setting) {
                return [$setting->key => [
                    'value'     => $setting->value,
                    'type'      => $setting->type,
                    'is_public' => $setting->is_public,
                ]];
            });
        });

        return $this->successResponse($grouped);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'settings'         => ['required', 'array'],
            'settings.*.key'   => ['required', 'string'],
            'settings.*.value' => ['nullable', 'string'],
        ]);

        foreach ($data['settings'] as $item) {
            AppSetting::where('key', $item['key'])->update(['value' => $item['value'] ?? '']);
        }

        return $this->successResponse(message: 'Settings updated successfully.');
    }
}
