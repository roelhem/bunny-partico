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
 *
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $email
 * @property boolean $is_admin
 * @property string
 * @property-read Contact $contact
 * @package App\Models
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property int|null $contact_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \App\Models\Team|null $currentTeam
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $ownedTeams
 * @property-read int|null $owned_teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
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
