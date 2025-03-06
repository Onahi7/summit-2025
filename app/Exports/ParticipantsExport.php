<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ParticipantsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::where('role', 'participant')
            ->with(['payment', 'validations'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'State',
            'School',
            'Payment Status',
            'QR Verified',
            'Registration Date'
        ];
    }

    public function map($participant): array
    {
        return [
            $participant->id,
            $participant->name,
            $participant->email,
            $participant->phone,
            $participant->state,
            $participant->school_name,
            $participant->payment ? $participant->payment->status : 'pending',
            $participant->validations->isNotEmpty() ? 'Yes' : 'No',
            $participant->created_at->format('Y-m-d H:i:s')
        ];
    }
}
