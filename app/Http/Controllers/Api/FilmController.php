<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Film;
use Illuminate\Http\Request;
use Validator;

class FilmController extends Controller
{
    public function index()
    {
        $film = Film::with(['genre', 'actor'])->get();
        return response()->json([
            'success' => true,
            'message' => 'Data Film',
            'data' => $film,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|unique:films',
            'deskripsi' => 'required|string',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'url_video' => 'required|string',
            'id_kategori' => 'required|exists:kategoris,id',
            'genre' => 'required|array',
            'actor' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $path = $request->file('foto')->store('public/foto');

            $film = Film::create([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'foto' => $path,
                'url_video' => $request->url_video,
                'id_kategori' => $request->id_kategori,
            ]);

            $film->genre()->attach($request->genre);
            $film->genre()->attach($request->actor);

            return response()->json([
                'succes' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $film,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $film = Film::with(['genre', 'actor'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Detail Film',
                'data' => $film,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $film = Film::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|unique:films,judul,' . $id,
            'deskripsi' => 'required|string',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'url_video' => 'required|string',
            'id_kategori' => 'required|exists:kategoris,id',
            'genre' => 'required|array',
            'actor' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if ($request->hasFile('foto')) {
                // Delete old photo
                Storage::delete($film->foto);

                $path = $request->file('foto')->store('public/foto');
                $film->foto = $path;
            }

            $film->update($request->only(['judul', 'deskripsi', 'ur_video', 'id_kategori']));

            if ($request->has('genre')) {
                $film->genre()->sync($request->genre);
            }

            if ($request->has('actor')) {
                $film->actor()->sync($request->actor);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Diperbarui',
                'data' => $film,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An Error Occurred',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $film = Film::findOrFail($id);

            // Delete photo
            Storage::delete($film->foto);

            $film->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data Deleted Successfully',
                'data' => null,
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data Not Found',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }
}
