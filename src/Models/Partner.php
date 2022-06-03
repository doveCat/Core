<?php

namespace DACore\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use SoftDeletes;

    public $table = 'partner';

    public $fillable = [
        'store_id','username','avatar','phone','auth_status','total_income','income','freeze_income','withdrawn_income',
        'invite_code','status','group_id','app_id'
    ];
    # 合伙人的下线数
    public function invite_users(){
        return $this->hasMany(PartnerInvite::class,'partner_id','id');
    }
}
