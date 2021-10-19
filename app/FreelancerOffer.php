<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FreelancerOffer extends Model
{
    public function sub_category()
    {
        return $this->belongsTo(OfferSubCategory::class, 'sub_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dispute(){
        return $this->hasOne(FreelancerOfferDispute::class);
    }
}