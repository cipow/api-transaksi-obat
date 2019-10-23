<?php

namespace App\Models\Barang;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model {

    const RULE = [
        'tanggal' => 'date',
        'barang' => 'required',
        'barang.*.id' => 'required|integer',
        'barang.*.harga' => 'required|numeric',
        'barang.*.jumlah' => 'required|integer'
    ];

    const RULE_MESSAGE = [
        'barang.required' => 'daftar barang tidak boleh kosong',
        'barang.*.id.required' => 'id barang tidak boleh kosong',
        'barang.*.id.integer' => 'id barang harus bilangan bulat',
        'barang.*.harga.required' => 'harga barang tidak boleh kosong',
        'barang.*.harga.numeric' => 'harga barang harus angka',
        'barang.*.jumlah.required' => 'jumlah barang tidak boleh kosong',
        'barang.*.jumlah.integer' => 'jumlah barang harus bilangan bulat',
    ];

    protected $table = 'tr_barang';

    protected $guarded = [];

    public function barang() {
        return $this->belongsToMany('App\Models\Barang\Barang', 'tr_barang_detail', 'tr_barang_id', 'mr_barang_id')
            ->withPivot('harga', 'jumlah', 'total');
    }
}