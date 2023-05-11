<?php

namespace App\Http\Controllers;

use App\Http\Resources\LowonganPKLResource;
use App\Models\LowonganPKL;
use Illuminate\Support\Str;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LowonganPKLController extends Controller
{
    public function index()
    {
        $lowongan_pkl = LowonganPKL::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Lowongan PKL',
            'data' => LowonganPKLResource::collection($lowongan_pkl)
        ], 200);
    }

    public function showByProdi()
    {
        $prodi = Auth::user();

        if (!$prodi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data prodi tidak ditemukan'
            ], 404);
        }

        $lowongan_pkl = LowonganPKL::where('id_prodi', $prodi->id_prodi)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data Lowongan PKL',
            'data' => LowonganPKLResource::Collection($lowongan_pkl)
        ], 200);
    }

public function searchByKeyword(Request $request)
{
    $keyword = $request->query('q');

    if (empty($keyword)) {
        $lowongan_pkl = [];
    } else {
        $lowongan_pkl = LowonganPKL::where('posisi', 'like', '%' . $keyword . '%')
            ->orWhere('nama_perusahaan', 'like', '%' . $keyword . '%')
            ->get();
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Hasil Pencarian',
        'data' => LowonganPKLResource::collection($lowongan_pkl)
    ], 200);
}



    public function prosple()
    {
        $url = 'https://id.prosple.com/search-jobs?opportunity_types=2&locations=9714%2C9714%7C24768&defaults_applied=1&study_fields=502';

        $goutteClient = new Client(HttpClient::create(['timeout' => 60, 'verify_peer' => base_path('prosple-cert.crt')]));

        $crawler = $goutteClient->request('GET', $url);
        $data = $crawler->filter("li[class='SearchResultsstyle__SearchResult-sc-c560t5-1 hlOmzw']")->each(function ($node) {
            return [
                'posisi' => $node->filter("a[class='JobTeaserstyle__JobTeaserTitleLink-sc-1p2iccb-2 eiICbF']")->text(),
                'nama_perusahaan' => $node->filter("header[class='Teaser__TeaserHeader-sc-129e2mv-1 JobTeaserstyle__JobTeaserHeader-sc-1p2iccb-1 iBnwQU bycdHT']")->text(),
                'alamat_perusahaan' => $node->filter("div[class='sc-gsTCUz JobTeaserstyle__JobLocation-sc-1p2iccb-8 hAURsc jOLgFK']")->text(),
                'gambar' => $node->filter("img[src]")->attr('src'),
                'url' => 'https://id.prosple.com' . $node->filter("a[href]")->attr('href'),
                'sumber' => 'Prosple'
            ];
        });

        return response()->json([
            "message" => "Scraping data dari prosple",
            "status" => "Success",
            "data" => $data
        ]);
    }

    public function prospleStoreDb()
    {
        $url = 'https://id.prosple.com/search-jobs?opportunity_types=2&locations=9714%2C9714%7C24768&defaults_applied=1&study_fields=502';

        $goutteClient = new Client(HttpClient::create(['timeout' => 60, 'verify_peer' => base_path('prosple-cert.crt')]));

        $crawler = $goutteClient->request('GET', $url);
        $data = $crawler->filter("li[class='SearchResultsstyle__SearchResult-sc-c560t5-1 hlOmzw']")->each(function ($node) {
            return [
                'id_prodi' => Auth::id(),
                'posisi' => $node->filter("a[class='JobTeaserstyle__JobTeaserTitleLink-sc-1p2iccb-2 eiICbF']")->text(),
                'nama_perusahaan' => $node->filter("header[class='Teaser__TeaserHeader-sc-129e2mv-1 JobTeaserstyle__JobTeaserHeader-sc-1p2iccb-1 iBnwQU bycdHT']")->text(),
                'alamat_perusahaan' => $node->filter("div[class='sc-gsTCUz JobTeaserstyle__JobLocation-sc-1p2iccb-8 hAURsc jOLgFK']")->text(),
                'gambar' => $node->filter("img[src]")->attr('src'),
                'url' => 'https://id.prosple.com' . $node->filter("a[href]")->attr('href'),
                'sumber' => 'Prosple'
            ];
        });

        foreach ($data as $data) {
            LowonganPKL::create($data);
        }

        return response()->json([
            "message" => "Simpan data lowongan dari prosple ke database",
            "status" => "Success",
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'posisi' => 'required',
            'nama_perusahaan' => 'required',
            'alamat_perusahaan' => 'required',
            'gambar' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $file = $request->file('gambar');
        $destinationPath = "public\images";
        $filename = 'lowongan_pkl_' . date("Ymd_his") . '.' . $file->extension();
        $lowongan_pkl = LowonganPKL::create([
            'id_prodi' => Auth::id(),
            'posisi' => $request->posisi,
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat_perusahaan' => $request->alamat_perusahaan,
            'gambar' => $filename,
            'url' => $request->url,
            'sumber' => 'Politeknik TEDC Bandung'
        ]);
        Storage::putFileAs($destinationPath, $file, $filename);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Lowongan PKL',
            'data' => new LowonganPKLResource($lowongan_pkl)
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $lowongan_pkl = LowonganPKL::findOrFail($id);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $destinationPath = "public\images";
            $filename = 'lowongan_pkl_' . date("Ymd_his") . '.' . $file->extension();
            Storage::putFileAs($destinationPath, $file, $filename);
            Storage::delete('public/images/' . $lowongan_pkl->gambar);
            $lowongan_pkl->gambar = $filename;
            $lowongan_pkl->update($request->except('gambar'));
        } else {
            $lowongan_pkl->update($request->all());
        }


        return response()->json([
            'status' => 'success',
            'message' => 'Data Lowongan PKL',
            'data' => new LowonganPKLResource($lowongan_pkl)
        ], 200);
    }

    public function destroy($id)
    {
        $lowongan_pkl = LowonganPKL::findOrFail($id);
        Storage::delete('public/images/' . $lowongan_pkl->gambar);
        $lowongan_pkl->delete();

        if ($lowongan_pkl != null) {
            return response()->json([
                'message' => 'Lowongan PKL Terhapus',
                'data' => $lowongan_pkl
            ]);
        } else {
            return response()->json([
                'message' => 'Lowongan PKL Tidak Ditemukan',
            ], 404);
        }
    }
}
