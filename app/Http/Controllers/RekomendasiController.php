<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\BobotKriteria;
use App\Models\BobotKriteriaMhs;
use App\Models\Perankingan;
use App\Models\Alternatif;
use App\Models\DetailAlternatif;
use App\Models\Rekomendasi;
use App\Models\KriteriaUser;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use DB, Auth, PDF;

class RekomendasiController extends Controller
{

    public function index()
    {
        $subkriteria = BobotKriteria::leftJoin('kriteria as satu', 'bobot_kriteria.kriteria1', 'satu.kode')
                                        ->leftJoin('kriteria as dua', 'bobot_kriteria.kriteria2', 'dua.kode')
                                        ->where('bobot', '>', 1)
                                        ->orderByRaw('kriteria1 ASC, bobot_kriteria.bobot ASC')
                                        // ->orderBy('kriteria1', 'ASC')
                                        ->get(['satu.nama as kriteria1_nama','dua.nama as kriteria2_nama','bobot_kriteria.bobot']);

        $perankingan = Perankingan::leftJoin('alternatif', 'perankingan.alternatif_id', 'alternatif.id')->orderBy('nilai', 'DESC')->get(['alternatif.nama']);
        // $kriteria = [];

        // foreach ($subkriteria as $value) {
        //     $temp = Kriteria::where()
        // }

        // dd($perankingan);

        return view('pages.rekomendasi.index', compact('subkriteria','perankingan'));
    }

    // old
    // public function create()
    // {
    //     // $kriteria = Kriteria::orderBy('kode','ASC')->get(['kode','nama']);

    //     $kriteria = Kriteria::orderBy('kode','ASC')->get(['id','kode','nama','jumlah_bobot','jumlah_eigen','rata_eigen']);
    //     $bobotKriteria = BobotKriteriaMhs::groupBy('kriteria1')->get(['kriteria1']);

    //     // Mapping tabel bobot kriteria //
    //     $bobot = [];
    //     foreach ($bobotKriteria as $value) {
    //         array_push($bobot, BobotKriteriaMhs::leftJoin('kriteria as satu', 'bobot_kriteria_mhs.kriteria1', 'satu.kode')
    //                                             ->leftJoin('kriteria as dua', 'bobot_kriteria_mhs.kriteria2', 'dua.kode')
    //                                             ->orderByRaw('bobot_kriteria_mhs.kriteria1 ASC, bobot_kriteria_mhs.kriteria2 ASC')
    //                                             ->where('kriteria1', $value->kriteria1)->get(['satu.nama','kriteria1','kriteria2','bobot','eigen']));
    //     }

    //     $formKriteria = []; $index = 0;
    //     foreach ($kriteria as $value) {
    //         $index++;
    //         foreach ($kriteria as $key => $item) {
    //             $temp = BobotKriteriaMhs::where('mhs_id',Auth::id())->where('kriteria1',$value->kode)->where('kriteria2',$item->kode)->first(['kriteria1','kriteria2','bobot']);
    //             if($key >= $index-1) array_push($formKriteria, [$value->kode, $value->nama, $item->kode, $item->nama, $temp ]);
    //         }
    //     }

    //     // dd($formKriteria);

    //     /////////////////////////////////////

    //     $formSubkriteria = [];

    //     foreach ($kriteria as $data) {
    //         $subkriteria = SubKriteria::orderBy('kode','ASC')->where('kriteria_id', $data->id)->get(['kode','nama']);

    //         $temp = []; $index = 0;
    //         foreach ($subkriteria as $value) {
    //             $index++;
    //             foreach ($subkriteria as $key => $item) {
    //                 if($key >= $index) array_push($temp, [$value->kode, $value->nama, $item->kode, $item->nama]);
    //             }
    //         }

    //         array_push($formSubkriteria, [$data->nama, $temp]);
    //     }

    //     // dd($formSubkriteria);

    //     // dd($bobot);

    //     return view('pages.rekomendasi.create', compact('formKriteria', 'formSubkriteria', 'kriteria','bobot'));
    // }

    public function create()
    {
        $kriteria = Kriteria::orderBy('kode','ASC')->get(['id','nama']);
        
        $daftarKriteria = [];
        foreach ($kriteria as $value) {
            $temp = SubKriteria::where('kriteria_id', $value->id)->orderBy('created_at', 'ASC')->get(['id','nama']);
            array_push($daftarKriteria, ['kriteria_id'=>$value->id, 'nama_kriteria'=>$value->nama, 'subkriteria'=>$temp]);
        }

        //////////////////////////////
        
        $rekomendasi = Rekomendasi::where('user_id',Auth::id())->get();
        $kriteriaUser = KriteriaUser::where('user_id',Auth::id())->get();

        return view('pages.rekomendasi.create', compact('daftarKriteria','rekomendasi','kriteriaUser'));
    }

    // public function storeKriteria(Request $request)
    // {
    //     DB::beginTransaction();

    //     $kriteria = Kriteria::orderBy('kode','ASC')->get(['kode','nama']);

    //     $rules = [];

    //     // START // mengatur rules secara dinamis sesuai dengan daftar kriteria yg ada
    //     $index = 0;
    //     foreach ($kriteria as $value) {
    //         $index++;
    //         foreach ($kriteria as $key => $item) {
    //             if($key >= $index) $rules += ["".$value->kode."#".$item->kode."" => 'required'];
    //         }
    //     }
    //     // END // mengatur rules secara dinamis sesuai dengan daftar kriteria yg ada

    //     $this->validate($request,$rules,['required'=>'Tidak boleh kosong.']);
        
    //     try {    

    //         foreach ($kriteria as $value) {
    //             BobotKriteriaMhs::create([
    //                 'mhs_id' => Auth::id(),
    //                 'kriteria1' => $value->kode,
    //                 'kriteria2' => $value->kode,
    //                 'bobot' => 1
    //             ]);
    //         }

    //         // update nilai bobot sesuai dengan kriteria yg dipilih pada form select pertama
    //         foreach ($request->except("_token") as $key => $value) {
    //             BobotKriteriaMhs::create([
    //                 'mhs_id' => Auth::id(),
    //                 'kriteria1' => explode("#",$value)[0],
    //                 'kriteria2' => explode("#",$value)[1],
    //                 'bobot' => explode("#",$value)[2],
    //             ]);

    //             BobotKriteriaMhs::create([
    //                 'mhs_id' => Auth::id(),
    //                 'kriteria1' => explode("#",$value)[1],
    //                 'kriteria2' => explode("#",$value)[0],
    //                 'bobot' => 1/explode("#",$value)[2],
    //             ]);
    //         }

    //         // update nilai bobot sesuai dengan kriteria yg dipilih pada form select kedua
    //         // BobotKriteriaMhs::where('kriteria1', $request->kriteria_2)->where('kriteria2', $request->kriteria_1)->update(['bobot' => 1/$request->nilai]);
            
    //         Alert::success('Berhasil', "Berhasil disimpan");
    //         DB::commit();

    //         return redirect()->route('rekomendasi.create');

    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         Alert::error('Terjadi kesalahan', $th->getMessage());
    //         DB::rollback();
    //         return back();
    //     }
    // }

    public function cari(Request $request)
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

            $alternatif = Perankingan::leftJoin('alternatif', 'perankingan.alternatif_id', 'alternatif.id')->orderBy('nilai','DESC')->get(['perankingan.alternatif_id','alternatif.nama']);
            $rekomendasi = [];
            foreach ($alternatif as $value) {
                $input = array_values($request->except('_token'));
                $check = DetailAlternatif::where('alternatif_id', $value->alternatif_id)->get()->pluck('subkriteria_id')->toArray();

                // jika <= 0, berarti nilai $check sama dengan nilai $input
                if(count(array_diff($input, $check)) <= 0){
                    array_push($rekomendasi, [
                        'id' => Str::uuid()->toString(),
                        'user_id' => Auth::id(),
                        // 'alternatif_id' => $value->alternatif_id,
                        'nama' => $value->nama,
                        'created_at'=>\Carbon\Carbon::now()->toDateTimeString(),
                        'updated_at'=>\Carbon\Carbon::now()->toDateTimeString(),
                    ]);
                }
            }

            // $kriteria = Kriteria::leftJoin('sub_kriteria', 'sub_kriteria.kriteria_id', 'kriteria.id')->get(['kriteria.nama as nama_kriteria', 'sub_kriteria.nama as nama_subkriteria']);
            $kriteriaUser = [];
            foreach ($request->except('_token') as $value) {
                $temp = SubKriteria::where('sub_kriteria.id', $value)->leftJoin('kriteria', 'sub_kriteria.kriteria_id', 'kriteria.id')->first(['kriteria.nama as nama_kriteria', 'sub_kriteria.nama as nama_subkriteria']);
                array_push($kriteriaUser, [
                    'id' => Str::uuid()->toString(),
                    'user_id' => Auth::id(),
                    'kriteria' => $temp->nama_kriteria,
                    'subkriteria' => $temp->nama_subkriteria,
                    'created_at'=>\Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at'=>\Carbon\Carbon::now()->toDateTimeString(),
                ]);
            }
            
            
            // dd($kriteriaUser);
            if(count($rekomendasi) > 0){
                Rekomendasi::where('user_id', Auth::id())->delete();
                Rekomendasi::insert($rekomendasi);

                KriteriaUser::where('user_id', Auth::id())->delete();
                KriteriaUser::insert($kriteriaUser);
                
                Alert::success('Berhasil', "Rekomendasi sesuai kriteria ditemukan");
            }else{
                Alert::warning('Informasi', "Tidak ditemukan sesuai kriteria yang dipilih");
            }
            
            // dd($rekomendasi);

            DB::commit();

            return redirect()->route('rekomendasi.store');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th->getMessage());
            Alert::error('Terjadi kesalahan', $th->getMessage());
            return back();
        }
    }

    public function storeKriteria(Request $request)
    {
        DB::beginTransaction();
        
        try {    

            $request->validate([
                'kriteria_1' => 'required',
                'nilai' => 'required',
                'kriteria_2' => 'required'
            ]);
            
            $daftarKriteria = Kriteria::get(['id','kode']);

            // START // update nilai bobot
            if($request->kriteria_1 === $request->kriteria_2){
                // update nilai bobot sesuai dengan kriteria yg dipilih pada form select pertama
                $check = BobotKriteriaMhs::where('kriteria1', $request->kriteria_1)->where('kriteria2', $request->kriteria_2)->first();

                if($check) $check->update(['bobot' => 1]);
                else {
                    BobotKriteriaMhs::create([
                        'mhs_id' => Auth::id(),
                        'kriteria1' => $request->kriteria_1,
                        'kriteria2' => $request->kriteria_2,
                        'bobot' => 1
                    ]);
                }

            }else{
                // update nilai bobot sesuai dengan kriteria yg dipilih pada form select pertama
                BobotKriteriaMhs::where('kriteria1', $request->kriteria_1)->where('kriteria2', $request->kriteria_2)->update(['bobot' => $request->nilai]);
                
                // update nilai bobot sesuai dengan kriteria yg dipilih pada form select kedua
                BobotKriteriaMhs::where('kriteria1', $request->kriteria_2)->where('kriteria2', $request->kriteria_1)->update(['bobot' => 1/$request->nilai]);
            }
            // END // update nilai bobot
            
            Alert::success('Berhasil', "Berhasil disimpan");
            DB::commit();

            return redirect()->route('rekomendasi.create');

        } catch (\Throwable $th) {
            //throw $th;
            Alert::error('Terjadi kesalahan', $th->getMessage());
            DB::rollback();
            return back();
        }
    }

    public function storeSubkriteria(Request $request)
    {
        dd($request->all());
    }

    public function cetak(){
        $subkriteria = BobotKriteria::leftJoin('kriteria as satu', 'bobot_kriteria.kriteria1', 'satu.kode')
                                        ->leftJoin('kriteria as dua', 'bobot_kriteria.kriteria2', 'dua.kode')
                                        ->where('bobot', '>', 1)
                                        ->orderByRaw('kriteria1 ASC, bobot_kriteria.bobot ASC')
                                        // ->orderBy('kriteria1', 'ASC')
                                        ->get(['satu.nama as kriteria1_nama','dua.nama as kriteria2_nama','bobot_kriteria.bobot']);

        $perankingan = Perankingan::leftJoin('alternatif', 'perankingan.alternatif_id', 'alternatif.id')->orderBy('nilai', 'DESC')->get(['alternatif.nama']);

        //////////////////////////////////////////////////////
        
        $rekomendasi = Rekomendasi::where('user_id',Auth::id())->get();
        $kriteriaUser = KriteriaUser::where('user_id',Auth::id())->get();

        $pdf = PDF::loadView('cetak.rekomendasi', compact('subkriteria','perankingan','rekomendasi','kriteriaUser'))
        ->setpaper('A4', 'potrait');

        return $pdf->stream();
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
