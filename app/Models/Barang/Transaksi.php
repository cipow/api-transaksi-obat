<?php

namespace App\Models\Barang;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model {
    protected $table = 'tr_barang';

    protected $guarded = [];

    public function barang() {
        return $this->belongsToMany('App\Models\Barang\Barang', 'tr_barang_detail', 'tr_barang_id', 'mr_barang_id')
            ->withPivot('jumlah', 'total');
    }
}