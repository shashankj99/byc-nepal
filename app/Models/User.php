<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile_number',
        'password',
        'status',
        'off_board_at',
        'is_admin_created',
        'myob_uid'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $appends = ["full_name", "created_at_formatted"];

    public function getFullNameAttribute(): string
    {
        return "{$this->attributes['first_name']} {$this->attributes['last_name']}";
    }

    public function getCreatedAtFormattedAttribute(): string
    {
        return Carbon::parse($this->attributes["created_at"])
            ->format("d/m/Y");
    }

    public function getOffBoardAtAttribute(): ?string
    {
        if ($this->attributes["off_board_at"])
            return Carbon::parse($this->attributes["off_board_at"])
                ->format("d/m/Y");
        return null;
    }

    public function verificationTokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VerificationToken::class);
    }

    public function userAddresses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function customerSubscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CustomerSubscription::class);
    }

    public function preOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PreOrder::class);
    }

    public function userCharities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserCharity::class);
    }

    /**
     * Get latest user subscription
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasOne|object|null
     */
    public function latestUserSubscription()
    {
        return $this->hasOne(CustomerSubscription::class)
            ->where("has_pre_order", "=", "1")
            ->latest("id")
            ->first();
    }

    /**
     * Get the latest user charity
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasOne|object|null
     */
    public function latestUserCharity()
    {
        return $this->hasOne(UserCharity::class)
            ->latest("id")
            ->first();
    }

    public function customerAccounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CustomerAccount::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function customerBins(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CustomerBin::class);
    }

    public function pickups(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pickup::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bins(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Bin::class, CustomerBin::class, 'user_id', 'bin_id'
        );
    }

    public function driver(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Driver::class);
    }

    public function adminNotifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AdminNotification::class);
    }

    public function countUnseenNotifications(): int
    {
        return $this->adminNotifications()
            ->getQuery()
            ->where("is_seen", "=", "0")
            ->count("id");
    }

    public function myobTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MyobTransaction::class);
    }

    public function driverPickups(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DriverPickup::class);
    }
}
