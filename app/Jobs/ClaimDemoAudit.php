<?php

namespace App\Jobs;

use App\Actions\ProvisionWorkspace;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClaimDemoAudit implements ShouldQueue
{
    // Queueable includes InteractsWithQueue and SerializesModels in Laravel 11+
    use Queueable;

    /**
     * retry_after in config/queue.php must exceed $timeout.
     * Three attempts with exponential backoff handle transient DB failures
     * without hammering the queue on a hard error.
     */
    public int $tries = 3;
    public int $timeout = 30;
    public array $backoff = [2, 5, 10];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly int $userId,
        public readonly string $demoSessionKey,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ProvisionWorkspace $provision): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            // User may have deleted their account between registration and the job running
            Log::info('ClaimDemoAudit: User not found — may have been deleted.', [
                'user_id' => $this->userId,
            ]);
            return;
        }

        $audit = Audit::withoutGlobalScopes()
            ->where('demo_session_key', $this->demoSessionKey)
            ->unclaimed()
            ->first();

        // isDemoExpired uses the property hook defined in the Audit model.
        if (! $audit || $audit->isDemoExpired) {
            Log::info('ClaimDemoAudit: Audit not found or session has expired.', [
                'demo_session_key' => $this->demoSessionKey,
            ]);
            return;
        }

        // Wrap ONLY the transfer in a transaction. 
        // Provisioning handles its own transaction internally.
        DB::transaction(function () use ($user, $audit, $provision) {

            // Single Source of Truth for setup
            $provision->execute($user);

            // Refresh to get the new workspace ID
            $user->refresh();

            // Transfer the audit
            $audit->update([
                'user_id'      => $user->id,
                'workspace_id' => $user->current_workspace_id,
                'claimed_at'   => now(),
                'is_demo'      => false,
            ]);
        });
    }


    /**
     * Called after all retry attempts are exhausted.
     * The user's demo session will not be recovered automatically.
     * A support process should handle re-claim requests.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ClaimDemoAudit: All retries exhausted – demo audit not claimed.', [
            'user_id' => $this->userId,
            'demo_session_key' => $this->demoSessionKey,
            'error' => $exception->getMessage(),
        ]);
    }
}
