<?php

namespace App\Filament\Resources\Inquiries\Pages;

use App\Filament\Resources\Inquiries\InquiryResource;
use App\Models\Inquiry;
use Filament\Resources\Pages\EditRecord;

class EditInquiry extends EditRecord
{
    protected static string $resource = InquiryResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        abort_unless(
            $this->record->status === Inquiry::STATUS_DRAFT,
            403,
            'Only draft inquiries can be edited.'
        );
    }
}
