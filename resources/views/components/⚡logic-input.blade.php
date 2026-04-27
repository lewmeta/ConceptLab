<?php

use App\Actions\CreateDemoAudit;
use App\Enums\ConfusionType;
use App\Models\Audit;
use App\Models\Finding;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    // ── Dependencies (constructor injection via boot) ──────────────────────

    private CreateDemoAudit $createDemoAudit;

    public function boot(CreateDemoAudit $createDemoAudit): void
    {
        $this->createDemoAudit = $createDemoAudit;
    }

    // ── State ─────────────────────────────────────────────────────────────

    #[Validate(['required', 'string', 'min:10', 'max:3000'])]
    public string $inputText = '';

    public ?int $auditId = null;

    // ── Computed properties ───────────────────────────────────────────────

    #[Computed]
    public function audit(): ?Audit
    {
        if ($this->auditId === null) {
            return null;
        }

        return Audit::withoutGlobalScopes()->find($this->auditId);
    }

    /**
     * Total finding count — resolved here, never queried in the template.
     */
    #[Computed]
    public function findingCount(): int
    {
        return $this->audit
            ? Finding::where('audit_id', $this->auditId)->count()
            : 0;
    }

    /**
     * Finding counts keyed by confusion type value.
     * One grouped query — zero queries in the template.
     *
     * Returns: ['word_concept' => 2, 'concept_theory' => 1, ...]
     */
    #[Computed]
    public function breakdownByType(): array
    {
        if (! $this->auditId) {
            return [];
        }

        return Finding::where('audit_id', $this->auditId)
            ->selectRaw('primary_confusion_type, count(*) as total')
            ->groupBy('primary_confusion_type')
            ->pluck('total', 'primary_confusion_type')
            ->toArray();
    }

    /**
     * Annotated text with <mark> spans injected right-to-left.
     * mb_str_split + array_splice — the only UTF-8-safe offset approach.
     * e() applied to attribute values only, never to raw text.
     */
    #[Computed]
    public function annotatedText(): string
    {
        $audit = $this->audit;

        if (! $audit) {
            return '';
        }

        $findings = Finding::where('audit_id', $audit->id)
            ->orderByDesc('start_char')
            ->get();

        if ($findings->isEmpty()) {
            return nl2br(e($audit->input_text));
        }

        $chars = mb_str_split($audit->input_text);

        foreach ($findings as $finding) {
            $start = $finding->start_char;
            $end   = min($finding->end_char, count($chars));

            if ($end <= $start) {
                continue;
            }

            $cssClass = $this->highlightClass($finding->primary_confusion_type);

            array_splice($chars, $end, 0, ['</mark>']);
            array_splice($chars, $start, 0, [
                '<mark'
                . ' class="finding-mark ' . $cssClass . '"'
                . ' data-finding-id="' . $finding->id . '"'
                . ' data-confusion-type="' . e($finding->primary_confusion_type->value) . '"'
                . ' title="' . e($finding->primary_confusion_type->label()) . '"'
                . '>',
            ]);
        }

        return nl2br(implode('', $chars));
    }

    // ── Actions ───────────────────────────────────────────────────────────

    /**
     * Rate-limit check first, then validate, then run the engine.
     * Rate limiter lives here — not on the GET route — because Livewire
     * actions POST to /livewire/update, outside any route middleware group.
     */
    public function analyze(): void
    {
        $key = 'demo:' . request()->ip();

        // dd($key);

        if (RateLimiter::tooManyAttempts($key, maxAttempts: 10)) {
            $this->addError('inputText', 'Too many requests. Please wait a moment before trying again.');
            return;
        }

        RateLimiter::hit($key, decaySeconds: 60);

        $this->validate();

        $audit = $this->createDemoAudit->execute($this->inputText);

        cookie()->queue(
            cookie(
                name:     'demo_session_key',
                value:    $audit->demo_session_key,
                minutes:  60 * 24,
                path:     '/',
                secure:   app()->isProduction(),
                httpOnly: true,
                sameSite: 'lax',
            )
        );

        $this->auditId = $audit->id;
    }

    /**
     * Return to the input phase.
     * Named startOver() — not reset() — to avoid shadowing Livewire internals.
     */
    public function startOver(): void
    {
        $this->auditId   = null;
        $this->inputText = '';
    }

    // public function render(): \Illuminate\View\View
    // {
    //     return view('livewire.logic-input');
    // }

    // ── Private helpers ───────────────────────────────────────────────────

    private function highlightClass(ConfusionType $type): string
    {
        return match ($type) {
            ConfusionType::WordConcept,
            ConfusionType::ConceptReferent          => 'highlight-red',
            ConfusionType::ConceptOperationalization => 'highlight-amber',
            ConfusionType::ConceptTheory,
            ConfusionType::PrototypeEssence         => 'highlight-purple',
            ConfusionType::FeatureIndicator         => 'highlight-pink',
        };
    }
};
?>

{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- INPUT PHASE                                                              --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
@if ($this->auditId === null)

    <div class="rounded-xl mt-6 border border-white/6 bg-surface p-6">

        <div class="mb-4">
            <h2 class="mb-1 text-2xl font-medium tracking-tight text-white">
                Paste your definition, claim, or brief.
            </h2>
            <p class="text-xs leading-relaxed text-white/50">
                The engine runs 60 SBA heuristics. No account required.
            </p>
        </div>

        <form wire:submit="analyze">

            {{-- Textarea --}}
            <flux:textarea
                wire:model="inputText"
                wire:loading.attr="disabled"
                placeholder="e.g. 'Customer success is the process of ensuring customers achieve their desired outcomes…'"
                rows="9"
                class="font-sans text-sm"
                autocomplete="off"
                spellcheck="false"
                @keydown.enter.exact.prevent="$el.closest('form').dispatchEvent(new Event('submit'))"
            />

            {{-- Word count + limit --}}
            <div class="mt-1.5 flex justify-between text-[11px] text-white/30">
                <span wire:key="wc">
                    {{ mb_strlen(trim($inputText)) > 0
                        ? str_word_count($inputText) . ' words'
                        : '' }}
                </span>
                <span>max 600 words</span>
            </div>

            @error('inputText')
                <p class="mt-1.5 text-xs text-red-400" role="alert">{{ $message }}</p>
            @enderror

            {{-- Submit --}}
            <flux:button
                type="submit"
                variant="primary"
                class="mt-4 w-full"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Run the Audit</span>
                <span wire:loading>Analysing…</span>
            </flux:button>

        </form>

        <p class="mt-3 text-center text-[11px] text-white/25">
            Your session is preserved when you register.
        </p>

    </div>

{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- DIAGNOSIS PHASE                                                          --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
@else

    @php $audit = $this->audit; @endphp

    <div class="rounded-xl border border-white/6 bg-surface p-6">

        {{-- Header --}}
        <div class="mb-4 flex items-center justify-between border-b border-white/6 pb-4">
            <div class="flex items-baseline gap-4">
                <span class="text-xs text-white/50">
                    {{ $this->findingCount }} {{ Str::plural('finding', $this->findingCount) }}
                </span>
                <span class="text-2xl font-medium tracking-tight text-white" title="Sharpness Score">
                    {{ number_format($audit->sharpness_score, 0) }}<span class="text-xs font-normal text-white/30">/100</span>
                </span>
            </div>

            <div class="flex items-center gap-2">
                <flux:button wire:click="startOver" variant="ghost" size="sm">
                    ← Audit new text
                </flux:button>

                @guest
                    <flux:button href="{{ route('register') }}" variant="filled" size="sm">
                        Save this audit →
                    </flux:button>
                @endguest
            </div>
        </div>

        {{-- Confusion type legend --}}
        <div class="mb-4 flex flex-wrap gap-1.5" role="list">
            @foreach (\App\Enums\ConfusionType::cases() as $type)
                <flux:badge
                    class="highlight-{{ $type->highlightGroup() }}"
                    role="listitem"
                >
                    <span
                        class="mr-1 inline-block h-1.5 w-1.5 rounded-full"
                        style="background-color: {{ $type->colour() }}"
                        aria-hidden="true"
                    ></span>
                    {{ $type->label() }}
                </flux:badge>
            @endforeach
        </div>

        {{-- Annotated text — flux:tooltip wraps each mark on hover --}}
        {{-- The marks themselves are PHP-injected; tooltip is driven by title attr --}}
        <div
            class="mb-4 cursor-default rounded-lg border border-white/6 bg-card px-5 py-4 text-sm leading-loose text-white"
        >{!! $this->annotatedText !!}</div>

        {{-- Breakdown by confusion type — reads computed property, zero queries --}}
        @if (count($this->breakdownByType) > 0)
            <div class="mb-5 flex flex-wrap gap-1.5">
                @foreach (\App\Enums\ConfusionType::cases() as $type)
                    @if (($this->breakdownByType[$type->value] ?? 0) > 0)
                        <flux:badge
                            class="highlight-{{ $type->highlightGroup() }} gap-1.5"
                        >
                            <span class="text-sm font-medium">
                                {{ $this->breakdownByType[$type->value] }}
                            </span>
                            {{ $type->label() }}
                        </flux:badge>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- CTA --}}
        @guest
            <div class="border-t border-white/6 pt-5 text-center">
                <p class="mb-3 text-xs text-white/50">
                    Register to unlock the full Socratic Sidebar, repair suggestions, and Forum Lite.
                </p>
                <flux:button href="{{ route('register') }}" variant="primary" class="w-full">
                    Continue to full diagnosis →
                </flux:button>
            </div>
        @endguest

    </div>

@endif