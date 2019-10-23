<?php

namespace App\Http\Controllers\Barang;

use App\Http\Controllers\Controller;
use App\Models\Barang\Barang as MasterBarang;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;

class Barang extends Controller {

    private $barang;
    
    public function __construct() {
        parent::__construct();
    }

    private function findOrFail($id) {
        try {
            $barang = MasterBarang::findOrFail($id);
            return $barang;
        } catch (Exception $e) {
            throw new ModelNotFoundException('barang tidak ditemukan');
        }
    }

    public function list() {
        $list_barang = MasterBarang::get();
        return $this->response->success($list_barang);
    }

    public function get($id) {
        $barang = $this->findOrFail($id);
        return $this->response->success($barang);
    }

    public function delete($id) {
        $barang = $this->findOrFail($id);
        $nama_barang = $barang->nama;
        $barang->delete();
        return $this->response->success("$nama_barang berhasil dihapus");
    }

    public function store(Request $req) {
        $input = $req->only('nama', 'harga');
        $validation = Validator::make($input, MasterBarang::RULE, MasterBarang::RULE_MESSAGE);
        if ($validation->fails()) return $this->response->notValidInput($validation->errors());

        $barang = MasterBarang::create($input);
        return $this->response->success($barang);
    }

    public function update(Request $req, $id) {
        $barang = $this->findOrFail($id);
        $input = $req->only('nama', 'harga');
        $validation = Validator::make($input, MasterBarang::RULE, MasterBarang::RULE_MESSAGE);
        if ($validation->fails()) return $this->response->notValidInput($validation->errors());

        $barang->update($input);
        return $this->response->success($barang);
    }
}