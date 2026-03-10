<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        $keys = [
            'mail_from_name',
            'mail_from_address',
            'mail_smtp_host',
            'mail_smtp_port',
            'mail_smtp_username',
            'mail_smtp_password',
            'mail_smtp_encryption',
        ];

        $settings = Setting::query()
            ->whereIn('key', $keys)
            ->pluck('value', 'key');

        $smtpHost = (string) ($settings['mail_smtp_host'] ?? config('mail.mailers.smtp.host'));
        $smtpPort = (int) ($settings['mail_smtp_port'] ?? config('mail.mailers.smtp.port'));
        $smtpUsername = (string) ($settings['mail_smtp_username'] ?? config('mail.mailers.smtp.username'));
        $smtpPassword = (string) ($settings['mail_smtp_password'] ?? config('mail.mailers.smtp.password'));
        $smtpEncryption = (string) ($settings['mail_smtp_encryption'] ?? config('mail.mailers.smtp.encryption', 'tls'));

        $fromName = (string) ($settings['mail_from_name'] ?? config('mail.from.name'));
        $fromAddress = (string) ($settings['mail_from_address'] ?? config('mail.from.address'));

        if ($fromAddress === '' && $smtpUsername !== '') {
            $fromAddress = $smtpUsername;
        }

        if (str_contains(strtolower($smtpHost), 'gmail.com') && $smtpUsername !== '') {
            $fromAddress = $smtpUsername;
        }

        config([
            'mail.default' => 'smtp',
            'mail.from.name' => $fromName,
            'mail.from.address' => $fromAddress,
            'mail.mailers.smtp.host' => $smtpHost,
            'mail.mailers.smtp.port' => $smtpPort,
            'mail.mailers.smtp.username' => $smtpUsername,
            'mail.mailers.smtp.password' => $smtpPassword,
            'mail.mailers.smtp.encryption' => $smtpEncryption === 'none' ? null : $smtpEncryption,
        ]);
    }
}
