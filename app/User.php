<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function contests()
    {
        return $this->hasMany(Contest::class);
    }

    public function getPaidContestsAttribute()
    {
        return $this->hasMany(Contest::class)->whereHas('payment');
    }

    public function getCountryAttribute()
    {
        return Country::where('id', $this->country_id)->first();
    }

    public function freelancer_profile()
    {
        return $this->hasOne(Freelancer::class);
    }

    public function payment_method()
    {
        return $this->hasOne(PaymentMethod::class);
    }

    public function project_manager_offers()
    {
        return $this->hasMany(ProjectManagerOffer::class)->with('sub_category.offer_category');
    }

    public function freelancer_offers()
    {
        return $this->hasMany(FreelancerOffer::class)->with('sub_category.offer_category');
    }

    public function contest_submissions()
    {
        return $this->hasMany(ContestSubmission::class);
    }

    public function getFreelancerRankAttribute()
    {
        $rank = 0;

        if ($this->freelancer) {
            // Calculate Rank
            $rank = 1;
        }

        return $rank;
    }
}
