<?php

namespace App;

use App\Traits\SoftDeletesWithTrashed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;
    use Notifiable;
    use CausesActivity;
    use LogsActivity;
    use SoftDeletesWithTrashed;
    use HasFactory;
    // use Authenticatable;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'nickname', 'email', 'password',
    ];

    protected $appends = [
        'avatar_url',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static $logOnlyDirty = true;
    protected static $logFillable = true;
    protected static $logAttributes = ['id'];
    protected static $ignoreChangedAttributes = ['password'];

    public function getLanguage() {
        $langObj = Preference::getUserPreference($this->id, 'prefs.gui-language');
        if(isset($langObj)) return $langObj->value;
        return 'en';
    }

    public function setPermissions() {
        $permissions = [];
        foreach($this->roles as $role) {
            $rolePermissions = $role->permissions;
            foreach($rolePermissions as $p) {
                if(!isset($permissions[$p->name])) {
                    $permissions[$p->name] = 1;
                }

            }
        }
        $this->permissions = $permissions;
    }

    public function setMetadata($data) {
        if(!isset($this->metadata)) {
            $this->metadata = $data;
        } else {
            $this->metadata = array_replace($this->metadata, $data);
        }
        $this->save();
    }

    public function getAvatarUrlAttribute() {

        return isset($this->avatar) ? sp_get_public_url($this->avatar) : null;
    }

    public function preferences() {
        return $this->hasMany('App\UserPreference');
    }

    // public function roles() {
    //     return $this->belongsToMany('App\Role', 'role_user', 'user_id', 'role_id');
    // }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }
}
