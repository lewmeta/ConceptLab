<?php

namespace App\Enums;

enum ConfusionType: string
{
    case WordConcept = 'word_concept';
    case ConceptReferent = 'concept_referent';
    case ConceptTheory = 'concept_theory';
    case ConceptOperationalization = 'concept_operationalization';
    case PrototypeEssence = 'prototype_essence';
    case FeatureIndicator = 'feature_indicator';

    /**
     * Get the label for the confusion type.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            ConfusionType::WordConcept => 'Word ↔ Concept',
            ConfusionType::ConceptReferent => 'Concept ↔ Referent',
            ConfusionType::ConceptTheory => 'Concept ↔ Theory',
            ConfusionType::ConceptOperationalization => 'Concept ↔ Operationalization',
            ConfusionType::PrototypeEssence => 'Prototype ↔ Essence',
            ConfusionType::FeatureIndicator => 'Feature ↔ Indicator',
        };
    }

    /**
     * Tailwind/CSS colour token for span highlighting in the audit view 
     * Maps to the locked colour palette in the UI spec.
     * 
     * @return string
     */
    public function colour(): string
    {
        return match ($this) {
            ConfusionType::WordConcept              => '#534AB7', // Purple
            ConfusionType::ConceptReferent          => '#0F6E56', // Teal
            ConfusionType::ConceptTheory            => '#BA7517', // Amber
            ConfusionType::ConceptOperationalization => '#185FA5', // Blue
            ConfusionType::PrototypeEssence         => '#993C1D', // Coral
            ConfusionType::FeatureIndicator         => '#993556', // Pink

        };
    }

    /**
     * Highlight group for the audit text span layer.
     * word_concept + concept_referent → red (most dangerous)
     * concept_operationalization      → amber
     * concept_theory + prototype_essence → purple
     * feature_indicator               → pink
     * 
     * @return string
     */
    public function highlightGroup(): string
    {
        return match ($this) {
            ConfusionType::WordConcept,
            ConfusionType::ConceptReferent          => 'red',
            ConfusionType::ConceptOperationalization => 'amber',
            ConfusionType::ConceptTheory,
            ConfusionType::PrototypeEssence         => 'purple',
            ConfusionType::FeatureIndicator         => 'pink',
        };
    }
}
