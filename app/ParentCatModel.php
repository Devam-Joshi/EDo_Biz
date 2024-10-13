<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParentCatModel extends Model
{
  protected $table ='tbl_categories_parent';
  protected $hidden = ['created_at', 'updated_at'];
  protected $guarded = [];
  
    public function map()
    {
      return $this->hasMany(CategoryMapModel::class, 'parent_id', 'id')->with('parent:id,name','category:id,name')->select(['category_id','parent_id']);
    }

    public function saveCategory($data=[],$object_id=0,$object = null){
      if(!empty($object)){
          //
      }
      elseif($object_id > 0){
          $object = $this->find($object_id);
      }
      else{
          $object = new ParentCatModel();
      }
      $object->fill($data);
      $object->save();
     
      return $object;
}

}
