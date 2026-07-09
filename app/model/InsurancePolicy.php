<?php
namespace app\model;

class InsurancePolicy extends BaseModel
{
    protected $name = 'insurance_policy';

    public function customer()
    {
        return $this->belongsTo(InsuranceCustomer::class, 'customer_id');
    }
}

