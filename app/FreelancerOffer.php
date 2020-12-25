<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FreelancerOffer extends Model
{
    public function sub_category()
    {
        return $this->belongsTo(OfferSubCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
