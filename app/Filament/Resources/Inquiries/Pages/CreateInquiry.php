<?php

namespace App\Filament\Resources\Inquiries\Pages;

use App\Filament\Resources\Inquiries\InquiryResource;
use App\Models\Inquiry;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateInquiry extends CreateRecord
{
    protected static string $resource = InquiryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['inquiry_number'] ?? null)) {
            $data['inquiry_number'] = 'INQ-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(4));
        }

        $data['status'] = $data['status'] ?? Inquiry::STATUS_DRAFT;

        return $data;
    }
}
