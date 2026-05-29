<?php

namespace Tests\Unit\PdfBrochure;

use App\PdfBrochure\Labels;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LabelsTest extends TestCase
{
    #[Test]
    public function tire_position_maps_all_canonical_enum_keys(): void
    {
        $this->assertSame('Przednia lewa',  Labels::tirePosition('front_left'));
        $this->assertSame('Przednia prawa', Labels::tirePosition('front_right'));
        $this->assertSame('Tylna lewa',     Labels::tirePosition('rear_left'));
        $this->assertSame('Tylna prawa',    Labels::tirePosition('rear_right'));
        $this->assertSame('Zapasowe',       Labels::tirePosition('spare'));
        $this->assertSame('Górna',          Labels::tirePosition('top'));
    }

    #[Test]
    public function tire_position_returns_dash_for_empty(): void
    {
        $this->assertSame('—', Labels::tirePosition(null));
        $this->assertSame('—', Labels::tirePosition(''));
    }

    #[Test]
    public function tire_position_cleans_up_unknown_enum_so_underscores_dont_leak(): void
    {
        // Unknown keys get title-case treatment — never the raw underscore form.
        $this->assertStringNotContainsString('_', Labels::tirePosition('front_extra'));
    }

    #[Test]
    public function tire_condition_classifies_all_canonical_buckets(): void
    {
        $bad = Labels::tireCondition(['do wymiany']);
        $this->assertSame('Do wymiany', $bad['label']);
        $this->assertSame('cond-bad',   $bad['class']);

        $worn = Labels::tireCondition(['zużyta']);
        $this->assertSame('Wymaga uwagi', $worn['label']);
        $this->assertSame('cond-warn',    $worn['class']);

        $excellent = Labels::tireCondition(['bardzo dobry']);
        $this->assertSame('Stan bardzo dobry', $excellent['label']);

        $good = Labels::tireCondition(['ok']);
        $this->assertSame('Dobry', $good['label']);

        $empty = Labels::tireCondition([]);
        $this->assertSame('Stan bardzo dobry', $empty['label']);
    }

    #[Test]
    public function tire_condition_falls_back_to_warning_for_unknown_text(): void
    {
        // The slang word itself MUST NOT be passed through.
        $result = Labels::tireCondition(['zajebiste']);
        $this->assertSame('Wymaga uwagi', $result['label']);
        $this->assertNotSame('zajebiste',  $result['label']);
    }

    #[Test]
    public function damage_location_maps_known_keys(): void
    {
        $this->assertSame('Przód lewy',           Labels::damageLocation('front_left'));
        $this->assertSame('Tył prawy',            Labels::damageLocation('rear_right'));
        $this->assertSame('Maska',                Labels::damageLocation('hood'));
        $this->assertSame('Klapa bagażnika',      Labels::damageLocation('trunk'));
        $this->assertSame('Drzwi przednie lewe',  Labels::damageLocation('door_front_left'));
        $this->assertSame('—',                    Labels::damageLocation(null));
    }

    #[Test]
    public function tech_condition_classifies_with_note_carried_through(): void
    {
        $r = Labels::techCondition(['status' => 'bad', 'note' => 'wymiana klocków przód']);
        $this->assertSame('Wymaga naprawy', $r['label']);
        $this->assertSame('cond-bad',       $r['class']);
        $this->assertSame('wymiana klocków przód', $r['note']);

        $r = Labels::techCondition(['status' => 'attention', 'note' => '   ']);
        $this->assertSame('Wymaga uwagi', $r['label']);
        $this->assertNull($r['note'], 'whitespace-only note should be dropped');

        $r = Labels::techCondition('ok');
        $this->assertSame('Bez zarzutu', $r['label']);
    }

    #[Test]
    public function fuel_type_maps_english_to_polish(): void
    {
        $this->assertSame('Benzyna',     Labels::fuelType('petrol'));
        $this->assertSame('Diesel',      Labels::fuelType('Diesel'));
        $this->assertSame('Elektryczny', Labels::fuelType('electric'));
        $this->assertSame('Hybryda',     Labels::fuelType('hybrid'));
        $this->assertNull(Labels::fuelType(null));
        $this->assertNull(Labels::fuelType(''));
    }
}
