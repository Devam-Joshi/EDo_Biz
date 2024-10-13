<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryMapModel extends Model
{
  protected $table ='tbl_category_parent_mapping';
  public $timestamps = false;

    // public function parent()
    // {
    //     return $this->belongsTo(ParentCatModel::class, 'parent_id', 'id');
    // }

    // public function category()
    // {
    //     return $this->belongsTo(CategoryModel::class, 'category_id', 'id');
    // }

}
