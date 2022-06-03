<?php
/**
 * @return mixed 获取GroupID
 */
function DoveGetGroupID(): mixed
{
    if(class_exists('DACore\Util\Auth\Token') && !CLOSE_ACCESS_TOKEN){
        $group_id = DACore\Util\Auth\Token::getGroupId();
    }else{
        $group_id = config('dacore_core.default.group_id');
    }
    return $group_id;
}