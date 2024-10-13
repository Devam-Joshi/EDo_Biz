<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
  protected $table ='tbl_categories';
  protected $hidden = ['created_at', 'updated_at'];
  protected $guarded = [];
  
  /*  public function products()
    {
        return $this->hasMany(Product::class);
    }
    */

    // public function products()
    // {
    //     if ($this->status == 0) {
    //         return $this->hasMany(StockModel::class, 'category_id', 'id');
    //     }
    // }

    // public function tax()
    // {
    //     return $this->belongsTo(GstModel::class, 'tax_id', 'id');
    // }

    // public function parent()
    // {
    //     return $this->belongsTo(ParentCatModel::class, 'parent_id', 'id');
    // }
    
    // public function parentcat(){
    //   return $this->hasMany(CategoryMapModel::class, 'category_id', 'id')->with('parent');
    // }

    // public function user()
    // {
    //     return $this->belongsTo(UserModel::class, 'user_id', 'id');
    // }

}
