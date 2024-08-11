<?php

namespace App\Http\Controllers\Api;

use App\Exports\BukuExport;
use App\Http\Controllers\Controller;
use App\Models\Buku;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class BukuController extends Controller
{
    //
    public function index(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                "status" => Response::HTTP_UNAUTHORIZED,
                "message" => "Token tidak ditemukan, login terlebih dahulu",
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        // Permission admin
        if ($user->role === 1) {
            $query = Buku::orderBy('id', 'asc');
            $keyword = $request->input("kategori");

            if ($keyword) {
                $query->where("kategoris_id", $keyword);
            }

            $buku = $query->paginate(10);

            if ($buku->isEmpty()) {
                return response()->json([
                    "status" => Response::HTTP_NO_CONTENT,
                    "message" => "Data buku tidak ditemukan"
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    "status" => Response::HTTP_OK,
                    "message" => "Berhasil mendapatkan list data buku",
                    "data" => $buku,
                ], Response::HTTP_OK);
            }
        } else {
            // Permission user
            $query = Buku::where('user_id', $user->id)->orderBy('id', 'asc');
            $keyword = $request->input("kategori");

            if ($keyword) {
                $query->where("kategoris_id", $keyword);
            }

            $buku = $query->paginate(10);

            if ($buku->isEmpty()) {
                return response()->json([
                    "status" => Response::HTTP_NO_CONTENT,
                    "message" => "Data buku tidak ditemukan"
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    "status" => Response::HTTP_OK,
                    "message" => "Berhasil mendapatkan list data buku",
                    "data" => $buku,
                ], Response::HTTP_OK);
            }
        }
    }

    public function store(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Token tidak ditemukan, login terlebih dahulu",
            ], Response::HTTP_BAD_REQUEST);
        }

        $validator = Validator::make($request->all(), [
            "judul" => 'required|unique:bukus',
            "user_id" => 'required',
            "kategoris_id" => 'required',
            "deskripsi" => 'required',
            "jumlah" => 'required|integer',
            "file" => "mimes:pdf|max:2048",
            "cover" => 'mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Field tidak valid",
                "error" => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $filePath = null;
        $coverPath = null;

        if ($request->hasFile('file')) {
            // file
            $file = $request->file('file');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads/file', $fileName, 'public');
        }

        if ($request->hasFile('cover')) {
            // cover
            $cover = $request->file('cover');
            $coverName = time() . '.' . $cover->getClientOriginalExtension();
            $coverPath = $cover->storeAs('uploads/cover', $coverName, 'public');
        }

        try {
            Buku::create([
                'judul' => $request->judul,
                'user_id' => $request->user_id,
                'kategoris_id' => $request->kategoris_id,
                'deskripsi' => $request->deskripsi,
                'jumlah' => $request->jumlah,
                'file' => $filePath,
                'cover' => $coverPath,
            ]);

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Buku berhasil ditambahkan'
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
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Token tidak ditemukan, login terlebih dahulu",
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = Auth::user();
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Buku tidak ditemukan"
            ], Response::HTTP_BAD_REQUEST);
        } else if ($user->id !== 1 && ($buku->user_id !== $user->id)) {
            // Permission user
            return response()->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Tidak memiliki akses ke data buku ini"
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $validator = Validator::make($request->all(), [
                "judul" => ['required', Rule::unique('bukus')->ignore($buku->id),],
                "kategoris_id" => 'required',
                "deskripsi" => 'required',
                "jumlah" => 'required|integer',
                "cover" => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => Response::HTTP_BAD_REQUEST,
                    "message" => "Field tidak valid",
                    "error" => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            try {
                $buku->update([
                    'judul' => $request->judul,
                    'kategoris_id' => $request->kategoris_id,
                    'deskripsi' => $request->deskripsi,
                    'jumlah' => $request->jumlah,
                    'file' => $request->file,
                    'cover' => $request->cover,
                ]);

                return response()->json([
                    'status' => Response::HTTP_OK,
                    'message' => 'Buku berhasil diupdate'
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

    public function destroy(Request $request, $id)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Token tidak ditemukan, login terlebih dahulu",
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = Auth::user();
        $buku = Buku::find($id);

        if ($buku) {
            if ($user->id !== 1 && ($buku->user_id !== $user->id)) {
                // Permission user
                return response()->json([
                    "status" => Response::HTTP_BAD_REQUEST,
                    "message" => "Tidak memiliki akses ke data buku ini"
                ], Response::HTTP_BAD_REQUEST);
            }
            try {
                $buku->delete();
                return response()->json([
                    'status' => Response::HTTP_OK,
                    'message' => 'Buku berhasil didelete'
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
                "message" => "Buku tidak dapat ditemukan"
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function exportXls()
    {
        return Excel::download(new BukuExport, 'Daftar_Buku.xlsx');
    }

    public function exportPdf()
    {
        $buku = Buku::all();
        $pdf = Pdf::loadView('Buku/pdf_buku', compact('buku'))->setPaper('a4', 'landscape');

        return $pdf->download('Daftar_Buku.pdf');
    }
}
