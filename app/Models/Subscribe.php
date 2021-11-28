<?php

namespace App\Models;

use App\Services\GoogleSheet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Subscribe extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public static function booted()
    {

        static::created(function($subscribe) {

            $created_at = Carbon::parse($subscribe->created_at)->timezone('Asia/Riyadh')->format('Y-m-d H:i:s');
            $created_at_formatted = Carbon::parse($subscribe->created_at)->timezone('Asia/Riyadh')->format('Y-m-d');

            $googleSheet = new GoogleSheet();
            $values = [
                [
                    $created_at  ?? '-', $subscribe->reference_number  ?? '-', $created_at_formatted ?? '-',
                    'أقرّ باطلاعي نظام التعليم عن بعد الخاص بالمركز.', 'نعم',
                    $subscribe->student->section == 1 ? 'بنين' : 'بنات', $subscribe->student->serial_number ?? '-',
                    $subscribe->student->name ?? '-', $subscribe->country->name, $subscribe->email,
                    url(Storage::url($subscribe->money_transfer_image_path)) ?? '-', $subscribe->bank_name ?? '-', $subscribe->account_owner ?? '-',
                    $subscribe->transfer_date ?? '-', $subscribe->bank_reference_number ?? '-', $subscribe->payment_method ?? '-',
                    $subscribe->payment_id ?? '-',

                ],
            ];

            $googleSheet->saveDataToSheet($values);
            dd($values);

        });


    }
}
