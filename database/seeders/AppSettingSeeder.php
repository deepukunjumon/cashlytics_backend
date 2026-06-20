<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $settings = [
            // General — public
            ['key' => 'app_version',      'value' => '1.0.0',                    'type' => 'string',  'group' => 'general', 'is_public' => true],
            ['key' => 'footer_text',      'value' => '© 2025 Cashlytics. All rights reserved.', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'app_name',         'value' => 'Cashlytics',           'type' => 'string',  'group' => 'general', 'is_public' => true],

            // SSO — partially public
            ['key' => 'sso_enabled',          'value' => 'false',  'type' => 'boolean', 'group' => 'sso', 'is_public' => true],
            ['key' => 'sso_provider_name',    'value' => '',       'type' => 'string',  'group' => 'sso', 'is_public' => true],
            ['key' => 'sso_client_id',        'value' => '',       'type' => 'string',  'group' => 'sso', 'is_public' => false],
            ['key' => 'sso_client_secret',    'value' => '',       'type' => 'string',  'group' => 'sso', 'is_public' => false],
            ['key' => 'sso_redirect_url',     'value' => '',       'type' => 'string',  'group' => 'sso', 'is_public' => false],
            ['key' => 'sso_authorization_url','value' => '',       'type' => 'string',  'group' => 'sso', 'is_public' => false],
            ['key' => 'sso_token_url',        'value' => '',       'type' => 'string',  'group' => 'sso', 'is_public' => false],
            ['key' => 'sso_userinfo_url',     'value' => '',       'type' => 'string',  'group' => 'sso', 'is_public' => false],
        ];

        foreach ($settings as $setting) {
            AppSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
