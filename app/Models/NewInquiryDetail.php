<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class NewInquiryDetail extends Model
{
   protected $table = 'tbl_new_inquery_detail';

   function product(){
      $this->belongsTo(Product::class,'product_id');
   }

   function category(){
      $this->belongsTo(Category::class,'category_id');
   }

   public function State()
    {
 		return $this->belongsTo(State::class, 'state_id');
    }

}

?>