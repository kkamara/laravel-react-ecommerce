<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserPaymentConfig extends Model
{
    use HasFactory;

    /**
     * This models immutable values.
     *
     * @property Array
     */
    protected $guarded = [];

    /**
     * This model relationship belongs to \App\Models\User.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * This model relationship belongs to \App\Models\Order\OrderHistory.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function orderHistory()
    {
        return $this->hasOne('App\Models\Order\OrderHistory');
    }

    /**
     * Set a publicily accessible identifier to get the hidden cart number for this unique instance.
     *
     * @return  String
     */
    public function getHiddenCardNumberAttribute()
    {
        $cardNumber = $this->attributes['card_number'];

        $cardNumber = substr($cardNumber, -4, strlen($cardNumber));
        $cardNumber = str_pad($cardNumber, 16, "*", STR_PAD_LEFT);

        return $cardNumber;
    }

    /**
     * Set a publicily accessible identifier to get the expiry date for this unique instance.
     *
     * @return  \Carbon\Carbon
     */
    public function getExpiryDateAttribute()
    {
        return Carbon::createFromDate(
                $this->attributes['expiry_year'], 
                $this->attributes['expiry_month'], 
                1
            )
            ->format('m/Y');
    }

    /**
     * Set a publicily accessible identifier to get the edit expiry date for this unique instance.
     *
     * @return  \Carbon\Carbon
     */
    public function getEditExpiryDateAttribute()
    {
        return Carbon::createFromDate(
                $this->attributes['expiry_year'], 
                $this->attributes['expiry_month'], 
                1
            )
            ->format('Y-m');
    }

    /**
     * Set a publicily accessible identifier to get the edit 
     * expiry date for this unique instance.
     *
     * @return  \Carbon\Carbon
     */
    public function getFormattedPhoneNumberAttribute()
    {
        return sprintf(
            "%s %s",
            $this->attributes['phone_number_extension'],
            $this->attributes['phone_number']
        );
    }

    /**
     * Set a publicily accessible identifier to get the 
     * formatted mobile number for this unique instance.
     *
     * @return  String
     */
    public function getFormattedMobileNumberAttribute()
    {
        return sprintf(
            "%s %s",
            $this->attributes['mobile_number_extension'],
            $this->attributes['mobile_number']
        );
    }
}
