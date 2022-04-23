<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiddingInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        "date",
        "ip_address",
        "state",
        "run",
        "lot",
        "item",
        "bidder",
        "user",
        "amount",
        "page_url",
        "useragent",
        "session_number"
    ];
}
