<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Actor;
use Illuminate\Http\Request;
use Validator;

class ActorController extends Controller
{
    public function index()
    {
        $actor = Actor::latest()->get();
        $response = [
            'success' => true,
            'message' => 'Data Actor',
            'data' => $actor,
        ];
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        // validasi data
        $validator = Validator::make($request->all(), [
            'nama_actor' => 'required|unique:actors',
            'biodata' => 'required'
        ], [
            'nama_actor.required' => 'Masukkan Actor',
            'nama_actor.unique' => 'Actor Sudah Digunakan',
            'biodata.required' => 'Masukkan Biodata Actor',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan Isi Dengan Benar',
                'data' => $validator->errors(),
            ], 400);
        } else {
            $actor = new Actor;
            $actor->nama_actor = $request->nama_actor;
            $actor->biodata = $request->biodata;
            $actor->save();
        }

        if ($actor) {
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
        $actor = Actor::find($id);

        if ($actor) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Actor',
                'data' => $actor,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Actor Tidak Ditemukan',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        // validasi data
        $validator = Validator::make($request->all(), [
            'nama_actor' => 'required',
            'biodata' => 'required'
        ], [
            'nama_actor.required' => 'Masukkan Actor',
            'biodata.required' => 'Masukkan Biodata Actor',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan Isi Dengan Benar',
                'data' => $validator->errors(),
            ], 401);
        } else {
            $actor = Actor::find($id);
            $actor->nama_actor = $request->nama_actor;
            $actor->biodata = $request->biodata;

            $actor->save();
        }

        if ($actor) {
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
        $actor = Actor::find($id);
        if ($actor) {
            $actor->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data ' . $actor->nama_actor . ' Berhasil Dihapus',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ditemukan',
            ], 404);
        }
    }
}
