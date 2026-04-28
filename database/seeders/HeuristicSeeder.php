<?php

namespace Database\Seeders;

use App\Models\Heuristic;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class HeuristicSeeder extends Seeder
{
    /**
     * Seeds all 60 heuristics from Fester Medendorp's definition-project matrix.
     *
     * Structure:
     *   6 Fundamental Confusions  (top layer)
     *   12 Systematic Errors      (middle layer)
     *   60 Concrete Errors        (bottom layer)
     *   60 Repair Heuristics      (1-to-1 with concrete errors)
     *
     * Option A: each rule has exactly one confusion_type.
     *
     * All seed rules are published immediately (published_at = seed run time).
     * supersedes_id = null for all seed rules (they have no predecessor).
     * Admin-added rules may be seeded as drafts (published_at = null).
     */
    public function run(): void
    {
        // Disable foreignKey contraints
        Schema::disableForeignKeyConstraints();

        // Reset the table and the auto-increment counter
        Heuristic::truncate();

        // Re-enable constraints before inserting new data
        Schema::enableForeignKeyConstraints();


        $publishedAt = Carbon::now();

        $rules = [

            // ════════════════════════════════════════════════════════════════
            // BLOCK 1 — Word ↔ Concept  (Errors 1–12)
            // ════════════════════════════════════════════════════════════════

            [
                'rule_number'    => 1,
                'confusion_type' => 'word_concept',
                'error_name'     => 'Term Discussion Without Concept',
                'plain_name'     => 'Stuck at the word, not the meaning',
                'trigger_logic'  => [
                    'keywords'                    => ['the word', 'the term', 'by which we mean', 'the label'],
                    'vague_patterns'              => ['what (the )?word \\w+ means', 'the term \\w+ (is|refers)'],
                    'requires_behavioral_anchor'  => true,
                ],
                'repair_template'         => 'Formulate one sentence: "By X we mean … in terms of observable or thinkable cases." Move from the label to a reference description.',
                'forum_question_template' => 'You used the term "{excerpt}" — but what specific cases does this concept include? Can you give one example and one counterexample?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 2,
                'confusion_type' => 'word_concept',
                'error_name'     => 'Circular Term Definition',
                'plain_name'     => 'Definition uses the same word it is defining',
                'trigger_logic'  => [
                    'keywords'       => [
                        'is defined as',
                        'can be defined as',
                        'which is defined',
                        'definition of',
                        'we define',
                    ],
                    'vague_patterns' => [],
                    'circular_probe' => true,
                ],
                'repair_template'         => 'Replace each word in the definition with synonyms and check for circularity. Build a list of independent features that do not reference the target word.',
                'forum_question_template' => 'Your definition of "{excerpt}" seems to rely on the concept it is defining. What is the meaning if we remove that word entirely?',
                'severity_weight'         => 3,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 3,
                'confusion_type' => 'word_concept',
                'error_name'     => 'Term Too Broad',
                'plain_name'     => 'Definition includes cases that should not belong',
                'trigger_logic'  => [
                    'keywords'       => ['any', 'all', 'every', 'everything', 'anyone', 'whatever'],
                    'vague_patterns' => ['(any|all|every) (kind|type|form|instance|case) of', 'without (exception|restriction|limitation)'],
                ],
                'repair_template'         => 'Generate 3 boundary cases that should not fall under the term. Sharpen the necessary conditions to exclude them.',
                'forum_question_template' => 'Your definition could include "{excerpt}" — does that case actually belong? If not, what condition would exclude it?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 4,
                'confusion_type' => 'word_concept',
                'error_name'     => 'Term Too Narrow',
                'plain_name'     => 'Definition excludes cases that clearly belong',
                'trigger_logic'  => [
                    'keywords'       => ['only', 'exclusively', 'solely', 'must be', 'requires'],
                    'vague_patterns' => ['only (applies|refers|counts) (to|when)', 'exclusively (for|in|when)'],
                ],
                'repair_template'         => 'Identify 3 recognized cases that fall outside the current definition. Expand the conditions to include them.',
                'forum_question_template' => 'Your definition excludes "{excerpt}" — is that intentional? Is there a recognized case that your definition would wrongly exclude?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 5,
                'confusion_type' => 'word_concept',
                'error_name'     => 'Context-Free Word Use',
                'plain_name'     => 'Meaning assumed constant across all contexts',
                'trigger_logic'  => [
                    'keywords'       => ['universally', 'in all cases', 'everywhere', 'at all times', 'regardless'],
                    'vague_patterns' => ['always (means|refers|implies)', 'in (every|all) (context|situation|domain|field)'],
                ],
                'repair_template'         => 'Add a context variable: time, place, or domain. Reformulate as "X in context C = …" and verify the meaning changes across contexts.',
                'forum_question_template' => 'You used "{excerpt}" as if its meaning is constant. How would this definition change in a different domain or time period?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 6,
                'confusion_type' => 'concept_referent',
                'error_name'     => 'Level Confusion (Word Block)',
                'plain_name'     => 'Individual, group, and system levels are mixed',
                'trigger_logic'  => [
                    'keywords'       => ['people', 'society', 'organizations', 'individuals', 'groups', 'systems'],
                    'vague_patterns' => ['(people|individuals) (and|as well as) (society|organizations|systems)', 'at (every|all) levels'],
                ],
                'repair_template'         => 'Label each usage as individual, group, or system level. Reformulate the concept separately at each level.',
                'forum_question_template' => 'Your definition mixes levels — "{excerpt}" could refer to an individual or a system. Which level does your definition target?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 7,
                'confusion_type' => 'concept_referent',
                'error_name'     => 'Timeless Word Conception',
                'plain_name'     => 'Concept treated as unchanging across time',
                'trigger_logic'  => [
                    'keywords'       => ['always has been', 'by nature', 'inherently', 'timeless', 'permanent'],
                    'vague_patterns' => ['(has always|will always) (meant|means|been)', 'is (by nature|inherently|fundamentally) \\w+'],
                ],
                'repair_template'         => 'Define X at two points in time (t1 and t2). Produce a comparison showing whether the concept is stable or changing.',
                'forum_question_template' => 'You implied "{excerpt}" is timeless. How would this concept have been understood 50 years ago? How might it change in 20 years?',
                'severity_weight'         => 1,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 8,
                'confusion_type' => 'concept_referent',
                'error_name'     => 'Domain Shift (Word Block)',
                'plain_name'     => 'Concept applied outside its original domain',
                'trigger_logic'  => [
                    'keywords'       => ['in general', 'broadly speaking', 'across disciplines', 'universally applicable'],
                    'vague_patterns' => ['applies (to|in) (all|any) (field|domain|discipline|context)', 'regardless of (field|domain|discipline)'],
                ],
                'repair_template'         => 'Define X in two distinct domains. Produce a difference matrix showing where the definitions converge and where they conflict.',
                'forum_question_template' => 'Your definition of "{excerpt}" — does it hold equally in psychology, law, and economics? Where does it break down?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 9,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Measurement Term Equals Concept',
                'plain_name'     => 'A score or test result is treated as the concept itself',
                'trigger_logic'  => [
                    'keywords'       => ['score', 'test', 'measured by', 'index', 'rating', 'IQ', 'metric'],
                    'vague_patterns' => ['(is|are) measured (by|as|through)', '(score|index|rating) (equals?|is|defines?|represents?) \\w+'],
                ],
                'repair_template'         => 'Write: "Indicator I measures aspects of X, but X also includes [list other dimensions]." Separate the construct from the measurement.',
                'forum_question_template' => 'You equated "{excerpt}" with the measurement. What aspects of the underlying concept does the score fail to capture?',
                'severity_weight'         => 3,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 10,
                'confusion_type' => 'concept_referent',
                'error_name'     => 'Word Treated as a Thing',
                'plain_name'     => 'Abstract concept given the properties of a physical object',
                'trigger_logic'  => [
                    'acting_abstracts' => ['intelligence', 'freedom', 'justice', 'culture', 'society', 'the market', 'the system', 'progress', 'the mind'],
                    'vague_patterns'   => ['\\w+ (has|contains|holds|carries|stores|moves)', '(the )?\\w+ (itself|as such) (is|does|wants|seeks)'],
                ],
                'repair_template'         => 'Reformulate X as a process or relationship rather than an object. Replace "X has Y" with "when condition Z occurs, outcome Y follows."',
                'forum_question_template' => 'You wrote "{excerpt}" as if it is a physical thing. What process or relationship is actually happening here?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 11,
                'confusion_type' => 'word_concept',
                'error_name'     => 'Normative Word Use',
                'plain_name'     => 'Value judgments hidden inside a descriptive word',
                'trigger_logic'  => [
                    'keywords'       => ['good', 'bad', 'proper', 'correct', 'real', 'true', 'genuine', 'authentic', 'legitimate'],
                    'vague_patterns' => ['(true|real|genuine|proper|correct) \\w+', '(good|bad|right|wrong) (kind|type|form|example) of'],
                ],
                'repair_template'         => 'Mark every value-laden term in the sentence. Produce two separate sentences: one purely descriptive, one explicitly normative.',
                'forum_question_template' => 'The word "{excerpt}" carries a value judgment. What would a neutral, purely descriptive version of this sentence look like?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 12,
                'confusion_type' => 'prototype_essence',
                'error_name'     => 'Prototype-Dominant Term',
                'plain_name'     => 'A typical example is mistaken for the essential definition',
                'trigger_logic'  => [
                    'keywords'       => ['typically', 'usually', 'the classic case', 'the standard', 'everyone knows'],
                    'vague_patterns' => ['the (typical|classic|standard|usual|normal) (case|example|instance) of', 'we all (know|understand|recognize) that \\w+'],
                ],
                'repair_template'         => 'Generate 3 atypical examples of the concept. Adjust the definition to include them without relying on the typical case.',
                'forum_question_template' => 'Your definition works for the typical case of "{excerpt}" — but would it still hold for an unusual or edge-case example?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            // ════════════════════════════════════════════════════════════════
            // BLOCK 2 — Concept ↔ Theory  (Errors 13–24)
            // ════════════════════════════════════════════════════════════════

            [
                'rule_number'    => 13,
                'confusion_type' => 'concept_theory',
                'error_name'     => 'Definition Treated as Factual Claim',
                'plain_name'     => 'A naming convention presented as an empirical truth',
                'trigger_logic'  => [
                    'keywords'       => ['it is a fact', 'research shows', 'studies confirm', 'evidence proves'],
                    'vague_patterns' => ['it (is|has been) (proven|shown|demonstrated) that \\w+ (is|means)', 'by definition it (is|must be|follows)'],
                ],
                'repair_template'         => 'Label each sentence as stipulative (a convention) or empirical (a testable claim). Keep them in separate lists.',
                'forum_question_template' => 'Is "{excerpt}" a definitional choice or an empirical finding? What would it take to prove it wrong?',
                'severity_weight'         => 3,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 14,
                'confusion_type' => 'concept_theory',
                'error_name'     => 'Circular Theory',
                'plain_name'     => 'Theory explains and is explained by the same thing',
                'trigger_logic'  => [
                    'keywords'       => [],
                    'circular_probe' => true,
                    'vague_patterns' => ['because of \\w+.{0,40}therefore \\w+.{0,40}because', '\\w+ (leads to|causes) \\w+.{0,60}\\w+ (leads to|causes) \\w+'],
                ],
                'repair_template'         => 'Separate what explains from what is explained. Produce a clear schema: cause → effect, with no mutual dependence.',
                'forum_question_template' => 'Your explanation of "{excerpt}" seems to loop back on itself. What is the independent cause and what is the dependent effect?',
                'severity_weight'         => 3,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 15,
                'confusion_type' => 'concept_theory',
                'error_name'     => 'Theory Too Broad',
                'plain_name'     => 'Theory cannot be falsified by any conceivable evidence',
                'trigger_logic'  => [
                    'keywords'          => ['always', 'inevitably', 'by necessity', 'in all cases', 'without exception'],
                    'vague_patterns'    => ['will (always|inevitably|necessarily) (result|lead|cause)', 'cannot (fail|be wrong|be disproven)'],
                    'flag_immunization' => true,
                ],
                'repair_template'         => 'Formulate a falsification condition: "This theory would fail if [condition]." If no such condition exists, the theory is unfalsifiable.',
                'forum_question_template' => 'What evidence would convince you that "{excerpt}" is false? If nothing could, the claim may not be a theory at all.',
                'severity_weight'         => 3,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 16,
                'confusion_type' => 'concept_theory',
                'error_name'     => 'Theory Too Narrow',
                'plain_name'     => 'Theory only works on the original test case',
                'trigger_logic'  => [
                    'keywords'       => ['in this specific case', 'in our study', 'for this sample', 'in this context only'],
                    'vague_patterns' => ['only (applies?|works?) (in|for|when)', 'limited to (this|our|the specific)'],
                ],
                'repair_template'         => 'Test the theory on 3 new cases outside the original sample. Produce an expansion rule that makes the theory more general.',
                'forum_question_template' => 'Your theory about "{excerpt}" was developed in one context. Does it hold when applied to [different domain or population]?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 17,
                'confusion_type' => 'concept_theory',
                'error_name'     => 'Context-Free Theory',
                'plain_name'     => 'Theory claims universality without boundary conditions',
                'trigger_logic'  => [
                    'keywords'       => ['universally', 'everywhere', 'cross-culturally', 'in all societies'],
                    'vague_patterns' => ['applies? (universally|everywhere|in all)', 'holds? (across|for all|regardless of)'],
                ],
                'repair_template'         => 'Specify the boundary conditions. Produce a domain of application: "This theory applies when [condition] and does not apply when [condition]."',
                'forum_question_template' => 'You claimed "{excerpt}" holds universally. In which cultures, time periods, or domains might this not be true?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 18,
                'confusion_type' => 'concept_theory',
                'error_name'     => 'Level Confusion (Theory Block)',
                'plain_name'     => 'Theory mixes variables from different levels of analysis',
                'trigger_logic'  => [
                    'keywords'       => ['individuals', 'groups', 'organizations', 'society', 'nations'],
                    'vague_patterns' => ['(individual|personal) \\w+ (and|affects?|drives?) (social|collective|systemic)', '(society|culture|the system) (makes?|causes?|forces?) (individuals?|people)'],
                ],
                'repair_template'         => 'Map each variable to its correct level of analysis. Build a consistent model where all variables operate at the same level.',
                'forum_question_template' => 'Your theory mixes individual and systemic variables around "{excerpt}." Which level is the theory actually about?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 19,
                'confusion_type' => 'concept_theory',
                'error_name'     => 'Temporal Confusion (Theory Block)',
                'plain_name'     => 'Theory cannot account for change over time',
                'trigger_logic'  => [
                    'keywords'       => ['static', 'fixed', 'permanent', 'constant', 'does not change'],
                    'vague_patterns' => ['is (and will always be|permanently|forever)', 'remains (unchanged|constant|fixed|stable)'],
                ],
                'repair_template'         => 'Distinguish between a static model and a dynamic model. Add a time parameter and show how the theory handles change.',
                'forum_question_template' => 'Your theory treats "{excerpt}" as static. How would the theory account for it changing over a 10-year period?',
                'severity_weight'         => 1,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 20,
                'confusion_type' => 'concept_theory',
                'error_name'     => 'Domain Migration (Theory Block)',
                'plain_name'     => 'Theory transplanted between disciplines without validation',
                'trigger_logic'  => [
                    'keywords'       => ['borrowed from', 'as in physics', 'like in economics', 'the same principle'],
                    'vague_patterns' => ['the same (principle|logic|law|rule) (applies?|holds?) (in|for)', 'borrowing (from|the concept of) \\w+ (from|in)'],
                ],
                'repair_template'         => 'Test the assumptions of the theory per domain. Produce a list of which assumptions are valid and which are not in the new domain.',
                'forum_question_template' => 'You applied "{excerpt}" from one discipline to another. Which assumptions break down in the new domain?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 21,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Operationalization Equals Theory',
                'plain_name'     => 'Theory collapses into a single way of measuring it',
                'trigger_logic'  => [
                    'keywords'       => ['operationalized as', 'defined operationally', 'measured exclusively by'],
                    'vague_patterns' => ['(the theory|\\w+) is (operationalized|defined|measured) (as|by|through)', 'we (define|measure|operationalize) \\w+ (as|using|with) (the|a|our)'],
                ],
                'repair_template'         => 'Write at least two alternative operationalizations for the construct. If only one is possible, the theory has been reduced to its measurement.',
                'forum_question_template' => 'You operationalized "{excerpt}" in one specific way. How would the theory change if you measured it differently?',
                'severity_weight'         => 3,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 22,
                'confusion_type' => 'concept_theory',
                'error_name'     => 'Reified Theory',
                'plain_name'     => 'Theoretical model treated as physical reality',
                'trigger_logic'  => [
                    'acting_abstracts' => ['the model', 'the framework', 'the theory', 'the construct', 'the structure', 'the system'],
                    'vague_patterns'   => ['the (model|framework|theory|construct) (shows?|proves?|demonstrates?|reveals?)', 'according to (the model|the framework|the theory) \\w+ (is|does|causes?)'],
                ],
                'repair_template'         => 'Label each model component explicitly as an abstraction. Add the phrase "the model assumes" or "the model predicts" before each claim.',
                'forum_question_template' => 'You wrote "{excerpt}" as if the model is reality. What would the real-world equivalent of this claim look like?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 23,
                'confusion_type' => 'concept_theory',
                'error_name'     => 'Normative Theory Disguised as Empirical',
                'plain_name'     => 'Value premises hidden inside scientific-sounding claims',
                'trigger_logic'  => [
                    'keywords'       => ['should', 'ought', 'must', 'need to', 'it is important', 'it is necessary'],
                    'vague_patterns' => ['(research|evidence|data) (shows?|suggests?|confirms?) (that )?\\w+ should', 'it (is|has been) (shown|proven|established) that \\w+ (must|should|ought)'],
                ],
                'repair_template'         => 'Identify every value premise in the argument. Produce an explicit value framework separate from the empirical claims.',
                'forum_question_template' => 'Your claim about "{excerpt}" sounds empirical but contains a value judgment. What is the underlying normative premise?',
                'severity_weight'         => 3,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 24,
                'confusion_type' => 'concept_theory',
                'error_name'     => 'Prototype-Based Theory',
                'plain_name'     => 'Theory built on a typical case, breaks at edge cases',
                'trigger_logic'  => [
                    'keywords'       => ['the typical', 'the standard case', 'normally', 'in most cases', 'the average'],
                    'vague_patterns' => ['the (typical|standard|normal|average|usual) (case|example|instance)', 'in (most|many|the majority of) (cases|instances|situations)'],
                ],
                'repair_template'         => 'Test the theory on extreme and atypical cases. Produce a robustness score showing how many edge cases the theory handles correctly.',
                'forum_question_template' => 'Your theory works for "{excerpt}" in the typical case. How does it perform on an unusual or extreme case?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            // ════════════════════════════════════════════════════════════════
            // BLOCK 3 — Concept ↔ Operationalization  (Errors 25–36)
            // ════════════════════════════════════════════════════════════════

            [
                'rule_number'    => 25,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Indicator Equals Property',
                'plain_name'     => 'A measurement proxy treated as the actual construct',
                'trigger_logic'  => [
                    'keywords'       => ['measured by', 'indicated by', 'captured by', 'assessed through'],
                    'vague_patterns' => ['\\w+ (is|equals?|equals) (the score|the test|the measurement|the index)', '(the score|the result|the measurement) (of|on|from) \\w+ (is|equals?) \\w+'],
                ],
                'repair_template'         => 'Define the construct independently of any measurement. Produce a construct description that could exist without the indicator.',
                'forum_question_template' => 'You treated "{excerpt}" as if the measurement and the concept are the same. What aspects of the concept does the measurement miss?',
                'severity_weight'         => 3,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 26,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Circular Measurement',
                'plain_name'     => 'Validity criterion overlaps conceptually with what is measured',
                'trigger_logic'  => [
                    'keywords'       => ['validated by', 'confirmed by', 'correlated with'],
                    'vague_patterns' => ['validated (against|by|using) (the same|a similar|another measure of)', 'correlates (with|to) (itself|the same construct|a related measure)'],
                    'circular_probe' => true,
                ],
                'repair_template'         => 'Use an external criterion that does not conceptually overlap with the construct. Produce a validation correlation against an independent measure.',
                'forum_question_template' => 'How did you validate "{excerpt}"? Is the validation criterion truly independent of the construct being measured?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 27,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Indicator Too Broad',
                'plain_name'     => 'Measurement captures irrelevant cases (false positives)',
                'trigger_logic'  => [
                    'keywords'       => ['any positive result', 'any occurrence', 'any instance of'],
                    'vague_patterns' => ['any (positive|above-zero|non-negative|occurring)', 'all (cases|instances|occurrences) (of|where|in which)'],
                ],
                'repair_template'         => 'Perform a discrimination analysis. List the false positives the indicator captures and add exclusion criteria to eliminate them.',
                'forum_question_template' => 'Your measure of "{excerpt}" — what irrelevant cases would it flag as positive? What false positives does it produce?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 28,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Indicator Too Narrow',
                'plain_name'     => 'Measurement misses relevant cases (false negatives)',
                'trigger_logic'  => [
                    'keywords'       => ['only counts', 'only measures', 'restricted to', 'limited to measuring'],
                    'vague_patterns' => ['only (measures?|counts?|captures?) \\w+ (when|if|that)', 'does not (capture|measure|include) (cases?|instances?) (where|when|in which)'],
                ],
                'repair_template'         => 'Perform a sensitivity analysis. List the false negatives the indicator misses and expand the criteria to include them.',
                'forum_question_template' => 'What relevant cases of "{excerpt}" would your measure fail to detect? What false negatives does it produce?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 29,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Context-Free Measurement',
                'plain_name'     => 'Score assumed stable across contexts without calibration',
                'trigger_logic'  => [
                    'keywords'       => ['regardless of context', 'context-independent', 'comparable across'],
                    'vague_patterns' => ['the (score|measure|result) (is|remains) (the same|comparable|equivalent) (across|in all)', 'can be (compared|used) (across|in all|regardless of) (contexts?|domains?|cultures?)'],
                ],
                'repair_template'         => 'Calibrate the measurement per context. Produce a correction factor for each major context where the score is used.',
                'forum_question_template' => 'Does your measure of "{excerpt}" produce comparable scores in different cultures, settings, or populations? What calibration is needed?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 30,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Level Confusion (Measurement Block)',
                'plain_name'     => 'Measurement unit applied at the wrong level of analysis',
                'trigger_logic'  => [
                    'keywords'       => ['per person', 'per group', 'national average', 'team score'],
                    'vague_patterns' => ['(individual|group|national|organizational) (score|average|level|rate)', '(aggregate|sum|average) (of|across) (individuals?|groups?|teams?)'],
                ],
                'repair_template'         => 'Check whether the measurement unit matches the level of analysis. Correct the scale to match the level at which the concept operates.',
                'forum_question_template' => 'You measured "{excerpt}" at the individual level — but is that the right unit? Should this be measured at a group or organizational level?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 31,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Time-Insensitive Measurement',
                'plain_name'     => 'Single snapshot measurement used for a dynamic construct',
                'trigger_logic'  => [
                    'keywords'       => ['at one point', 'at baseline', 'single measurement', 'one-time assessment'],
                    'vague_patterns' => ['(measured|assessed|evaluated) (at|during) (a single|one|baseline)', '(the|a) (single|one-time|baseline) (measurement|assessment|score|test)'],
                ],
                'repair_template'         => 'Repeat the measurement at multiple time points. Produce a time series that captures the dynamics of the construct.',
                'forum_question_template' => 'You measured "{excerpt}" at a single point in time. How would the score change over a 6-month period? Is one measurement enough?',
                'severity_weight'         => 1,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 32,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Domain Shift (Measurement Block)',
                'plain_name'     => 'Measurement tool used outside its validated domain',
                'trigger_logic'  => [
                    'keywords'       => ['adapted from', 'translated from', 'originally designed for'],
                    'vague_patterns' => ['(adapted|borrowed|translated) (from|for use in)', 'originally (designed|developed|validated) (for|in)'],
                ],
                'repair_template'         => 'Cross-validate the instrument in the new domain. Report performance per domain and flag where generalization fails.',
                'forum_question_template' => 'Your measure of "{excerpt}" was developed in a different context. Has it been validated in the context where you are applying it?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 33,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Reduction to a Single Score',
                'plain_name'     => 'Multi-dimensional construct collapsed into one number',
                'trigger_logic'  => [
                    'keywords'       => ['total score', 'composite score', 'single index', 'overall rating'],
                    'vague_patterns' => ['(the|a) (total|composite|single|overall|aggregate) (score|index|rating|number)', 'collapsed (into|to) (a single|one) (number|score|value)'],
                ],
                'repair_template'         => 'Add a second independent indicator for the same construct. Produce a multi-metric profile rather than a single score.',
                'forum_question_template' => 'You reduced "{excerpt}" to a single score. What dimensions of the construct does that score not capture?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 34,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Reified Score',
                'plain_name'     => 'Estimate or approximation treated as a certain fact',
                'trigger_logic'  => [
                    'keywords'       => ['the score is', 'the result is', 'the measurement shows', 'proves'],
                    'vague_patterns' => ['the (score|result|measurement|test|data) (proves?|shows?|confirms?) (that|this)', '\\w+ (scored?|achieved?|obtained?) \\d+ (which|so|therefore)'],
                ],
                'repair_template'         => 'Reformulate the score as an estimate. Add a confidence interval and acknowledge the measurement error.',
                'forum_question_template' => 'You stated "{excerpt}" as a certainty. What is the measurement error or confidence interval around this score?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 35,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Normative Bias in Measurement',
                'plain_name'     => 'Measurement instrument contains hidden value judgments',
                'trigger_logic'  => [
                    'keywords'       => ['desirable', 'appropriate', 'correct answer', 'ideal score', 'expected response'],
                    'vague_patterns' => ['(the|a) (desirable|appropriate|correct|ideal|expected) (score|answer|response|level)', 'measures (how well|how much|the degree to which) \\w+ (should|ought|is expected)'],
                ],
                'repair_template'         => 'Conduct a bias audit on the measurement instrument. Produce a bias report identifying where value judgments are embedded in the scoring.',
                'forum_question_template' => 'Does your measurement of "{excerpt}" assume a particular response is better or more correct? Where is the normative judgment in the instrument?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 36,
                'confusion_type' => 'concept_operationalization',
                'error_name'     => 'Prototype-Based Indicator',
                'plain_name'     => 'Measurement calibrated on typical cases, fails on outliers',
                'trigger_logic'  => [
                    'keywords'       => ['calibrated on', 'normed on', 'based on typical', 'validated on standard cases'],
                    'vague_patterns' => ['(normed?|calibrated|standardized|validated) (on|against|using) (typical|standard|normal|average)', 'developed (using|with|based on) (a representative|the typical|standard)'],
                ],
                'repair_template'         => 'Test the indicator on outliers and atypical cases. Produce a robustness report showing where the indicator fails outside typical cases.',
                'forum_question_template' => 'Your measure of "{excerpt}" was validated on typical cases. How does it perform on unusual or extreme cases?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            // ════════════════════════════════════════════════════════════════
            // BLOCK 4 — Prototype ↔ Essence  (Errors 37–48)
            // ════════════════════════════════════════════════════════════════

            [
                'rule_number'    => 37,
                'confusion_type' => 'prototype_essence',
                'error_name'     => 'Prototype Equals Definition',
                'plain_name'     => 'A typical example treated as the essential definition',
                'trigger_logic'  => [
                    'keywords'       => ['the classic example', 'the typical case', 'think of', 'consider', 'take for example'],
                    'vague_patterns' => ['(think of|consider|take) (the (typical|classic|standard|usual)) (case|example)', 'a (good|typical|classic|perfect) example (of|is)'],
                ],
                'repair_template'         => 'Identify the necessary features that all instances must have. Produce a minimal set of conditions that is not derived from the example.',
                'forum_question_template' => 'You described "{excerpt}" using a typical example. What are the necessary and sufficient conditions that every instance must meet?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 38,
                'confusion_type' => 'prototype_essence',
                'error_name'     => 'Circular Prototypes',
                'plain_name'     => 'Examples used to define the concept that selects the examples',
                'trigger_logic'  => [
                    'keywords'       => [],
                    'circular_probe' => true,
                    'vague_patterns' => ['these (cases|examples|instances) are (\\w+ )?because they (are|fit|match)', '\\w+ (are|qualify) (as|because) they (are|represent|exemplify) \\w+'],
                ],
                'repair_template'         => 'Add external criteria for selecting the examples that are independent of the concept being defined. Run an independent test.',
                'forum_question_template' => 'How did you select the examples of "{excerpt}"? Were they chosen because they fit the definition — which was built from those examples?',
                'severity_weight'         => 3,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 39,
                'confusion_type' => 'prototype_essence',
                'error_name'     => 'Prototype Too Broad',
                'plain_name'     => 'Prototype includes accidental features that should be excluded',
                'trigger_logic'  => [
                    'keywords'       => ['also', 'additionally', 'furthermore', 'as well as', 'along with'],
                    'vague_patterns' => ['(also|additionally|furthermore|typically) (has|includes?|involves?|features?)', '(and|as well as|along with) (usually|often|typically|generally)'],
                ],
                'repair_template'         => 'Remove accidental features from the prototype description. Produce a cleaned-up set of features that are all necessary.',
                'forum_question_template' => 'Your prototype for "{excerpt}" includes several features — which ones are essential and which are just common but not required?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 40,
                'confusion_type' => 'prototype_essence',
                'error_name'     => 'Prototype Too Narrow',
                'plain_name'     => 'Prototype misses important variations of the concept',
                'trigger_logic'  => [
                    'keywords'       => ['must have', 'requires', 'always includes', 'necessarily'],
                    'vague_patterns' => ['(must|always|necessarily) (have|include|contain|involve)', 'requires (the presence of|both|all of)'],
                ],
                'repair_template'         => 'Add variation to the prototype. Produce an expanded spectrum that includes less typical but still legitimate instances.',
                'forum_question_template' => 'Your prototype of "{excerpt}" seems to require very specific features. Are there legitimate instances that lack one of those features?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 41,
                'confusion_type' => 'prototype_essence',
                'error_name'     => 'Context-Bound Prototype',
                'plain_name'     => 'Prototype from one context generalized without justification',
                'trigger_logic'  => [
                    'keywords'       => ['in our culture', 'in western society', 'in this field', 'in our experience'],
                    'vague_patterns' => ['in (our|western|this|the|modern) (culture|society|context|field|experience)', '(from|in) (our|this|the) (perspective|tradition|discipline)'],
                ],
                'repair_template'         => 'Compare the prototype across different contexts. Produce a context matrix showing where the prototype holds and where it does not.',
                'forum_question_template' => 'Your prototype for "{excerpt}" reflects one cultural or disciplinary context. How would the prototype differ in another?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 42,
                'confusion_type' => 'prototype_essence',
                'error_name'     => 'Level Confusion (Prototype Block)',
                'plain_name'     => 'Prototype mixes individual and collective instances',
                'trigger_logic'  => [
                    'keywords'       => ['people', 'organizations', 'societies', 'families', 'groups'],
                    'vague_patterns' => ['(an individual|a person|someone) (and|or) (a group|an organization|a society)', 'both (individual|personal) and (collective|group|organizational)'],
                ],
                'repair_template'         => 'Split the prototype by level: individual, group, and system. Produce multiple prototypes, one per level.',
                'forum_question_template' => 'Your prototype of "{excerpt}" — is it an individual instance or a collective one? Can the same prototype apply at both levels?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 43,
                'confusion_type' => 'prototype_essence',
                'error_name'     => 'Temporary Prototype',
                'plain_name'     => 'Historically contingent example treated as timeless',
                'trigger_logic'  => [
                    'keywords'       => ['the classic', 'the original', 'the archetypal', 'as always', 'the traditional'],
                    'vague_patterns' => ['the (classic|original|archetypal|traditional|historical) (example|case|instance)', 'as (it has always|traditionally|historically) (been|meant|referred)'],
                ],
                'repair_template'         => 'Perform a historical comparison of the prototype. Show how the typical example has evolved and produce a description of that evolution.',
                'forum_question_template' => 'Your prototype for "{excerpt}" — was it the same 50 years ago? How has the typical example changed over time?',
                'severity_weight'         => 1,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 44,
                'confusion_type' => 'prototype_essence',
                'error_name'     => 'Domain-Specific Prototype',
                'plain_name'     => 'Prototype from one discipline imposed on another',
                'trigger_logic'  => [
                    'keywords'       => ['as in psychology', 'as economists define', 'in legal terms', 'from a medical perspective'],
                    'vague_patterns' => ['(as|like) (psychologists?|economists?|lawyers?|doctors?) (define|describe|see|understand)', '(from a|in) (psychological|economic|legal|medical|sociological) (perspective|framework|sense)'],
                ],
                'repair_template'         => 'Run a cross-domain test of the prototype. Produce a report of the differences across disciplines.',
                'forum_question_template' => 'Your prototype for "{excerpt}" comes from one discipline. How would a practitioner in a different field describe the same concept?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 45,
                'confusion_type' => 'prototype_essence',
                'error_name'     => 'Measurement Prototype',
                'plain_name'     => 'A measured example confused with the essential criterion',
                'trigger_logic'  => [
                    'keywords'       => ['as shown by', 'as measured in', 'as demonstrated by the test'],
                    'vague_patterns' => ['(as shown|as measured|as demonstrated|as indicated) (by|in|through) (the|a)', 'the (best|clearest|most common) example is (when|where|someone who) (scores?|measures?)'],
                ],
                'repair_template'         => 'Separate the example from the measurement criterion. Produce two distinct definitions: what the example shows and what the criterion measures.',
                'forum_question_template' => 'You used "{excerpt}" as both an example and a criterion. Are these actually the same, or is the example just one possible indicator?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 46,
                'confusion_type' => 'prototype_essence',
                'error_name'     => 'Reified Prototype',
                'plain_name'     => 'Heuristic example treated as if it defines the essence',
                'trigger_logic'  => [
                    'acting_abstracts' => ['the prototype', 'the ideal type', 'the archetype', 'the paradigm case'],
                    'vague_patterns'   => ['the (prototype|ideal type|archetype|paradigm case) (is|represents?|defines?|captures?)', '(this|the) (case|example|instance) (is|represents?) the (essence|core|heart|definition)'],
                ],
                'repair_template'         => 'Label the prototype explicitly as a heuristic tool, not a definition. Produce a usage rule that explains when the prototype applies and when it does not.',
                'forum_question_template' => 'You described "{excerpt}" as if the prototype is the essence. What are the boundaries of this prototype — when does it stop being a reliable guide?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 47,
                'confusion_type' => 'prototype_essence',
                'error_name'     => 'Normative Prototype',
                'plain_name'     => 'Ideal case presented as a factual description',
                'trigger_logic'  => [
                    'keywords'       => ['ideally', 'the ideal', 'in a perfect case', 'at its best', 'the exemplary'],
                    'vague_patterns' => ['(ideally|at its best|in the ideal case|in a perfect world)', 'the (ideal|exemplary|perfect|best) (case|example|instance|form) (of|is)'],
                ],
                'repair_template'         => 'Split the description into two: what the ideal looks like and what the factual reality is. Produce two separate descriptions.',
                'forum_question_template' => 'You described "{excerpt}" using an ideal case. Is this what actually happens, or what you think should happen?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 48,
                'confusion_type' => 'prototype_essence',
                'error_name'     => 'Dominant Typical Case',
                'plain_name'     => 'Definition only works for the most common instance',
                'trigger_logic'  => [
                    'keywords'       => ['the most common', 'the standard form', 'the familiar', 'everyone recognizes'],
                    'vague_patterns' => ['the (most common|most familiar|most recognized|standard) (form|type|version|example)', '(everyone|most people) (knows?|recognizes?|understands?) (that |what )?\\w+'],
                ],
                'repair_template'         => 'Force counterexamples. Generate 3 cases that are not the typical instance and adjust the definition to include them.',
                'forum_question_template' => 'Your definition of "{excerpt}" works for the most common case. Name one instance that is unusual but still legitimate — does your definition cover it?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            // ════════════════════════════════════════════════════════════════
            // BLOCK 5 — Feature ↔ Indicator / Normative ↔ Descriptive  (49–60)
            // ════════════════════════════════════════════════════════════════

            [
                'rule_number'    => 49,
                'confusion_type' => 'feature_indicator',
                'error_name'     => 'Value Treated as Fact',
                'plain_name'     => 'Evaluative claim written as if it is a neutral description',
                'trigger_logic'  => [
                    'keywords'          => ['clearly', 'obviously', 'naturally', 'of course', 'it goes without saying'],
                    'vague_patterns'    => ['(clearly|obviously|naturally|of course|it is (clear|obvious)) (that )?\\w+', '(everyone|it is) (knows?|agreed|accepted|understood) (that )?\\w+'],
                    'flag_immunization' => true,
                ],
                'repair_template'         => 'Rewrite the sentence without any evaluative language. Produce a neutral, purely descriptive version.',
                'forum_question_template' => 'You wrote "{excerpt}" as a fact. Is this a description of what is, or a judgment about what is good or bad?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 50,
                'confusion_type' => 'feature_indicator',
                'error_name'     => 'Circular Norm',
                'plain_name'     => 'A norm justified only by reference to itself',
                'trigger_logic'  => [
                    'keywords'       => ['because it is right', 'because that is how it should be', 'by its very nature'],
                    'vague_patterns' => ['(should|ought|must) \\w+.{0,40}because (it is|that is) (right|correct|proper|how it)', '(it is|this is) (right|proper|correct) (because|since|as) (it is|that is)'],
                    'circular_probe' => true,
                ],
                'repair_template'         => 'Provide an external justification for the norm that does not reference the norm itself. Produce a structured argument.',
                'forum_question_template' => 'You justified "{excerpt}" by saying it is right or correct. What is the independent reason for this norm, other than the norm itself?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 51,
                'confusion_type' => 'feature_indicator',
                'error_name'     => 'Norm Too Broad',
                'plain_name'     => 'Normative claim generalized beyond its valid scope',
                'trigger_logic'  => [
                    'keywords'       => ['everyone should', 'all people must', 'universally', 'always required'],
                    'vague_patterns' => ['(everyone|all people|all humans|society) (should|must|ought|needs to)', '(always|universally|in all cases) (required|necessary|obligatory|expected)'],
                ],
                'repair_template'         => 'Specify the application criteria for the norm. Produce a delimitation that defines who the norm applies to and under what conditions.',
                'forum_question_template' => 'You stated "{excerpt}" as a universal norm. For whom does this norm apply, and in what specific circumstances?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 52,
                'confusion_type' => 'feature_indicator',
                'error_name'     => 'Norm Too Narrow',
                'plain_name'     => 'Norm excludes cases it should legitimately cover',
                'trigger_logic'  => [
                    'keywords'       => ['only applies to', 'restricted to', 'only valid for', 'limited to'],
                    'vague_patterns' => ['(only applies?|only (valid|relevant)) (to|for|in)', '(restricted|limited) (to|for) (this|these|specific|particular)'],
                ],
                'repair_template'         => 'Add extension cases that should fall under the norm. Produce a broader formulation that includes them.',
                'forum_question_template' => 'Your norm about "{excerpt}" seems narrow — are there relevant cases it should cover that it currently excludes?',
                'severity_weight'         => 1,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 53,
                'confusion_type' => 'feature_indicator',
                'error_name'     => 'Context-Free Norm',
                'plain_name'     => 'Universal normative claim made without contextual conditions',
                'trigger_logic'  => [
                    'keywords'       => ['regardless of circumstance', 'in any situation', 'absolute', 'unconditionally'],
                    'vague_patterns' => ['(regardless of|irrespective of|independent of) (the )?(context|situation|circumstance)', '(absolutely|unconditionally|always and everywhere) (required|valid|binding|obligatory)'],
                ],
                'repair_template'         => 'Make the context explicit. Produce a conditional norm: "In context C, norm N applies because of condition X."',
                'forum_question_template' => 'You stated "{excerpt}" as a universal norm. In what context might this norm not apply or need to be modified?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 54,
                'confusion_type' => 'feature_indicator',
                'error_name'     => 'Level Confusion (Normative Block)',
                'plain_name'     => 'Individual and collective norms applied interchangeably',
                'trigger_logic'  => [
                    'keywords'       => ['individuals and society', 'personal and collective', 'private and public'],
                    'vague_patterns' => ['(both|for) (individuals?|persons?|people) (and|as well as) (society|groups?|organizations?)', '(individual|personal|private) (and|as well as) (collective|social|public) (norm|duty|obligation)'],
                ],
                'repair_template'         => 'Split the norm into individual and collective components. Produce separate norm statements for each level.',
                'forum_question_template' => 'Your norm around "{excerpt}" — does it apply to individuals, to organizations, or to both? The obligations may be very different.',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 55,
                'confusion_type' => 'feature_indicator',
                'error_name'     => 'Timeless Norm',
                'plain_name'     => 'Norm presented as permanent despite historical variation',
                'trigger_logic'  => [
                    'keywords'       => ['has always been', 'fundamental', 'has always required', 'since time immemorial'],
                    'vague_patterns' => ['(has always|will always) (been|required|meant|implied) (that|this|the)', '(fundamental|basic|core|essential) (norm|principle|value|obligation) (that|of)'],
                ],
                'repair_template'         => 'Run a historical test on the norm. Show how it has varied across time periods and cultures.',
                'forum_question_template' => 'You presented "{excerpt}" as a timeless norm. How would this norm have been understood in a different era or culture?',
                'severity_weight'         => 1,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 56,
                'confusion_type' => 'feature_indicator',
                'error_name'     => 'Domain Migration (Normative Block)',
                'plain_name'     => 'Norm transplanted between practices without validation',
                'trigger_logic'  => [
                    'keywords'       => ['as in medicine', 'as in law', 'borrowed from ethics', 'applies the same standard'],
                    'vague_patterns' => ['(applies?|transfers?|carries?) (the same|this) (norm|standard|principle) (to|from|in)', '(as|like) in (medicine|law|education|business|science)'],
                ],
                'repair_template'         => 'Test the norm per practice domain. Produce domain-specific norm formulations where the original does not transfer cleanly.',
                'forum_question_template' => 'You applied the norm about "{excerpt}" from one domain to another. Does the norm carry the same weight and meaning in the new domain?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 57,
                'confusion_type' => 'feature_indicator',
                'error_name'     => 'Measurement Contains Norm',
                'plain_name'     => 'Scoring system embeds a value judgment as a technical step',
                'trigger_logic'  => [
                    'keywords'       => ['above threshold', 'passing score', 'acceptable level', 'benchmark'],
                    'vague_patterns' => ['(above|below|meets?) (the )?(threshold|benchmark|standard|acceptable|passing)', '(scores?|rates?|ranks?) (as|above|below) (acceptable|satisfactory|good|passing)'],
                ],
                'repair_template'         => 'Separate the measuring step from the judging step. Produce two explicit steps: what the score is, and what value is assigned to that score.',
                'forum_question_template' => 'Your measurement of "{excerpt}" — at what point does measuring become judging? Where is the normative threshold, and who decided it?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 58,
                'confusion_type' => 'feature_indicator',
                'error_name'     => 'Reified Norm',
                'plain_name'     => 'A chosen value standard presented as an objective fact',
                'trigger_logic'  => [
                    'keywords'        => ['the standard requires', 'the norm dictates', 'the benchmark demands'],
                    'acting_abstracts' => ['the standard', 'the norm', 'the benchmark', 'best practice'],
                    'vague_patterns'  => ['the (standard|norm|benchmark|rule|criterion) (requires?|dictates?|demands?|states?)', '(best practice|the accepted standard|the established norm) (is|says?|requires?)'],
                ],
                'repair_template'         => 'Label the norm as an explicit choice. Produce a statement of the form: "We have chosen to apply standard S because of reason R."',
                'forum_question_template' => 'You wrote "{excerpt}" as if the standard exists independently. Who chose this standard, and what is the reason for this choice?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 59,
                'confusion_type' => 'feature_indicator',
                'error_name'     => 'Hidden Normativity',
                'plain_name'     => 'Value judgments embedded in apparently neutral language',
                'trigger_logic'  => [
                    'keywords'       => ['effective', 'efficient', 'successful', 'healthy', 'rational', 'optimal'],
                    'vague_patterns' => ['(effective|efficient|successful|healthy|rational|optimal|appropriate) \\w+', 'a (well-functioning|properly structured|correctly organized|healthy)'],
                ],
                'repair_template'         => 'Detect all implicit values in the language. Produce an explicit list of the value judgments embedded in the description.',
                'forum_question_template' => 'The word "{excerpt}" sounds neutral but carries a value judgment. Effective for whom? Rational by whose standard?',
                'severity_weight'         => 3,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

            [
                'rule_number'    => 60,
                'confusion_type' => 'feature_indicator',
                'error_name'     => 'Prototype-Based Norm',
                'plain_name'     => 'Norm derived from an ideal case, fails for atypical situations',
                'trigger_logic'  => [
                    'keywords'       => ['in the ideal case', 'when things work properly', 'as it should be'],
                    'vague_patterns' => ['in (the ideal|a perfect|a well-functioning|a properly working) (case|situation|world)', '(as it|as things) (should|ought to|are supposed to) (be|work|function)'],
                ],
                'repair_template'         => 'Test the norm against atypical and edge cases. Produce a robust norm formulation that holds outside the ideal scenario.',
                'forum_question_template' => 'Your norm about "{excerpt}" is based on an ideal case. How does the norm apply when the situation is messy, partial, or non-ideal?',
                'severity_weight'         => 2,
                'is_active'               => true,
                'supersedes_id'           => null,
                'published_at'            => $publishedAt,
            ],

        ];

        foreach ($rules as $rule) {
            Heuristic::create($rule);
        }

        $this->command->info('Seeded ' . count($rules) . ' heuristics across 6 confusion types.');

        $this->command->table(
            ['Confusion Type', 'Count'],
            collect($rules)
                ->groupBy('confusion_type')
                ->map(fn($group, $type) => [$type, count($group)])
                ->values()
                ->toArray()
        );
    }
}
