<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table  = 'testimonials';
    //Primary Key
    public $primaryKey = 'id';
    // Timestamsp
    public $timestamps = true;

    public function users()
    {
        return $this->belongsToMany('App\User');
    }
    public function getTestimonials()
    {
        $testimonials = $this::orderBy('created_at', 'desc')->where('disabled', false)->get();
        return $testimonials;
    }
    public function getDisabledTestimonials()
    {
        $testimonials = $this::orderBy('created_at', 'desc')->where('disabled', true)->get();
        return $testimonials;
    }

}