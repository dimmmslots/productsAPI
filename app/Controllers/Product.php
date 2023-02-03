<?php

namespace App\Controllers;

class Product extends BaseController
{
    private $productModel;
    public function __construct()
    {
        // init model 
        $this->productModel = new \App\Models\Product();
    }
    public function getAll()
    {
        try {
            $name = $this->request->getGet('name') ? $this->request->getGet('name') : null;
            $min = $this->request->getGet('min') ? $this->request->getGet('min') : null;
            $max = $this->request->getGet('max') ? $this->request->getGet('max') : null;

            // prepared statement
            $query = $this->productModel->select('*');
            if ($name) {
                $query->like('name', $name);
            }
            if ($min) {
                $query->where('price >=', $min);
            }
            if ($max) {
                $query->where('price <=', $max);
            }
            $products = $query->get()->getResultArray();

            $data = [
                'message' => 'success',
                'count' => count($products),
                'data' => $products
            ];

            if (count($products) > 0) {
                return $this->response->setStatusCode(200)->setJSON($products);
            } else {
                $empty = [
                    'message' => 'No products found',
                    'count' => 0,
                    'data' => []
                ];
                return $this->response->setStatusCode(200)->setJSON($empty);
            }
        } catch (\Throwable $th) {
            return $this->response->setStatusCode(500)->setJSON($th->getMessage());
        }
    }

    public function getById($id)
    {
        try {
            $product = $this->productModel->find($id);
            if ($product) {
                $data = [
                    'message' => 'found',
                    'data' => $product
                ];
                return $this->response->setStatusCode(200)->setJSON($product);
            } else {
                $empty = [
                    'message' => 'No product found',
                    'data' => []
                ];
                return $this->response->setStatusCode(200)->setJSON($empty);
            }
        } catch (\Throwable $th) {
            return $this->response->setStatusCode(500)->setJSON($th->getMessage());
        }
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
