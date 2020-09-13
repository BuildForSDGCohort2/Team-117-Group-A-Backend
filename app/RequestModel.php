<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestModel extends Model
{
    protected $table = 'requests';
    protected $fillable = ['testId', 'customerId', 'address', 'accepted'];
    /**
     * Get the customer for the request.
     */
    public function customer()
    {
        return $this->hasOne(User::class, 'id', 'customerId');
    }

    /**
     * Get the test for the request.
     */
    public function test()
    {
        return $this->hasOne(Test::class, 'id', 'testId');
    }

    /**
     * Get the company that accepted the request.
     */
    public function acceptedCompany()
    {
        return $this->hasOne('App\Company');
    }
}
