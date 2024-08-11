<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::orderBy('nama', 'asc')->get();
        if ($kategori->isEmpty()) {
            return response()->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Tidak ada kategori yang tersedia"
            ], Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json([
                "status" => Response::HTTP_OK,
                "message" => "Berhasil mendapatkan kategori",
                "data" => $kategori->map(function (Kategori $kategori) {
                    return [
                        "id" => $kategori->id,
                        "nama" => $kategori->nama,
                    ];
                }),
            ], Response::HTTP_OK);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nama" => 'required|unique:kategoris',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Nama kategori tidak valid",
                "error" => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            Kategori::create([
                'nama' => $request->nama,
            ]);

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Kategori berhasil ditambahkan'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Gagal menyimpan data: ' . $e->getMessage());

            return response()->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Gagal menyimpan Data"
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::find($id);
        if (!$kategori) {
            return response()->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Kategori tidak ditemukan"
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $validator = Validator::make($request->all(), [
                "nama" => 'required|unique:kategoris',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => Response::HTTP_BAD_REQUEST,
                    "message" => "Kategori sudah ada",
                    "error"=> $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            try {
                $kategori->update([
                    'nama' => $request->nama,
                ]);

                return response()->json([
                    'status' => Response::HTTP_OK,
                    'message' => 'Kategori berhasil diupdate'
                ], Response::HTTP_OK);

            } catch (Exception $e) {
                Log::error('Gagal update data: ' . $e->getMessage());

                return response()->json([
                    "status" => Response::HTTP_BAD_REQUEST,
                    "message" => "Gagal update data"
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    public function destroy($id)
    {
        $kategori = Kategori::find($id);

        if ($kategori) {
            try {
                $kategori->delete();
                return response()->json([
                    'status' => Response::HTTP_OK,
                    'message' => 'Kategori berhasil didelete'
                ], Response::HTTP_OK);

            } catch (Exception $e) {
                Log::error('Gagal update data: ' . $e->getMessage());

                return response()->json([
                    "status" => Response::HTTP_BAD_REQUEST,
                    "message" => "Gagal delete data"
                ], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return response()->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Kategori tidak dapat ditemukan"
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
