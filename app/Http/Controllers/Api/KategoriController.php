<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Validator;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::latest()->get();
        $response = [
            'success' => true,
            'message' => 'Data Kategori',
            'data' => $kategori,
        ];
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        // validasi data
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|unique:kategoris',
        ], [
            'nama_kategori.required' => 'Masukkan Kategori',
            'nama_kategori.unique' => 'Kategori Sudah Digunakan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan Isi Dengan Benar',
                'data' => $validator->errors(),
            ], 400);
        } else {
            $kategori = new Kategori;
            $kategori->nama_kategori = $request->nama_kategori;
            $kategori->save();
        }

        if ($kategori) {
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data Gagal Disimpan',
            ], 400);
        }
    }

    public function show($id)
    {
        $kategori = Kategori::find($id);

        if ($kategori) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Kategori',
                'data' => $kategori,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Tidak Ditemukan',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        // validasi data
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required',
        ], [
            'nama_kategori.required' => 'Masukkan Kategori',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan Isi Dengan Benar',
                'data' => $validator->errors(),
            ], 401);
        } else {
            $kategori = Kategori::find($id);
            $kategori->nama_kategori = $request->nama_kategori;
            $kategori->save();
        }

        if ($kategori) {
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Diperbarui',
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data Gagal Diperbarui',
            ], 400);
        }
    }

    public function destroy($id)
    {
        $kategori = Kategori::find($id);
        if ($kategori) {
            $kategori->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data ' . $kategori->nama_kategori . ' Berhasil Dihapus',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ditemukan',
            ], 404);
        }
    }
}
