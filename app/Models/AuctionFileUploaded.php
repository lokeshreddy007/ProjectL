<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuctionFileUploaded extends Model
{
    use HasFactory;

    protected $fillable = [
        "file_name",
        "file_rows_count",
        "is_uploaded",
    ];
}
