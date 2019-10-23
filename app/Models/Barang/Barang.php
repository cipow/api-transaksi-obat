<?php

namespace App\Models\Barang;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model {
    
    const RULE = [
        'nama' => 'required',
        'harga' => 'required|numeric'
    ];

    const RULE_MESSAGE = [
        'nama.required' => 'nama tidak boleh kosong',
        'harga.required' => 'harga tidak boleh kosong',
        'harga.numeric' => 'harga harus angka'
    ];

    protected $table = 'mr_barang';

    protected $guarded = [];

    protected $casts = [
        'harga' => 'double'
    ];

    public function transaksi() {
        return $this->belongsToMany('App\Models\Barang\Transaksi', 'tr_barang_detail', 'mr_barang_id', 'tr_barang_id')
            ->withPivot('jumlah', 'total');
    }
}