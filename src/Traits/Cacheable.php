<?php
/*
 * @Description:
 * @Author: (c) Pian Zhou <pianzhou2021@163.com>
 * @Date: 2022-06-15 18:44:37
 * @LastEditors: Pian Zhou
 * @LastEditTime: 2022-07-21 22:59:27
 */
namespace Pianzhou\Laravel\Cacheable\Traits;

use Illuminate\Support\Facades\Cache;
use Pianzhou\Laravel\Cacheable\Observers\Cacheable as ObserversCacheable;

/**
 * @property string $cacheStore
 */
trait Cacheable
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootCacheable()
    {
        static::observe(new ObserversCacheable);
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function cacheableAs($id = '__ALL__')
    {
        return config('cacheable.prefix') . '__CACHEABLE__' . $this->getTable() . '__' . $id;
    }

    /**
     * 缓存标记
     *
     * @return mixed
     */
    public function cacheTags()
    {
        return property_exists($this, 'cacheTags') ? $this->cacheTags : [];
    }

    /**
     * 缓存驱动
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function cacheStore()
    {
        $store = Cache::store(property_exists($this, 'cacheStore') ? $this->cacheStore : null);

        if ($store->supportsTags()) {
            $store->tags($this->cacheTags());
        }

        return $store;
    }

    /**
     * 删除缓存
     *
     * @return void
     */
    public function uncacheable()
    {
        // 清除缓存标记
        $this->cacheStore()->flush();
        // 清除全表缓存
        $this->cacheStore()->forget($this->cacheableAs());
        // 清除缓存
        $this->cacheStore()->forget($this->cacheableAs($this->getKey()));
    }

    /**
     * 查找并缓存
     *
     * @param mixed $id
     * @param integer $ttl
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function cacheableById($id, $ttl = 600)
    {
        return $this->cacheStore()->remember($this->cacheableAs($id), $ttl, function() use ($id) {
            return static::find($id);
        });
    }

    /**
     * 缓存全表
     *
     * @param mixed $id
     * @param integer $ttl
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function cacheable($ttl = 600)
    {
        return $this->cacheStore()->remember($this->cacheableAs(), $ttl, function() {
            return static::get();
        });
    }

    /**
     * 查找并缓存
     *
     * @param mixed $id
     * @param integer $ttl
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function cachedById($id, $ttl = 600)
    {
        return (new static)->cacheableById($id, $ttl);
    }

    /**
     * 查找并缓存所有数据
     *
     * @param integer $ttl
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function cached($ttl = 600)
    {
        return (new static)->cacheable($ttl);
    }
}
