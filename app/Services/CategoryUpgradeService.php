<?php

namespace App\Services;

use App\Mail\CategoryUpgradeNotificationMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Service class for client category upgrade logic.
 * Evaluates client promotion usage and upgrades categories based on configurable thresholds.
 */
class CategoryUpgradeService
{
    /**
     * Evaluate a single client's category based on accepted promotions in last 6 months.
     * Uses configurable thresholds from config/shopping.php.
     *
     * @param User $client
     * @return array ['upgraded' => bool, 'old_category' => string|null, 'new_category' => string|null]
     */
    public function evaluateClient(User $client): array
    {
        if (!$client->isClient()) {
            return [
                'upgraded' => false,
                'old_category' => null,
                'new_category' => null,
                'message' => 'User is not a client.'
            ];
        }

        $oldCategory = $client->categoria_cliente;

        // Get accepted promotions count in last 6 months
        $usageService = new PromotionUsageService();
        $acceptedCount = $usageService->getAcceptedUsageCount($client, 6);

        // Get thresholds from config
        $thresholds = config('shopping.category_thresholds', [
            'medium' => 5,
            'premium' => 15
        ]);

        $newCategory = $this->determineCategory($acceptedCount, $thresholds);

        // Check if category changed
        if ($newCategory === $oldCategory) {
            return [
                'upgraded' => false,
                'old_category' => $oldCategory,
                'new_category' => $newCategory,
                'message' => 'Client category unchanged.'
            ];
        }

        // Check if it's actually an upgrade (not a downgrade)
        $categoryLevels = ['Inicial' => 1, 'Medium' => 2, 'Premium' => 3];
        if ($categoryLevels[$newCategory] <= $categoryLevels[$oldCategory]) {
            return [
                'upgraded' => false,
                'old_category' => $oldCategory,
                'new_category' => $newCategory,
                'message' => 'Category would downgrade or stay same. No action taken.'
            ];
        }

        // Upgrade the client category
        try {
            DB::beginTransaction();

            $client->categoria_cliente = $newCategory;
            $client->save();

            // Log the upgrade event
            Log::info("Client category upgraded: User {$client->id} from {$oldCategory} to {$newCategory}", [
                'user_id' => $client->id,
                'email' => $client->nombreUsuario,
                'old_category' => $oldCategory,
                'new_category' => $newCategory,
                'accepted_count' => $acceptedCount,
                'upgraded_at' => Carbon::now()
            ]);

            // Send upgrade notification email to client
            Mail::to($client->nombreUsuario)
                ->send(new CategoryUpgradeNotificationMail($client, $oldCategory, $newCategory));

            DB::commit();

            return [
                'upgraded' => true,
                'old_category' => $oldCategory,
                'new_category' => $newCategory,
                'message' => "Client upgraded from {$oldCategory} to {$newCategory}."
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to upgrade client category: ' . $e->getMessage());
            
            return [
                'upgraded' => false,
                'old_category' => $oldCategory,
                'new_category' => $newCategory,
                'message' => 'Failed to upgrade client category.'
            ];
        }
    }

    /**
     * Determine client category based on accepted promotion count and thresholds.
     *
     * @param int $acceptedCount
     * @param array $thresholds
     * @return string 'Inicial', 'Medium', or 'Premium'
     */
    private function determineCategory(int $acceptedCount, array $thresholds): string
    {
        if ($acceptedCount >= $thresholds['premium']) {
            return 'Premium';
        }

        if ($acceptedCount >= $thresholds['medium']) {
            return 'Medium';
        }

        return 'Inicial';
    }

    /**
     * Evaluate all clients and upgrade categories where applicable.
     * Used by scheduled job for batch processing.
     *
     * @return array ['total_evaluated' => int, 'total_upgraded' => int, 'upgrades' => array]
     */
    public function evaluateAllClients(): array
    {
        $clients = User::clients()->get();
        
        $totalEvaluated = 0;
        $totalUpgraded = 0;
        $upgrades = [];

        foreach ($clients as $client) {
            $result = $this->evaluateClient($client);
            $totalEvaluated++;

            if ($result['upgraded']) {
                $totalUpgraded++;
                $upgrades[] = [
                    'client_id' => $client->id,
                    'email' => $client->email,
                    'old_category' => $result['old_category'],
                    'new_category' => $result['new_category']
                ];
            }
        }

        Log::info("Category evaluation completed: {$totalEvaluated} clients evaluated, {$totalUpgraded} upgraded", [
            'total_evaluated' => $totalEvaluated,
            'total_upgraded' => $totalUpgraded,
            'evaluated_at' => Carbon::now()
        ]);

        return [
            'total_evaluated' => $totalEvaluated,
            'total_upgraded' => $totalUpgraded,
            'upgrades' => $upgrades
        ];
    }

    /**
     * Get client's progress towards next category upgrade.
     *
     * @param User $client
     * @return array ['current_category' => string, 'accepted_count' => int, 'next_category' => string|null, 'needed_count' => int|null]
     */
    public function getClientProgress(User $client): array
    {
        if (!$client->isClient()) {
            return [
                'current_category' => null,
                'accepted_count' => 0,
                'next_category' => null,
                'needed_count' => null,
                'message' => 'User is not a client.'
            ];
        }

        $usageService = new PromotionUsageService();
        $acceptedCount = $usageService->getAcceptedUsageCount($client, 6);
        
        $thresholds = config('shopping.category_thresholds', [
            'medium' => 5,
            'premium' => 15
        ]);

        $currentCategory = $client->categoria_cliente;
        $nextCategory = null;
        $neededCount = null;

        switch ($currentCategory) {
            case 'Inicial':
                $nextCategory = 'Medium';
                $neededCount = max(0, $thresholds['medium'] - $acceptedCount);
                break;
            case 'Medium':
                $nextCategory = 'Premium';
                $neededCount = max(0, $thresholds['premium'] - $acceptedCount);
                break;
            case 'Premium':
                $nextCategory = null; // Already at highest level
                $neededCount = null;
                break;
        }

        return [
            'current_category' => $currentCategory,
            'accepted_count' => $acceptedCount,
            'next_category' => $nextCategory,
            'needed_count' => $neededCount,
            'progress_percentage' => $this->calculateProgressPercentage($currentCategory, $acceptedCount, $thresholds)
        ];
    }

    /**
     * Calculate progress percentage towards next category.
     *
     * @param string $currentCategory
     * @param int $acceptedCount
     * @param array $thresholds
     * @return float
     */
    private function calculateProgressPercentage(string $currentCategory, int $acceptedCount, array $thresholds): float
    {
        switch ($currentCategory) {
            case 'Inicial':
                $target = $thresholds['medium'];
                $current = min($acceptedCount, $target);
                return ($current / $target) * 100;
                
            case 'Medium':
                $previousThreshold = $thresholds['medium'];
                $target = $thresholds['premium'];
                $current = min($acceptedCount, $target) - $previousThreshold;
                $range = $target - $previousThreshold;
                return ($current / $range) * 100;
                
            case 'Premium':
                return 100; // Already at max
                
            default:
                return 0;
        }
    }

    /**
     * Check if thresholds are properly configured.
     *
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateThresholds(): array
    {
        $thresholds = config('shopping.category_thresholds', []);
        $errors = [];

        if (empty($thresholds['medium'])) {
            $errors[] = 'Medium category threshold not configured.';
        }

        if (empty($thresholds['premium'])) {
            $errors[] = 'Premium category threshold not configured.';
        }

        if (!empty($thresholds['medium']) && !empty($thresholds['premium'])) {
            if ($thresholds['premium'] <= $thresholds['medium']) {
                $errors[] = 'Premium threshold must be greater than Medium threshold.';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
