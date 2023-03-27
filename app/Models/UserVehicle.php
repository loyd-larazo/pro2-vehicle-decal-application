<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'user_id',
        'make',
        'plate_number',
        'model',
        'year_model',
        'color',
        'engine_number',
        'chassis_number',
        'type',
        'or',
        'cr',
        'verified_by',
        'verified_date',
        'verified_status',
        'code',
        'qr_code',
        'issued_by',
        'issued_date',
        'issued_status',
        'expiration_date',
        'status',
        'own_vehicle',
        'deed_of_sale',
    ];

    public function photos() {
        return $this->hasMany(VehicleImage::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
