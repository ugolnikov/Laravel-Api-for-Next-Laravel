<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Seller $seller): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Seller $seller, Product $product): bool
    {
        return $seller->id === $product->seller_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Seller $seller): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Seller $seller, Product $product): bool
    {
        return $seller->id === $product->seller_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Seller $seller, Product $product): bool
    {
        return $seller->id === $product->seller_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Seller $seller, Product $product): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Seller $seller, Product $product): bool
    {
        return false;
    }
}
