<?php
/*
 * @Description:
 * @Author: (c) Pian Zhou <pianzhou2021@163.com>
 * @Date: 2022-06-15 18:44:37
 * @LastEditors: Pian Zhou
 * @LastEditTime: 2022-07-21 22:59:27
 */

namespace Pianzhou\Laravel\Cacheable\Observers;

class Cacheable
{
    /**
     * Handle the saved event for the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function saved($model)
    {
        if ($model->wasChanged()) {
            $model->uncacheable();
        }
    }

    /**
     * Handle the deleted event for the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function deleted($model)
    {
        $model->uncacheable();
    }

    /**
     * Handle the force deleted event for the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function forceDeleted($model)
    {
        $model->uncacheable();
    }
}