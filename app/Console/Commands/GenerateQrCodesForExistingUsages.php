<?php

namespace App\Console\Commands;

use App\Models\PromotionUsage;
use Illuminate\Console\Command;

class GenerateQrCodesForExistingUsages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:generate-existing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR codes for existing promotion usages that don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating QR codes for existing promotion usages...');

        $usagesWithoutQr = PromotionUsage::whereNull('codigo_qr')->get();

        if ($usagesWithoutQr->isEmpty()) {
            $this->info('All promotion usages already have QR codes!');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($usagesWithoutQr->count());
        $bar->start();

        foreach ($usagesWithoutQr as $usage) {
            $usage->code_qr = PromotionUsage::generateUniqueQrCode();
            $usage->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Generated {$usagesWithoutQr->count()} QR codes successfully!");

        return Command::SUCCESS;
    }
}
