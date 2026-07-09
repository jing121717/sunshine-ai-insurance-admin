<?php
namespace app\model;

use think\Model;

class BaseModel extends Model
{
    protected $autoWriteTimestamp = false;

    public static function updateOrCreate(array $where, array $data): self
    {
        $model = static::where($where)->find();
        if ($model) {
            $model->save($data);
            return $model;
        }

        $model = new static();
        $model->save(array_merge($where, $data));
        return $model;
    }
}
