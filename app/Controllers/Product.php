<?php

namespace App\Controllers;

class Product extends BaseController
{
    public function getAll()
    {
        return 'get all products';
    }

    public function getByName()
    {
        return 'get products by search';
    }

    public function getByPrice()
    {
        return 'get products by price';
    }

    public function getById($id)
    {
        return 'get product by id: ' . $id;
    }

    public function create()
    {
        return 'create product';
    }

    public function update($id)
    {
        return 'update product by id: ' . $id;
    }

    public function delete($id)
    {
        return 'delete product by id: ' . $id;
    }

}
