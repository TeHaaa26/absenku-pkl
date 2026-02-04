<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa extends Model
{
    protected $fillable = [
        'user_id', 'nisn', 'nama', 'kelas', 'no_telepon', 'alamat', 'jenis_kelamin'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

