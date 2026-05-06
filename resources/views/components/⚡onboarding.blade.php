<?php

use App\Enums\AuditStatus;
use App\Models\Audit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.web.app')] #[Title('Onboarding')] class extends Component {
    // ── State ─────────────────────────────────────────────────────────────

    /** Current step index */
    public int $step = 0;

    /** Workspace name typed by the user */
    public string $workspaceName = '';

    /** Domain profile - what world does this text live in? */
    public string $domainProfile = '';

    public function mount(): void
    {
        $user = Auth::user();

        if (request()->user()->onboarding_completed_at) {
            $this->redirectRoute('dashboard', navigate: true);
        }

        // Pre-filled workspace name from user's display name
        $this->workspaceName = $user->currentWorkspace?->name ?? "{$user->displayName}'s Workspace";
    }

    // ── Computed ──────────────────────────────────────────────────────────

    #[Computed]
    public function totalSteps(): int
    {
        return 3;
    }

    #[Computed]
    public function progressPercent(): int
    {
        return (int) round((($this->step + 1) / $this->totalSteps) * 100);
    }

    // ── Actions ───────────────────────────────────────────────────────────

    public function next(): void
    {
        $user = Auth::user();
        if ($this->step === 0) {
            $this->validateOnly('workspaceName', [
                'workspaceName' => ['required', 'string', 'max:80'],
            ]);

            $user->currentWorkspace?->update([
                'name' => $this->workspaceName,
            ]);
        }

        if ($this->step === 1 && filled($this->domainProfile)) {
            $user->currentWorkspace?->update([
                'domain_profile' => $this->domainProfile,
            ]);
        }

        if ($this->step < $this->totalSteps - 1) {
            $this->step++;
            return;
        }

        $this->complete();
    }

    public function skip(): void
    {
        $this->complete();
    }

    public function complete(): void
    {
        $user = auth()->user();

        DB::transaction(function () use ($user) {
            $user->update([
                'onboarding_completed_at' => now(),
            ]);
        });

        // If ClaimDemoAudit has already run, the user's demo audit is now
        // claimed and assigned to their workspace. Send them directly to it.
        // If the job has not run yet (queue still pending), fall back to the
        // dashboard — the audit will appear there once the job processes.
        $claimedAudit = Audit::where('status', AuditStatus::Diagnosed)->whereNotNull('claimed_at')->latest()->first();

        $destination = $claimedAudit ? route('audits.show', $claimedAudit) : route('dashboard');

        $this->redirect($destination, navigate: true);
    }
};
?>

{{-- Full-screen verlay --}}
<div class="relative w-full min-h-svh">
    <!-- Overlay -->
    <div data-state="open"
        class="fixed inset-0 z-50 bg-gray-300/30 backdrop-blur-[2.5px] data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 duration-200 ease-in-out"
        style="pointer-events: auto;" data-aria-hidden="true" aria-hidden="true"></div>


    <div {{-- class="fixed left-[50%] top-[50%] z-50 grid w-full max-w-lg max-h-[calc(100%-10.125px)] overflow-y-auto translate-x-[-50%] translate-y-[-50%] gap-5 bg-white p-5 shadow-md duration-200 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[state=closed]:slide-out-to-left-1/2 data-[state=closed]:slide-out-to-top-[48%] data-[state=open]:slide-in-from-left-1/2 data-[state=open]:slide-in-from-top-[48%] sm:rounded-3xl focus-visible:outline-0 h-full sm:h-auto sm:p-8 lg:max-w-none lg:w-max overflow-x-hidden max-sm:rounded-none!" --}}
        class="fixed left-[50%] top-[50%] z-50 grid w-full max-w-lg max-h-[90vh] overflow-y-auto translate-x-[-50%] translate-y-[-50%] gap-5 bg-white p-5 shadow-md duration-200 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[state=closed]:slide-out-to-left-1/2 data-[state=closed]:slide-out-to-top-[48%] data-[state=open]:slide-in-from-left-1/2 data-[state=open]:slide-in-from-top-[48%] sm:rounded-3xl focus-visible:outline-0 h-full sm:h-auto sm:p-8 lg:max-w-none lg:w-max overflow-x-hidden max-sm:rounded-none!"
        role="dialog">

        <div class="flex flex-col gap-5 min-w-0">
            <!-- Progress bar -->
            <div class="mb-4">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-[11px] font-medium uppercase tracking-wider text-ink-3">
                        Setup {{ $this->step + 1 }} of {{ $this->totalSteps }}
                    </span>
                    <button wire:click="skip" class="text-xs text-gray-600 p-2 rounded hover:text-gray-700" type="button">
                        Skip setup
                    </button>
                </div>
                <div class="h-0.5 w-full overflow-hidden rounded-full bg-border">
                    <div class="h-full bg-accent transition-all duration-500"
                        style="width: {{ $this->progressPercent }}%"></div>
                </div>
            </div>

            {{-- <div class="flex items-center pt-4 sm:pt-0">
                <div class="flex text-center sm:text-left flex-row space-y-0 items-center gap-4">
                    <div class="w-12 h-12 shadow-natural-xs rounded-xl flex items-center justify-center">
                        <svg viewBox="0 0 28 29" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7">
                            <path
                                d="M22.006 7.5a11.011 11.011 0 012.494 7c0 2.659-.936 5.098-2.494 7M19.163 9.337A8.126 8.126 0 0121 14.5c0 1.96-.69 3.76-1.837 5.162M16.275 11.044A5.454 5.454 0 0117.5 14.5c0 1.313-.46 2.517-1.225 3.456M13.431 12.881c.356.44.569 1.004.569 1.62 0 .614-.213 1.178-.569 1.618"
                                stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path d="M16.603 6.786a9.078 9.078 0 100 15.56" stroke="currentColor" stroke-width="2.1875"
                                stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="0 4.68"></path>
                        </svg>
                    </div>
                    <h2 class="tracking-tight font-waldenburg font-medium text-xl min-w-0 truncate">Setup account</h2>
                </div>
                <div class="hstack items-center gap-1.5 whitespace-nowrap ml-auto shrink-0">
                    <div class="flex gap-1 text-xs text-ggray-950">
                        <span class="text-ggray-500 translate-y-[0.5px]"> {{ $this->step + 1 }} of
                            {{ $this->totalSteps }}</span>
                    </div>
                </div>
            </div> --}}

            {{-- ── Step 0 — Workspace name ─────────────────────────────────── --}}
            @if ($step === 0)
                <div>
                    <div class="mb-6">
                        <h2 class="mb-2 font-medium text-xl tracking-tight text-navy">
                            Name your workspace
                        </h2>
                        <p class="text-sm text-ink-3">
                            This is how your workspace will appear to any teammates you invite later.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="workspace-name" class="mb-1.5 block text-xs font-medium text-ink-2">
                                Workspace name
                            </label>
                            <flux:input id="workspace-name" wire:model="workspaceName" type="text"
                                placeholder="e.g. Bridgespan Strategy Team" class="w-full" autofocus />
                            @error('workspaceName')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <flux:button wire:click="next" class="w-full bg-accent! hover:bg-accent-hover! text-white!">
                            Continue →
                        </flux:button>
                    </div>
                </div>

                {{-- ── Step 1 — Domain profile ──────────────────────────────────── --}}
            @elseif ($step === 1)
                <div>
                    <div class="mb-6">
                        <h2 class="mb-2 font-serif text-2xl tracking-tight text-navy">
                            What world does your text live in?
                        </h2>
                        <p class="text-sm text-ink-3">
                            This helps the engine frame its repair suggestions in your domain's language.
                            You can update this any time.
                        </p>
                    </div>

                    <div class="mb-4 grid grid-cols-2 gap-2">
                        @foreach (['NGO programme management', 'Strategy consulting', 'Academic research', 'Policy and governance', 'Product management', 'Other'] as $option)
                            <button type="button" wire:click="$set('domainProfile', '{{ $option }}')"
                                class="rounded-lg border px-3 py-2.5 text-left text-xs transition
                                {{ $domainProfile === $option
                                    ? 'border-accent bg-accent-light text-accent'
                                    : 'border-border bg-canvas text-ink-2 hover:border-accent/40 hover:bg-subtle' }}">
                                {{ $option }}
                            </button>
                        @endforeach
                    </div>

                    <div class="flex gap-2">
                        <flux:button wire:click="$set('step', 0)" variant="ghost" class="flex-1">
                            ← Back
                        </flux:button>
                        <flux:button wire:click="next" class="flex-1 !bg-accent hover:!bg-accent-hover !text-white">
                            Continue →
                        </flux:button>
                    </div>
                </div>

                {{-- ── Step 2 — Ready ───────────────────────────────────────────── --}}
            @elseif ($step === 2)
                <div class="text-center">
                    <div class="mb-5 inline-flex h-14 w-14 items-center justify-center rounded-full bg-accent-light">
                        <svg class="h-6 w-6 text-accent" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>

                    <h2 class="mb-2 font-serif text-2xl tracking-tight text-navy">
                        You're ready to audit.
                    </h2>
                    <p class="mb-8 text-sm text-ink-3">
                        Paste any definition, claim, or strategic brief into the engine.
                        The SBA heuristics will identify exactly where the language breaks down.
                    </p>

                    <flux:button wire:click="complete" class="w-full !bg-accent hover:!bg-accent-hover !text-white">
                        Go to my workspace →
                    </flux:button>
                </div>
            @endif
        </div>
    </div>
</div>
