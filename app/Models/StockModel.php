<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockModel extends Model
{

    use HasFactory;
	protected $table ='tbl_products_stock';
    protected $hidden = ['created_at', 'updated_at'];
    protected $guarded = [];

	public function category()
    {
		return $this->belongsTo(Category::class, 'category_id');
    }

    public function attr()
    {
       return $this->belongsTo(Color::class, 'attribute_id');
    }
	
	public function product()
    {
		return $this->belongsTo(Product::class, 'product_id');
    }

    public function sod(){
        $this->hasMany('App\TempOrderDetail', 'stock_id');
    } 
    
    public function pod(){
        $this->hasMany('App\TempPurchaseOrderDetail', 'stock_id', 'id');
    } 
  
}
