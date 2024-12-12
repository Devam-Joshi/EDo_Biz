<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	protected $table = 'tbl_product_master';

	public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockcategory()
    {
        return $this->hasMany(StockModel::class, 'product_id', 'id')->select(['id','product_id','category_id'])->groupBy('category_id');
    }

    public function stockvariant()
    {
        return $this->hasMany(StockModel::class, 'product_id', 'id')->select(['id','product_id','attribute_id','category_id','current_stock'])->groupBy('attribute_id');
    }

    public function activevarient(){
        return $this->hasMany(StockModel::class, 'product_id', 'id')->where('status','=', 1);
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
    
}
