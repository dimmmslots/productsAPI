<?php

namespace App\Controllers;

use Rakit\Validation\Validator;

use function PHPUnit\Framework\isEmpty;

class Product extends BaseController
{
    private $productModel;
    private $validate;
    public function __construct()
    {
        // init model 
        $this->productModel = new \App\Models\Product();
        $this->validate = new Validator;
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
        try {
            // init validator
            $name = $this->request->getPost('name');
            $price = $this->request->getPost('price');
            $product = [
                'name' => $name,
                'price' => $price
            ];
            $validator = $this->validate->validate($product, [
                'name' => 'required|min:2|max:50',
                'price' => 'required|numeric|min:1|integer'
            ]);
            if ($validator->fails()) {
                $data = [
                    'message' => 'validation error',
                    'data' => $validator->errors()->firstOfAll() 
                ];
                return $this->response->setStatusCode(400)->setJSON($data);
            }
            $checkName = $this->productModel->where('name', $name)->first();
            if ($checkName) {
                $data = [
                    'message' => 'product name already exists',
                    'data' => []
                ];
                return $this->response->setStatusCode(400)->setJSON($data);
            }

            $this->productModel->insert($product);
            $data = [
                'message' => 'success',
                'data' => $product
            ];
            return $this->response->setStatusCode(201)->setJSON($data);
        } catch (\Throwable $th) {
            return $this->response->setStatusCode(500)->setJSON($th->getMessage());
        }
    }

    public function update($id)
    {
        try {
            $old = $this->productModel->find($id);
            if (!$old) {
                $data = [
                    'message' => 'product not found',
                    'data' => []
                ];
                return $this->response->setStatusCode(404)->setJSON($data);
            }
            $name = $this->request->getPost('name') ? $this->request->getPost('name') : $old['name'];
            $price = $this->request->getPost('price') ? $this->request->getPost('price') : $old['price'];

            // init validator
            $product = [
                'name' => $name,
                'price' => $price
            ];

            $validator = $this->validate->validate($product, [
                'name' => 'required|min:2|max:50',
                'price' => 'required|numeric|min:1|integer'
            ]);

            if ($validator->fails()) {
                $data = [
                    'message' => 'validation error',
                    'data' => $validator->errors()->firstOfAll()
                ];
                return $this->response->setStatusCode(400)->setJSON($data);
            }

            if ($name == $old['name']) {
                $product = [
                    'name' => $name,
                    'price' => $price
                ];
            } else {
                $isNameExists = $this->productModel->where('name', $name)->first();
                // check if isNameExists is not null
                if ($isNameExists) {
                    $data = [
                        'message' => 'product name already exists',
                        'data' => []
                    ];
                    return $this->response->setStatusCode(400)->setJSON($data);
                }
                $product = [
                    'name' => $name,
                    'price' => $price
                ];
            }
            $this->productModel->update($id, $product);
            $data = [
                'message' => 'success',
                'data' => $product
            ];
            return $this->response->setStatusCode(200)->setJSON($data);
        } catch (\Throwable $th) {
            return $this->response->setStatusCode(500)->setJSON($th->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $product = $this->productModel->find($id);
            if (!$product) {
                $data = [
                    'message' => 'product not found',
                    'data' => []
                ];
                return $this->response->setStatusCode(404)->setJSON($data);
            }

            $this->productModel->delete($id);
            $data = [
                'message' => 'success',
                'data' => $product
            ];
            return $this->response->setStatusCode(200)->setJSON($data);
        } catch (\Throwable $th) {
            return $this->response->setStatusCode(500)->setJSON($th->getMessage());
        }
    }
}
