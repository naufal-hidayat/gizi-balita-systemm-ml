<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'posyandu_name',
        'phone',
        'address',
        'is_active'
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean'
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is kader
     */
    public function isKader()
    {
        return $this->role === 'kader';
    }

    /**
     * Check if user is bidan
     */
    public function isBidan()
    {
        return $this->role === 'bidan';
    }

    /**
     * Get role label
     */
    public function getRoleLabelAttribute()
    {
        $labels = [
            'admin' => 'Administrator',
            'kader' => 'Kader Posyandu',
            'bidan' => 'Bidan',
            'petugas' => 'Petugas Kesehatan'
        ];
        
        return $labels[$this->role] ?? 'Unknown';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Relationship with Balita (registered by this user)
     */
    public function balita()
    {
        return $this->hasMany(Balita::class);
    }

    /**
     * Relationship with Pengukuran (performed by this user)
     */
    public function pengukuran()
    {
        return $this->hasMany(Pengukuran::class);
    }

    /**
     * Get balita in user's posyandu
     */
    public function balitaInPosyandu()
    {
        if ($this->isAdmin()) {
            return Balita::query();
        }
        
        return Balita::where('posyandu', $this->posyandu_name);
    }

    /**
     * Get pengukuran in user's posyandu
     */
    public function pengukuranInPosyandu()
    {
        if ($this->isAdmin()) {
            return Pengukuran::query();
        }
        
        return Pengukuran::whereHas('balita', function($query) {
            $query->where('posyandu', $this->posyandu_name);
        });
    }

    /**
     * Count balita in user's jurisdiction
     */
    public function getBalitaCountAttribute()
    {
        return $this->balitaInPosyandu()->count();
    }

    /**
     * Count measurements performed by user
     */
    public function getMeasurementCountAttribute()
    {
        return $this->pengukuran()->count();
    }

    /**
     * Count high-risk balita in user's jurisdiction
     */
    public function getHighRiskBalitaCountAttribute()
    {
        return $this->balitaInPosyandu()
                    ->whereHas('latestPengukuran.prediksiGizi', function($query) {
                        $query->where('prediksi_status', 'stunting')
                              ->orWhere('prioritas', 'tinggi');
                    })->count();
    }

    /**
     * Get recent activity summary
     */
    public function getRecentActivitySummary($days = 30)
    {
        $startDate = now()->subDays($days);
        
        return [
            'measurements_performed' => $this->pengukuran()
                                            ->where('created_at', '>=', $startDate)
                                            ->count(),
            'balita_registered' => $this->balita()
                                       ->where('created_at', '>=', $startDate)
                                       ->count(),
            'high_risk_identified' => $this->pengukuranInPosyandu()
                                          ->whereHas('prediksiGizi', function($query) {
                                              $query->whereIn('prediksi_status', ['stunting', 'berisiko_stunting']);
                                          })
                                          ->where('created_at', '>=', $startDate)
                                          ->count()
        ];
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for users by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for users by posyandu
     */
    public function scopeByPosyandu($query, $posyandu)
    {
        return $query->where('posyandu_name', $posyandu);
    }

    /**
     * Check if user can access balita
     */
    public function canAccessBalita(Balita $balita)
    {
        if ($this->isAdmin()) {
            return true;
        }
        
        return $balita->posyandu === $this->posyandu_name;
    }

    /**
     * Check if user can access pengukuran
     */
    public function canAccessPengukuran(Pengukuran $pengukuran)
    {
        if ($this->isAdmin()) {
            return true;
        }
        
        return $pengukuran->balita->posyandu === $this->posyandu_name;
    }

    /**
     * Check if user can edit data
     */
    public function canEdit()
    {
        return $this->is_active && in_array($this->role, ['admin', 'kader', 'bidan']);
    }

    /**
     * Get user's permissions
     */
    public function getPermissions()
    {
        $basePermissions = [
            'view_dashboard' => true,
            'view_balita' => true,
            'view_pengukuran' => true,
            'view_reports' => true
        ];

        switch ($this->role) {
            case 'admin':
                return array_merge($basePermissions, [
                    'manage_users' => true,
                    'manage_settings' => true,
                    'view_all_data' => true,
                    'create_balita' => true,
                    'edit_balita' => true,
                    'delete_balita' => true,
                    'create_pengukuran' => true,
                    'edit_pengukuran' => true,
                    'delete_pengukuran' => true,
                    'export_data' => true
                ]);

            case 'bidan':
                return array_merge($basePermissions, [
                    'create_balita' => true,
                    'edit_balita' => true,
                    'create_pengukuran' => true,
                    'edit_pengukuran' => true,
                    'delete_pengukuran' => true,
                    'export_data' => true
                ]);

            case 'kader':
                return array_merge($basePermissions, [
                    'create_balita' => true,
                    'edit_balita' => true,
                    'create_pengukuran' => true,
                    'edit_pengukuran' => true
                ]);

            default:
                return $basePermissions;
        }
    }

    /**
     * Check if user has specific permission
     */
    public function hasPermission($permission)
    {
        $permissions = $this->getPermissions();
        return isset($permissions[$permission]) && $permissions[$permission] === true;
    }
}