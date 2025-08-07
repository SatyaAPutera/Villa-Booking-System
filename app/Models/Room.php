<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory, Uuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'room_no',
        'name',
        'description',
        'rate',
        'occupancy',  
        'image'
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'occupancy' => 'integer',
        'image' => 'array' // This will automatically handle JSON encoding/decoding
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
    
    // Helper method to get the first image
    public function getFirstImageAttribute()
    {
        $images = $this->image;
        return is_array($images) && count($images) > 0 ? $images[0] : null;
    }
    
    // Helper method to get all images
    public function getImagesAttribute()
    {
        return is_array($this->image) ? $this->image : [];
    }
}
