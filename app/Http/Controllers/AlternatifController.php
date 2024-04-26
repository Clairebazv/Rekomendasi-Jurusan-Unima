<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\Alternatif;
use App\Models\DetailAlternatif;
use App\Models\Perankingan;
use DB, Validator, Route;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class AlternatifController extends Controller
{

    public function index()
    {

        $kriteria = Kriteria::orderBy('created_at','ASC')->get(['nama']);

        $alternatif = Alternatif::orderBy('created_at', 'ASC')
                                    ->get(['id','nama']);

        $daftarAlternatif = [];
        foreach ($alternatif as $value) {
            $temp = DetailAlternatif::leftJoin('kriteria', 'detail_alternatif.kriteria_id', 'kriteria.id')->orderBy('kriteria.created_at','ASC') // join agar bisa orderBy sesuai dengan created_at pada tabel kriteria
                                        ->where('alternatif_id', $value->id)->get(['nama_subkriteria']);
            array_push($daftarAlternatif, ['id'=>$value->id,'nama_alternatif'=>$value->nama,'subkriteria'=>$temp]);
        }

        // return response()->json(['status'=>'success', 'data'=>$daftarAlternatif],200);
        // dd($daftarAlternatif,$id);
        return view('pages.alternatif.index', compact('kriteria','daftarAlternatif'));
    }

    public function create()
    {
        $kriteria = Kriteria::orderBy('kode','ASC')->get(['id','nama']);
        
        $daftarKriteria = [];
        foreach ($kriteria as $value) {
            $temp = SubKriteria::where('kriteria_id', $value->id)->orderBy('created_at', 'ASC')->get(['id','nama']);
            array_push($daftarKriteria, ['kriteria_id'=>$value->id, 'nama_kriteria'=>$value->nama, 'subkriteria'=>$temp]);
        }

        return view('pages.alternatif.create', compact('daftarKriteria'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $kriteria = Kriteria::orderBy('kode','ASC')->get(['id','nama']);

        // START // mengatur rules secara dinamis sesuai dengan daftar kriteria yg ada
        $rules = ['nama' => 'required'];
        foreach ($kriteria as $value) {
            $rules += [$value->id => 'required'];
        }
        // END // mengatur rules secara dinamis sesuai dengan daftar kriteria yg ada

        $this->validate($request,$rules,['required'=>'Tidak boleh kosong.']);

        try {
            $alternatif = Alternatif::create([
                'nama'=>$request->nama,
            ]);

            $detailAlternatif = [];
            foreach ($kriteria as $key => $value) {
                $subkriteria = SubKriteria::where('id',$request->get($value->id))->first(['nama']);
                array_push($detailAlternatif, [
                    'id' => Str::uuid()->toString(),
                    'alternatif_id' => $alternatif->id,
                    'kriteria_id' => $value->id,
                    'nama_kriteria' => $value->nama,
                    'subkriteria_id' => $request->get($value->id),
                    'nama_subkriteria' => $subkriteria?->nama,
                    'created_at'=>\Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at'=>\Carbon\Carbon::now()->toDateTimeString(),
                ]);
            }

            if($alternatif){
                DetailAlternatif::insert($detailAlternatif);
            }

            $this->updateNilaiPerankingan($alternatif->id);

            Alert::success('Berhasil', "Alternatif berhasil disimpan");
            DB::commit();

            return redirect()->route('alternatif.index');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th->getMessage());
            Alert::error('Terjadi kesalahan', $th->getMessage());
            return back();
        }
    }

    public function updateNilaiPerankingan($idAlternatif){
        $nilai = 0;

        $detailAlternatif = DetailAlternatif::where('alternatif_id',$idAlternatif)
                                                ->leftJoin('kriteria', 'detail_alternatif.kriteria_id', 'kriteria.id')
                                                ->leftJoin('sub_kriteria', 'detail_alternatif.subkriteria_id', 'sub_kriteria.id')
                                                ->get(['kriteria.rata_eigen as kriteria_rata_eigen', 'sub_kriteria.rata_eigen as subkriteria_rata_eiden']);

        foreach ($detailAlternatif as $value) {
            $nilai += $value->kriteria_rata_eigen * $value->subkriteria_rata_eiden;
        }
        // dd($nilai);
        $perankingan = Perankingan::where('alternatif_id', $idAlternatif)->first();
        // dd($nilai);
        if($perankingan) {
            Perankingan::where('alternatif_id', $idAlternatif)->update(['nilai'=>$nilai]);
        }else{
            Perankingan::create([
                'alternatif_id' => $idAlternatif,
                'nilai' => $nilai
            ]);
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {        
        $kriteria = Kriteria::orderBy('kode','ASC')->get(['id','nama']);
        $pengguna = Alternatif::where('id',$id)->first();

        $daftarKriteria = [];
        foreach ($kriteria as $value) {
            $subkriteria = SubKriteria::where('kriteria_id', $value->id)->orderBy('created_at', 'ASC')->get(['id','nama']);
            $detailAlternatif = DetailAlternatif::where('alternatif_id',$id)->where('kriteria_id', $value->id)->first();
            array_push($daftarKriteria, [
                'kriteria_id'=>$value->id, 
                'nama_kriteria'=>$value->nama, 
                'subkriteria_id'=>$detailAlternatif->subkriteria_id, 
                'subkriteria'=>$subkriteria
            ]);
        }
        // return response()->json(['status'=>'success', 'data'=>$daftarKriteria],200);
        return view('pages.alternatif.edit', compact('daftarKriteria','pengguna'));
        // dd($id);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $kriteria = Kriteria::orderBy('kode','ASC')->get(['id','nama']);

        // START // mengatur rules secara dinamis sesuai dengan daftar kriteria yg ada
        $rules = [];
        foreach ($kriteria as $value) {
            $rules += [$value->id => 'required'];
        }
        // END // mengatur rules secara dinamis sesuai dengan daftar kriteria yg ada

        $this->validate($request,$rules,['required'=>'Tidak boleh kosong.']);

        try {
            $detailAlternatif = [];
            foreach ($kriteria as $key => $value) {
                $subkriteria = SubKriteria::where('id',$request->get($value->id))->first(['nama']);
                array_push($detailAlternatif, [
                    'id' => Str::uuid()->toString(),
                    'alternatif_id' => $id,
                    'kriteria_id' => $value->id,
                    'nama_kriteria' => $value->nama,
                    'subkriteria_id' => $request->get($value->id),
                    'nama_subkriteria' => $subkriteria?->nama,
                    'created_at'=>\Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at'=>\Carbon\Carbon::now()->toDateTimeString(),
                ]);
            }

            DetailAlternatif::where('alternatif_id', $id)->delete(); // hapus detail alternatif sesuai dgn alternatif yg dipilih
            DetailAlternatif::insert($detailAlternatif);

            $this->updateNilaiPerankingan($id);
            
            Alert::success('Berhasil', "Alternatif berhasil disimpan");
            DB::commit();
            
            return redirect()->route('alternatif.index');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            // dd($th->getMessage());
            Alert::error('Terjadi kesalahan', $th->getMessage());
            return back();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            Alternatif::where('id',$id)->delete();
            DetailAlternatif::where('alternatif_id',$id)->delete();
            Perankingan::where('alternatif_id',$id)->delete();

            Alert::success('Berhasil', "Alternatif berhasil dihapus");
            DB::commit();

            return response()->json(['status'=>'success', 'message'=>'Berhasil dihapus.'],200);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['status'=>'failed','message'=>$th->getMessage()],500);
        }
    }
}
