<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    // Table name (if different from plural of model name)
    protected $table = 'tbl_stock_qrcode';

    // Primary key (if it's not 'id')
    protected $primaryKey = 'id';

    // Disable timestamps if your table doesn't have created_at/updated_at columns
    public $timestamps = true; // Set to false if you're not using created_at and updated_at

    // Define fillable fields to allow mass assignment
    protected $fillable = [
        'stock_id',   // Foreign key referencing tbl_product_stock
        'qrcode',     // The generated QR code string
		'purchase_id',
		'purchase_return_id',
		'sale_id',
		'sale_return_id',
    ];

    // Define relationships if needed (e.g., belongsTo for stock)
    public function stock()
    {
        return $this->belongsTo(StockModel::class, 'stock_id', 'id');
    }
}
