<?php

namespace App\Console\Commands;

use App\Mail\CategoryUpgradeNotificationMail;
use App\Mail\ClientVerificationMail;
use App\Mail\PromotionApprovedMail;
use App\Mail\PromotionDeniedMail;
use App\Mail\PromotionUsageAcceptedMail;
use App\Mail\PromotionUsageRejectedMail;
use App\Mail\PromotionUsageRequestMail;
use App\Mail\StoreOwnerApproved;
use App\Mail\StoreOwnerRejected;
use App\Models\Promotion;
use App\Models\PromotionUsage;
use App\Models\Store;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-emails {--send : Actually send emails instead of just rendering}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all email templates with sample data (TASK-077)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sendEmails = $this->option('send');
        
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('   ShoppingRio - Email Template Testing (TASK-077)');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        if ($sendEmails) {
            $this->warn('⚠️  SEND MODE: Emails will be sent via configured mail driver');
        } else {
            $this->info('ℹ️  RENDER MODE: Testing template rendering only (use --send to actually send)');
        }
        
        $this->newLine();

        // Get test data
        $testClient = User::where('user_type', 'cliente')->first();
        $testStoreOwner = User::where('user_type', 'dueño de local')->where('approved_at', '!=', null)->first();
        $testPendingOwner = User::where('user_type', 'dueño de local')->whereNull('approved_at')->first();
        $testStore = Store::first();
        $testPromotion = Promotion::where('status', 'aprobada')->first();
        $testUsage = PromotionUsage::where('status', 'enviada')->first();

        if (!$testClient || !$testStoreOwner || !$testStore || !$testPromotion) {
            $this->error('❌ Test data not found. Please run: php artisan migrate:fresh --seed');
            return 1;
        }

        $templates = [];
        $testEmail = 'test@example.com';

        // Test 1: Client Verification
        $this->info('Testing 1/9: ClientVerificationMail...');
        try {
            $verificationUrl = url('/verify-email/' . base64_encode($testClient->email) . '?expires=' . time());
            $mailable = new ClientVerificationMail($testClient, $verificationUrl);
            $this->testMailable($mailable, $testEmail, $sendEmails);
            $templates['ClientVerificationMail'] = '✅ PASS';
        } catch (\Exception $e) {
            $templates['ClientVerificationMail'] = '❌ FAIL: ' . $e->getMessage();
        }

        // Test 2: Store Owner Approved
        $this->info('Testing 2/9: StoreOwnerApproved...');
        try {
            $mailable = new StoreOwnerApproved($testStoreOwner);
            $this->testMailable($mailable, $testEmail, $sendEmails);
            $templates['StoreOwnerApproved'] = '✅ PASS';
        } catch (\Exception $e) {
            $templates['StoreOwnerApproved'] = '❌ FAIL: ' . $e->getMessage();
        }

        // Test 3: Store Owner Rejected
        $this->info('Testing 3/9: StoreOwnerRejected...');
        try {
            $reason = 'Documentación incompleta o datos inválidos';
            $mailable = new StoreOwnerRejected($testPendingOwner ?? $testStoreOwner, $reason);
            $this->testMailable($mailable, $testEmail, $sendEmails);
            $templates['StoreOwnerRejected'] = '✅ PASS';
        } catch (\Exception $e) {
            $templates['StoreOwnerRejected'] = '❌ FAIL: ' . $e->getMessage();
        }

        // Test 4: Promotion Approved
        $this->info('Testing 4/9: PromotionApprovedMail...');
        try {
            $mailable = new PromotionApprovedMail($testPromotion, $testStoreOwner);
            $this->testMailable($mailable, $testEmail, $sendEmails);
            $templates['PromotionApprovedMail'] = '✅ PASS';
        } catch (\Exception $e) {
            $templates['PromotionApprovedMail'] = '❌ FAIL: ' . $e->getMessage();
        }

        // Test 5: Promotion Denied
        $this->info('Testing 5/9: PromotionDeniedMail...');
        try {
            $mailable = new PromotionDeniedMail($testPromotion, $testStoreOwner);
            $this->testMailable($mailable, $testEmail, $sendEmails);
            $templates['PromotionDeniedMail'] = '✅ PASS';
        } catch (\Exception $e) {
            $templates['PromotionDeniedMail'] = '❌ FAIL: ' . $e->getMessage();
        }

        // Test 6: Promotion Usage Request
        $this->info('Testing 6/9: PromotionUsageRequestMail...');
        try {
            $mailable = new PromotionUsageRequestMail($testUsage ?? PromotionUsage::first(), $testStoreOwner);
            $this->testMailable($mailable, $testEmail, $sendEmails);
            $templates['PromotionUsageRequestMail'] = '✅ PASS';
        } catch (\Exception $e) {
            $templates['PromotionUsageRequestMail'] = '❌ FAIL: ' . $e->getMessage();
        }

        // Test 7: Promotion Usage Accepted
        $this->info('Testing 7/9: PromotionUsageAcceptedMail...');
        try {
            $mailable = new PromotionUsageAcceptedMail($testUsage ?? PromotionUsage::first(), $testClient);
            $this->testMailable($mailable, $testEmail, $sendEmails);
            $templates['PromotionUsageAcceptedMail'] = '✅ PASS';
        } catch (\Exception $e) {
            $templates['PromotionUsageAcceptedMail'] = '❌ FAIL: ' . $e->getMessage();
        }

        // Test 8: Promotion Usage Rejected
        $this->info('Testing 8/9: PromotionUsageRejectedMail...');
        try {
            $mailable = new PromotionUsageRejectedMail($testUsage ?? PromotionUsage::first(), $testClient);
            $this->testMailable($mailable, $testEmail, $sendEmails);
            $templates['PromotionUsageRejectedMail'] = '✅ PASS';
        } catch (\Exception $e) {
            $templates['PromotionUsageRejectedMail'] = '❌ FAIL: ' . $e->getMessage();
        }

        // Test 9: Category Upgrade Notification
        $this->info('Testing 9/9: CategoryUpgradeNotificationMail...');
        try {
            $mailable = new CategoryUpgradeNotificationMail($testClient, 'Inicial', 'Medium');
            $this->testMailable($mailable, $testEmail, $sendEmails);
            $templates['CategoryUpgradeNotificationMail'] = '✅ PASS';
        } catch (\Exception $e) {
            $templates['CategoryUpgradeNotificationMail'] = '❌ FAIL: ' . $e->getMessage();
        }

        // Display results
        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('   Test Results Summary');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        $passed = 0;
        $failed = 0;

        foreach ($templates as $name => $result) {
            $this->line(str_pad($name, 40) . ' : ' . $result);
            if (str_contains($result, '✅')) {
                $passed++;
            } else {
                $failed++;
            }
        }

        $this->newLine();
        $this->info('Total Templates : 9');
        $this->info('Passed          : ✅ ' . $passed);
        if ($failed > 0) {
            $this->error('Failed          : ❌ ' . $failed);
        } else {
            $this->info('Failed          : ❌ 0');
        }
        $this->newLine();

        if ($failed === 0) {
            $this->info('🎉 All email templates rendered successfully!');
            if ($sendEmails) {
                $this->info('📧 Emails sent via ' . config('mail.mailer') . ' driver');
                if (config('mail.mailer') === 'log') {
                    $this->info('📝 Check storage/logs/laravel.log for email content');
                }
            }
        } else {
            $this->error('⚠️  Some email templates failed. Please review the errors above.');
            return 1;
        }

        $this->newLine();
        
        return 0;
    }

    /**
     * Test a mailable by rendering or sending it
     */
    private function testMailable($mailable, string $toEmail, bool $send): void
    {
        if ($send) {
            Mail::to($toEmail)->send($mailable);
        } else {
            // Just render to verify no errors
            $mailable->render();
        }
    }
}
