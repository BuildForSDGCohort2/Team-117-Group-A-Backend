<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';
    protected $fillable = ['requestId', 'testId', 'customerId', 'companiesId', 'result'];

    /**
     * Get the request info for the request.
     */
     public function request()
     {
         return $this->hasOne(RequestModel::class, 'id', 'customerId');
     }

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
    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'companiesId');
    }
}
