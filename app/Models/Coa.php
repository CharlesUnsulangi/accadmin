<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * COA Model (Chart of Accounts) - LEVEL 4 Detail Accounts
 * 
 * STRUKTUR 4-LEVEL LEGACY + FLEXIBLE H1-H6:
 * 
 * LEGACY (4 tabel terpisah):
 * - Level 1: ms_acc_coa_main (Main Category - 10 records)
 * - Level 2: ms_acc_main_sub1 (Sub1 Category - 9 records)
 * - Level 3: ms_acc_main_sub2 (Sub2 Category - 9 records)
 * - Level 4: ms_acc_coa (THIS TABLE - Detail Accounts - 501+ records) ⭐
 * 
 * MODERN (1 tabel, flexible hierarchy):
 * - Format MODERN: H1-H6 (ms_coa_h1_id s/d ms_coa_h6_id) dalam tabel ini juga
 * - Flexible: bisa 1-6 level sesuai kebutuhan
 * 
 * HIERARKI FLEXIBLE (H1-H6):
 * - Minimum: H1 (1 level)
 * - Maksimum: H1 > H2 > H3 > H4 > H5 > H6 (6 levels)
 * - Umum dipakai: H1 > H2 > H3 > H4 (4 levels)
 * 
 * Setiap level punya 3 field:
 * - ms_coa_hX_id (varchar) - ID string
 * - id_hX (int) - ID integer  
 * - desc_hX (varchar) - Deskripsi
 * 
 * TRANSAKSI:
 * - Akun COA di level berapa saja bisa digunakan untuk transaksi
 * - Tergantung kebutuhan bisnis (bisa di H1, H2, H3, H4, H5, atau H6)
 * 
 * PRIMARY KEY: coa_code (varchar 50)
 * AUTO INCREMENT: id (int identity)
 * 
 * LEGACY LINK:
 * - coa_coasub2code → ms_acc_main_sub2.coa_main2_code (FK ke Level 3)
 * 
 * @property string $coa_code Primary key - Kode akun detail
 * @property int $id Auto-increment ID
 * @property string $coa_id ID alternatif
 * @property string $coa_desc Deskripsi akun
 * @property string $coa_note Catatan tambahan
 * @property string $arus_kas_code Kode untuk cash flow mapping
 * @property string $ms_acc_coa_h Header COA
 * @property string $coa_coasub2code Legacy FK ke Level 3 (ms_acc_main_sub2)
 * 
 * HIERARCHY AKTIF (H1-H6):
 * @property string $ms_coa_h1_id Level 1 ID (string)
 * @property string $ms_coa_h2_id Level 2 ID (string)
 * @property string $ms_coa_h3_id Level 3 ID (string)
 * @property string $ms_coa_h4_id Level 4 ID (string)
 * @property string $ms_coa_h5_id Level 5 ID (string)
 * @property string $ms_coa_h6_id Level 6 ID (string)
 * 
 * @property string $desc_h1 Deskripsi Level 1
 * @property string $desc_h2 Deskripsi Level 2
 * @property string $desc_h3 Deskripsi Level 3
 * @property string $desc_h4 Deskripsi Level 4
 * @property string $desc_h5 Deskripsi Level 5
 * @property string $desc_h6 Deskripsi Level 6
 * 
 * @property int $id_h1 Integer ID Level 1
 * @property int $id_h2 Integer ID Level 2
 * @property int $id_h3 Integer ID Level 3
 * @property int $id_h4 Integer ID Level 4
 * @property int $id_h5 Integer ID Level 5
 * @property int $id_h6 Integer ID Level 6
 * 
 * LEGACY FIELDS (Referensi saja, tidak aktif):
 * @property string $coa_coasub2code Legacy FK ke CoaSub2
 * @property string $id_old_sub_2 Legacy sub2 ID
 * @property string $id_old_sub1 Legacy sub1 ID
 * @property string $id_old_main Legacy main ID
 * @property string $sub2_desc Legacy sub2 description
 * @property string $sub1_desc Legacy sub1 description
 * @property string $main_desc Legacy main description
 * 
 * AUDIT TRAIL:
 * @property string $rec_usercreated User yang membuat
 * @property string $rec_userupdate User yang update
 * @property \Carbon\Carbon $rec_datecreated Tanggal dibuat
 * @property \Carbon\Carbon $rec_dateupdate Tanggal update
 * @property string $rec_status Status (A=Active, D=Deleted, I=Inactive)
 */
class Coa extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'ms_acc_coa';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'coa_code';

    /**
     * Indicates if the model's ID is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'coa_code',
        'coa_id',
        'coa_coasub2code',
        'coa_desc',
        'coa_note',
        'arus_kas_code',
        'ms_acc_coa_h',
        
        // Hierarchy H1-H6
        'ms_coa_h1_id',
        'ms_coa_h2_id',
        'ms_coa_h3_id',
        'ms_coa_h4_id',
        'ms_coa_h5_id',
        'ms_coa_h6_id',
        
        'desc_h1',
        'desc_h2',
        'desc_h3',
        'desc_h4',
        'desc_h5',
        'desc_h6',
        
        'id_h1',
        'id_h2',
        'id_h3',
        'id_h4',
        'id_h5',
        'id_h6',
        
        // Legacy fields
        'id_old_sub_2',
        'id_old_sub1',
        'id_old_main',
        'sub2_desc',
        'sub1_desc',
        'main_desc',
        
        // Audit trail
        'rec_usercreated',
        'rec_userupdate',
        'rec_datecreated',
        'rec_dateupdate',
        'rec_status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'rec_datecreated' => 'datetime',
        'rec_dateupdate' => 'datetime',
        'id' => 'integer',
        'id_h1' => 'integer',
        'id_h2' => 'integer',
        'id_h3' => 'integer',
        'id_h4' => 'integer',
        'id_h5' => 'integer',
        'id_h6' => 'integer',
    ];

    /**
     * Default values for attributes
     */
    protected $attributes = [
        'rec_status' => 'A',
    ];

    /**
     * Boot method untuk auto-fill audit trail
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = auth()->user();
            $model->rec_usercreated = $user ? $user->name : 'system';
            $model->rec_userupdate = $user ? $user->name : 'system';
            $model->rec_datecreated = now();
            $model->rec_dateupdate = now();
        });

        static::updating(function ($model) {
            $user = auth()->user();
            $model->rec_userupdate = $user ? $user->name : 'system';
            $model->rec_dateupdate = now();
        });
    }

    /**
     * Override delete untuk soft delete (rec_status = 'D')
     */
    public function delete()
    {
        $this->update([
            'rec_status' => 'D',
            'rec_userupdate' => auth()->user() ? auth()->user()->name : 'system',
            'rec_dateupdate' => now(),
        ]);

        return true;
    }

    /**
     * Validation rules
     */
    public static function validationRules($id = null)
    {
        return [
            'coa_code' => 'required|string|max:50|unique:ms_acc_coa,coa_code,' . $id . ',coa_code',
            'coa_id' => 'required|string|max:50',
            'coa_coasub2code' => 'nullable|string|max:50',
            'coa_desc' => 'nullable|string|max:50',
            'coa_note' => 'nullable|string|max:50',
            'arus_kas_code' => 'nullable|string|max:50',
            'rec_status' => 'required|in:A,D,I',
        ];
    }

    /**
     * ================================================================
     * RELATIONSHIPS
     * ================================================================
     */

    /**
     * LEGACY RELATIONSHIP (Referensi saja)
     * Relationship: COA belongs to COA Sub2 (Legacy Level 3)
     * Table: ms_acc_main_sub2
     * FK: coa_coasub2code → coa_main2_code
     * 
     * NOTE: Gunakan hierarchy H1-H6 untuk sistem baru
     */
    public function coaSub2()
    {
        return $this->belongsTo(CoaSub2::class, 'coa_coasub2code', 'coa_main2_code');
    }

    /**
     * ================================================================
     * SCOPES
     * ================================================================
     */

    /**
     * Scope: Only active records
     */
    public function scopeActive($query)
    {
        return $query->where('rec_status', 'A');
    }

    /**
     * Scope: Search by code, description, or note
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('coa_code', 'like', "%{$term}%")
              ->orWhere('coa_desc', 'like', "%{$term}%")
              ->orWhere('coa_note', 'like', "%{$term}%")
              ->orWhere('coa_id', 'like', "%{$term}%");
        });
    }

    /**
     * Scope: Filter by parent coa_coasub2code
     */
    public function scopeByParent($query, $parentCode)
    {
        return $query->where('coa_coasub2code', $parentCode);
    }

    /**
     * Scope: Eager load hierarchy (LEGACY - untuk backward compatibility)
     * NOTE: Sistem baru menggunakan H1-H6 yang sudah ada di tabel ms_acc_coa
     */
    public function scopeWithHierarchy($query)
    {
        // H1-H6 sudah ada di tabel ms_acc_coa, tidak perlu join
        // Legacy relationship tetap di-load untuk referensi
        return $query->with([
            'coaSub2',
            'coaSub2.coaSub1',
            'coaSub2.coaSub1.coaMain'
        ]);
    }

    /**
     * Scope: Filter by hierarchy level (H1-H6)
     * Contoh: Coa::byHierarchyLevel(1, 'ASSETS')->get()
     */
    public function scopeByHierarchyLevel($query, $level, $value)
    {
        $field = "ms_coa_h{$level}_id";
        return $query->where($field, $value);
    }

    /**
     * Scope: Has hierarchy up to level (punya hierarchy sampai level X)
     * Contoh: Coa::hasHierarchyLevel(4)->get() // yang punya sampai H4
     */
    public function scopeHasHierarchyLevel($query, $level)
    {
        $field = "ms_coa_h{$level}_id";
        return $query->whereNotNull($field);
    }

    /**
     * ================================================================
     * ACCESSORS & ATTRIBUTES
     * ================================================================
     */

    /**
     * Get account type based on first digit of coa_code
     * 1xxx = Asset, 2xxx = Liability, 3xxx = Equity
     * 4xxx = Revenue, 5xxx = Expense
     */
    public function getAccountTypeAttribute()
    {
        $firstChar = substr($this->coa_code, 0, 1);
        
        switch ($firstChar) {
            case '1':
                return 'Asset';
            case '2':
                return 'Liability';
            case '3':
                return 'Equity';
            case '4':
                return 'Revenue';
            case '5':
                return 'Expense';
            default:
                return 'Other';
        }
    }

    /**
     * Get full hierarchy path
     * Format: "Main > Sub1 > Sub2 > Detail"
     */
    public function getHierarchyPathAttribute()
    {
        $parts = [];
        
        // Gunakan kolom desc_h yang sudah ada di database
        if ($this->desc_h1) $parts[] = $this->desc_h1;
        if ($this->desc_h2) $parts[] = $this->desc_h2;
        if ($this->desc_h3) $parts[] = $this->desc_h3;
        if ($this->desc_h4) $parts[] = $this->desc_h4;
        if ($this->desc_h5) $parts[] = $this->desc_h5;
        if ($this->desc_h6) $parts[] = $this->desc_h6;
        if ($this->coa_desc) $parts[] = $this->coa_desc;
        
        return implode(' > ', array_filter($parts));
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->coa_code} - {$this->coa_desc}";
    }

    /**
     * Check if COA can be used in transactions
     * NOTE: Akun di level mana saja (H1-H6) bisa dipakai untuk transaksi
     * Tergantung kebutuhan bisnis
     */
    public function canBeUsed()
    {
        // Cukup cek status aktif saja
        // Tidak perlu cek hierarchy karena flexible (bisa di level berapa saja)
        return $this->rec_status === 'A';
    }

    /**
     * Get hierarchy level yang sedang digunakan (1-6)
     * Return: Angka level tertinggi yang terisi
     */
    public function getCurrentHierarchyLevel()
    {
        if ($this->ms_coa_h6_id) return 6;
        if ($this->ms_coa_h5_id) return 5;
        if ($this->ms_coa_h4_id) return 4;
        if ($this->ms_coa_h3_id) return 3;
        if ($this->ms_coa_h2_id) return 2;
        if ($this->ms_coa_h1_id) return 1;
        return 0; // Tidak punya hierarchy
    }

    /**
     * Check if this is a leaf node (tidak punya child)
     * Leaf node adalah akun yang bisa digunakan untuk transaksi detail
     */
    public function isLeafNode()
    {
        // Cek apakah ada COA lain yang menggunakan akun ini sebagai parent
        // di hierarchy level manapun
        $currentLevel = $this->getCurrentHierarchyLevel();
        
        if ($currentLevel == 0) return true; // Tidak punya hierarchy, bisa langsung dipakai
        if ($currentLevel == 6) return true; // Level maksimal, pasti leaf
        
        // Cek apakah ada child di level berikutnya
        $nextLevel = $currentLevel + 1;
        $nextField = "ms_coa_h{$nextLevel}_id";
        $currentField = "ms_coa_h{$currentLevel}_id";
        
        $hasChild = self::where($currentField, $this->getAttribute("ms_coa_h{$currentLevel}_id"))
                        ->whereNotNull($nextField)
                        ->exists();
        
        return !$hasChild;
    }

    /**
     * ================================================================
     * HELPER METHODS
     * ================================================================
     */

    /**
     * Get hierarchy level count (1-6)
     */
    public function getHierarchyLevelAttribute()
    {
        $level = 0;
        if ($this->ms_coa_h1_id) $level = 1;
        if ($this->ms_coa_h2_id) $level = 2;
        if ($this->ms_coa_h3_id) $level = 3;
        if ($this->ms_coa_h4_id) $level = 4;
        if ($this->ms_coa_h5_id) $level = 5;
        if ($this->ms_coa_h6_id) $level = 6;
        
        return $level;
    }

    /**
     * Get all hierarchy IDs as array
     */
    public function getHierarchyIdsAttribute()
    {
        return array_filter([
            'h1' => $this->ms_coa_h1_id,
            'h2' => $this->ms_coa_h2_id,
            'h3' => $this->ms_coa_h3_id,
            'h4' => $this->ms_coa_h4_id,
            'h5' => $this->ms_coa_h5_id,
            'h6' => $this->ms_coa_h6_id,
        ]);
    }

    /**
     * Get all hierarchy descriptions as array
     */
    public function getHierarchyDescsAttribute()
    {
        return array_filter([
            'h1' => $this->desc_h1,
            'h2' => $this->desc_h2,
            'h3' => $this->desc_h3,
            'h4' => $this->desc_h4,
            'h5' => $this->desc_h5,
            'h6' => $this->desc_h6,
        ]);
    }
}
