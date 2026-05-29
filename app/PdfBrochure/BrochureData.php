<?php

namespace App\PdfBrochure;

/**
 * Immutable, fully-sanitized data structure handed to the brochure view.
 *
 * The point of this DTO is that the Blade template never touches raw model
 * attributes. Every field has already been:
 *   - normalised through Labels (enums → Polish)
 *   - scrubbed through TextSanitizer (admin profanity / test text dropped)
 *   - embedded as base64 data: URI (images, all in EmbeddedImage form)
 *
 * If a field is null / empty in this DTO, the corresponding section in the
 * view hides itself. There is exactly one place to decide "is there enough
 * data here to render the section?" — the builder.
 *
 * Section layout (mirrors a COS-Check / professional inspection report):
 *   1. Header strip on every page: brand + contact (phone, email, site)
 *   2. Page 1 vehicle summary: hero, title, core facts, price
 *   3. Page 1 main "Dane pojazdu" dense table
 *   4. Historia pojazdu
 *   5. Dokumenty
 *   6. Formalności
 *   7. Serwis i dokumentacja
 *   8. Zużycie paliwa i emisje
 *   9. Pomiary lakieru
 *  10. Stan techniczny
 *  11. Koła i opony
 *  12. Stan wizualny / uszkodzenia
 *  13. Wyposażenie
 *  14. Dokumentacja fotograficzna
 *  15. Zdjęcia uszkodzeń
 *  16. Materiały online
 */
final class BrochureData
{
    /**
     * @param array<int,array{label:string,value:string}>                                                  $vehicleData
     * @param array<int,array{label:string,value:string}>                                                  $historyItems
     * @param array<int,array{label:string,value:string}>                                                  $documentItems
     * @param array<int,array{label:string,value:string}>                                                  $formalItems
     * @param array<int,array{label:string,value:string}>                                                  $serviceItems
     * @param array<int,array{label:string,value:string}>                                                  $fuelItems
     * @param array<int,array{key:string,label:string,status:string,class:string,note:?string}>            $technicalConditions
     * @param array<int,array{label:string,value:int,class:string,verdict:string}>                         $paintMeasurements
     * @param array<int,array{title:string,tires:array<int,array{position:string,treadMm:?string,label:string,class:string}>}> $tireSets
     * @param array<int,array{area:string,type:string,severity:?string,tags:array<int,string>,description:?string,photos:array<int,EmbeddedImage>}> $damages
     * @param array<int,array{title:string,items:array<int,string>}>                                       $equipment
     * @param array<int,EmbeddedImage>                                                                     $galleryImages
     * @param array<int,EmbeddedImage>                                                                     $damageImages
     */
    public function __construct(
        public readonly string $reportId,
        public readonly string $generatedAt,
        public readonly string $identifier,
        public readonly string $title,
        public readonly ?string $brand,
        public readonly ?string $model,
        public readonly ?string $formattedPrice,
        public readonly ?int $price,

        // Contact strip (rendered on every page header)
        public readonly string $contactPhone,
        public readonly string $contactEmail,
        public readonly string $contactWebsite,

        // Page 1 summary key facts
        public readonly ?int $mileage,
        public readonly ?string $firstRegistration,
        public readonly ?string $receptionDate,
        public readonly ?string $fuelType,
        public readonly ?string $transmission,
        public readonly ?int $powerHp,
        public readonly ?int $powerKw,
        public readonly ?int $engineCapacity,
        public readonly ?int $doors,
        public readonly ?int $seats,

        // Page 1 main Dane pojazdu table — pre-collected key/value rows
        // so the view stays mechanical (no missing-field branching).
        public readonly array $vehicleData,

        // Per-section key/value rows. Empty arrays hide the section.
        public readonly array $historyItems,
        public readonly array $documentItems,
        public readonly array $formalItems,
        public readonly array $serviceItems,
        public readonly array $fuelItems,

        // Free-text history paragraph (rendered after the historyItems table)
        public readonly ?string $vehicleHistoryNote,

        // Technical/visual sections
        public readonly array $technicalConditions,
        public readonly array $paintMeasurements,
        public readonly array $tireSets,
        public readonly array $damages,
        public readonly array $equipment,

        // Photo grids
        public readonly ?EmbeddedImage $heroImage,
        public readonly array $galleryImages,
        public readonly array $damageImages,

        // Online materials
        public readonly ?string $engineVideoUrl,
        public readonly ?string $exteriorPanoUrl,
        public readonly ?string $interiorPanoUrl,
    ) {}
}
