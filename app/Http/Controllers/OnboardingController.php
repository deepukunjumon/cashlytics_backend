<?php

namespace App\Http\Controllers;

use App\Enums\AccountType;
use App\Enums\ApiResponseMessage;
use App\Http\Requests\CompleteOnboardingRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;

class OnboardingController extends Controller
{
    public function complete(CompleteOnboardingRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update([
            'currency'             => $request->validated('currency'),
            'onboarding_completed' => true,
        ]);

        Account::create([
            'user_id' => $user->id,
            'name'    => 'Cash',
            'type'    => AccountType::Cash,
            'balance' => $request->validated('cash_balance'),
        ]);

        return $this->successResponse(
            $this->formatUser($user),
            ApiResponseMessage::OnboardingComplete->value
        );
    }

    private function formatUser($user): array
    {
        return [
            'id'                   => $user->id,
            'name'                 => $user->name,
            'email'                => $user->email,
            'mobile'               => $user->mobile,
            'currency'             => $user->currency,
            'onboarding_completed' => (bool) $user->onboarding_completed,
        ];
    }
}
