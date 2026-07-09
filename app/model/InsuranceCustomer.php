<?php
namespace app\model;

class InsuranceCustomer extends BaseModel
{
    protected $name = 'insurance_customer';

    public function policies()
    {
        return $this->hasMany(InsurancePolicy::class, 'customer_id');
    }
}

