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
 * If a field is null in this DTO, the corresponding section in the view
 * hides itself. There is exactly one place to decide "is there enough
 * data here to render the section?" — the builder.
 */
final class BrochureData
{
    public function __construct(
        public readonly string $reportId,
        public readonly string $generatedAt,
        public readonly string $identifier,
        public readonly string $title,
        public readonly ?string $brand,
        public readonly ?string $model,
        public readonly ?string $formattedPrice,
        public readonly ?int $price,

        // Cover key facts
        public readonly ?int $mileage,
        public readonly ?string $firstRegistration,
        public readonly ?string $fuelType,
        public readonly ?string $transmission,
        public readonly ?int $powerHp,
        public readonly ?int $powerKw,
        public readonly ?int $engineCapacity,
        public readonly ?int $doors,
        public readonly ?int $seats,

        // Page 2 — Dane pojazdu
        public readonly ?string $vin,
        public readonly ?string $bodyType,
        public readonly ?string $color,
        public readonly ?string $colorCode,
        public readonly ?string $upholstery,
        public readonly ?string $driveType,
        public readonly ?int $weight,
        public readonly ?int $numberOfKeys,

        // Page 3 — Historia / Formalności (free-text, scrubbed)
        public readonly ?int $previousOwners,
        public readonly ?string $importedFrom,
        public readonly ?string $countryRegistration,
        public readonly ?string $vehicleHistory,

        // Stan techniczny
        /** @var array<int,array{key:string,label:string,status:string,class:string,note:?string}> */
        public readonly array $technicalConditions,
        /** @var array<int,array{label:string,value:int,class:string,verdict:string}> */
        public readonly array $paintMeasurements,

        // Koła i opony
        /** @var array<int,array{title:string,tires:array<int,array{position:string,treadMm:?string,label:string,class:string}>}> */
        public readonly array $tireSets,

        // Damages
        /** @var array<int,array{area:string,type:string,severity:?string,tags:array<int,string>,description:?string,photos:array<int,EmbeddedImage>}> */
        public readonly array $damages,

        // Equipment
        /** @var array<int,array{title:string,items:array<int,string>}> */
        public readonly array $equipment,

        // Photo grid
        public readonly ?EmbeddedImage $heroImage,
        /** @var array<int,EmbeddedImage> */
        public readonly array $galleryImages,
        /** @var array<int,EmbeddedImage> */
        public readonly array $damageImages,

        // Online materials
        public readonly ?string $engineVideoUrl,
        public readonly ?string $exteriorPanoUrl,
        public readonly ?string $interiorPanoUrl,
    ) {}
}
