<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSp extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ms_admin_sp';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ms_admin_sp_id';

    /**
     * The primary key type.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ms_admin_sp_id',
        'sp_desc',
        'date_start_input',
        'date_end_input',
        'money_input',
        'varchar_input',
        'sp_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_start_input' => 'date',
        'date_end_input' => 'date',
        'money_input' => 'decimal:2',
    ];

    /**
     * Get formatted money input.
     *
     * @return string
     */
    public function getFormattedMoneyAttribute()
    {
        if ($this->money_input === null) {
            return 'Rp 0';
        }
        return 'Rp ' . number_format($this->money_input, 2, ',', '.');
    }

    /**
     * Get date range display.
     *
     * @return string
     */
    public function getDateRangeAttribute()
    {
        if ($this->date_start_input && $this->date_end_input) {
            return $this->date_start_input->format('d/m/Y') . ' - ' . $this->date_end_input->format('d/m/Y');
        }
        if ($this->date_start_input) {
            return 'From: ' . $this->date_start_input->format('d/m/Y');
        }
        if ($this->date_end_input) {
            return 'Until: ' . $this->date_end_input->format('d/m/Y');
        }
        return '-';
    }

    /**
     * Scope to search by description or SP name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            return $query->where(function ($q) use ($search) {
                $q->where('sp_desc', 'like', '%' . $search . '%')
                  ->orWhere('sp_name', 'like', '%' . $search . '%')
                  ->orWhere('ms_admin_sp_id', 'like', '%' . $search . '%')
                  ->orWhere('varchar_input', 'like', '%' . $search . '%');
            });
        }
        return $query;
    }
}
