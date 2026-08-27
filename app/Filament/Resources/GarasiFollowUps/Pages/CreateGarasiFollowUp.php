<?php

namespace App\Filament\Resources\GarasiFollowUps\Pages;

use App\Filament\Resources\GarasiFollowUps\GarasiFollowUpResource;
use App\Models\GarasiParticipant;
use Filament\Resources\Pages\CreateRecord;

class CreateGarasiFollowUp extends CreateRecord
{
    protected static string $resource = GarasiFollowUpResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['evaluator_id'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();
        if ($record && $record->garasi_participant_id) {
            $participant = GarasiParticipant::find($record->garasi_participant_id);
            if ($participant) {
                $participant->update([
                    'status' => 'completed_follow_up',
                ]);
            }
        }
    }
}
