<?php

namespace App\Models;

use App\Services\OpenID\HasClaims;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Passport\HasApiTokens;
use libphonenumber\PhoneNumberFormat;

/**
 * Class User
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $email
 * @property string
 * @property-read Contact $contact
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasClaims;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

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
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CLAIMS --------------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function getProfileClaim()
    {
        $result = [
            'preferred_username' => $this->name,
        ];
        if($this->contact) {
            $result['name'] = $this->contact->name;
            $result['family_name'] = $this->contact->name_last;
            $result['given_name'] = $this->contact->name_first;
            $result['middle_name'] = $this->contact->name_middle;
            $result['nickname'] = $this->contact->nickname;
            $result['birth_date'] = $this->contact->birth_date->toISOString();
            $result['updated_at'] = $this->contact->updated_at->toISOString();
        } else {
            $result['name'] = $this->name;
            $result['updated_at'] = $this->updated_at->toISOString();
        }
        return $result;
    }

    public function getEmailClaim()
    {
        return [
            'email' => $this->email,
            'email_verified' => $this->hasVerifiedEmail(),
        ];
    }

    public function getAddressClaim()
    {
        if($this->contact && $this->contact->postalAddress) {
            return [
                'postal_address' => $this->contact->postalAddress->format()
            ];
        } else {
            return [];
        }
    }

    public function getPhoneClaim()
    {
        if($this->contact && $this->contact->phoneNumber) {
            return [
                'phone_number' => $this->contact->phoneNumber->format(PhoneNumberFormat::E164),
            ];
        } else {
            return [];
        }
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CONTACTS ------------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
