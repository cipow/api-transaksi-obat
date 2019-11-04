<?php

namespace App\Http\Controllers\Barang;

use App\Http\Controllers\Controller;
use App\Models\Barang\Barang;
use App\Models\Barang\Transaksi as TransaksiBarang;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;

class Transaksi extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function list(Request $req) {
        if ($req->filled('start')) $startDate = $req->start;
        else {
            $startDateModel = TransaksiBarang::orderBy('created_at', 'asc')->first();
            $startDate = $startDateModel ? $startDateModel->created_at->format("Y-m-d"):Carbon::now()->format("Y-m-d");
        }
        
        if ($req->filled('end')) $endDate = $req->end;
        else {
            $endDateModel = TransaksiBarang::orderBy('created_at', 'desc')->first();
            $endDate = $endDateModel ? $endDateModel->created_at->format("Y-m-d"):Carbon::now()->format("Y-m-d");
        }

        $endDateTransaction = (new Carbon($endDate))->addDay(1);

        $transaksi = TransaksiBarang::withCount([
            'barang as jumlah_jenis_barang',
            'barang as jumlah_total_barang' => function($q) {
                $q->select(\DB::raw("CAST(SUM(tr_barang_detail.jumlah) as SIGNED)"));
            }
        ])
        ->whereBetween('created_at', [$startDate, $endDateTransaction])
        ->with('barang')
        ->get();


        return $this->response->success([
            "start" => $startDate,
            "end" => $endDate,
            "data" => $transaksi
        ]);
    }

    public function get($id) {
        $transaksi = TransaksiBarang::withCount([
            'barang as jumlah_jenis_barang',
            'barang as jumlah_total_barang' => function($q) {
                $q->select(\DB::raw("CAST(SUM(tr_barang_detail.jumlah) as SIGNED)"));
            }
        ])->with('barang')->find($id);

        if ($transaksi) return $this->response->success($transaksi);
        else return $this->response->error('transaksi tidak ditemukan');
    }

    public function store(Request $req) {
        $input = $req->only('barang');
        $validation = Validator::make($input, TransaksiBarang::RULE, TransaksiBarang::RULE_MESSAGE);
        if ($validation->fails()) return $this->response->notValidInput($validation->errors());

        $data_transaksi = [
            'kode' => str_random()
        ];

        if ($req->filled('tanggal')) {
            $time = Carbon::now()->format("H:i:s");
            $data_transaksi['created_at'] = "$req->tanggal $time";
            $data_transaksi['updated_at'] = "$req->tanggal $time";
        }

        \DB::beginTransaction();

        $transaksi = TransaksiBarang::create($data_transaksi);
        
        $total = 0;
        $success = true;
        $error_message = "";

        foreach ($input['barang'] as $barang) {
            if (Barang::find($barang['id'])) {
                $sub_total = $barang['harga'] * $barang['jumlah'];

                $total+=$sub_total;
                $transaksi->barang()->attach($barang['id'], [
                    'harga' => $barang['harga'],
                    'jumlah' => $barang['jumlah'],
                    'total' => $sub_total
                ]);
            }
            else {
                $success = false;
                $error_message = 'barang dengan id '.$barang['id'].' tidak ditemukan, transaksi dibatalkan';
                break;
            }
        }

        if ($success) {
            $transaksi->update(['total' => $total]);
            \DB::commit();
            return $this->response->success(
                TransaksiBarang::withCount([
                    'barang as jumlah_jenis_barang',
                    'barang as jumlah_total_barang' => function($q) {
                        $q->select(\DB::raw("CAST(SUM(tr_barang_detail.jumlah) as SIGNED)"));
                    }
                ])->with('barang')->find($transaksi->id)
            );
        }
        else {
            \DB::rollBack();
            return $this->response->error($error_message);
        }

    }

    public function delete($id) {
        $transaksi = TransaksiBarang::find($id);
        $kode = $transaksi->kode;
        $transaksi->delete();
        return $this->response->success([
            'message' => 'transaksi dengan kode '.$kode.' sudah dihapus'
        ]);
    }
}