<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\BobotKriteria;
use App\Models\SubKriteria;
use App\Models\BobotSubkriteria;
use DB, DataTables, Validator;

class KriteriaController extends Controller
{

    public function index()
    {
        return view('pages.kriteria.index');
    }

    public function create()
    {

    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $rules = [
                'nama' => 'required'
            ];
    
            $messages  = [
                'nama.required' => 'Nama : Tidak boleh kosong.',
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages);
        
            if($validator->fails()) {
                return response()->json(['status'=>'validation error','message'=>$validator->messages()],400);
            }else{  

                $latest = Kriteria::orderBy('created_at', 'DESC')->first();
                $number = !$latest ? 1 : $latest->kode+1;

                Kriteria::create([
                    'kode' => $number,
                    'nama' => $request->nama,
                ]);
    
                $daftarKriteria = Kriteria::get(['kode']);
    
                foreach($daftarKriteria as $value) {
                    foreach($daftarKriteria as $item) {
                        // cek jika sudah ada kriteria yg sama
                        $check = BobotKriteria::where('kriteria1',$value->kode)->where('kriteria2',$item->kode)->first();
                        if(!$check){
                            BobotKriteria::create([
                                'kriteria1' => $value->kode,
                                'kriteria2' => $item->kode,
                                'bobot' => 0
                            ]);
                        }
                    }
                }
    
                DB::commit();
                return response()->json(['status'=>'success', 'message'=>'Berhasil disimpan.'],200);
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['status'=>'failed','message'=>$th->getMessage()],500);
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $rules = [
                'nama' => 'required',
            ];
    
            $messages  = [
                'nama.required' => 'Nama : Tidak boleh kosong.',
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages);
        
            if($validator->fails()) {
                return response()->json(['status'=>'validation error','message'=>$validator->messages()],400);
            }else{               
                $data = Kriteria::where('id',$id)->update([
                    'nama' => $request->nama,
                ]);
    
                DB::commit();
                return response()->json(['status'=>'success', 'message'=>'Berhasil disimpan.'],200);
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['status'=>'failed','message'=>$th->getMessage()],500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $data = Kriteria::find($id);

            BobotKriteria::where('kriteria1',$data->kode)->orWhere('kriteria2',$data->kode)->delete();
            $data->delete();

            SubKriteria::where('kriteria_id',$id)->delete();
            BobotSubkriteria::where('kriteria_id',$id)->delete();

            $this->updatePerhitunganAhp();
            
            DB::commit();
            return response()->json(['status'=>'success', 'message'=>'Berhasil dihapus.'],200);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['status'=>'failed','message'=>$th->getMessage()],500);
        }
    }

    public function updatePerhitunganAhp(){
        $daftarKriteria = Kriteria::get(['id','kode']);
        
        // // START // update jumlah bobot salah satu kriteria
        foreach ($daftarKriteria as $value) {
            $jumlahBobot = BobotKriteria::where('kriteria2', $value->kode)->sum('bobot');
            Kriteria::where('kode', $value->kode)->update([
                'jumlah_bobot' => $jumlahBobot,
            ]);
        }
        // END // update jumlah bobot salah satu kriteria

        // START // update nilai eigen
        foreach ($daftarKriteria as $value) {
            $daftarBobotKriteria = BobotKriteria::where('kriteria2', $value->kode)->get();
            $jumlahBobot = BobotKriteria::where('kriteria2', $value->kode)->sum('bobot');
            foreach ($daftarBobotKriteria as $value) {
                $temp = BobotKriteria::where('id',$value->id)->first();
                // dd($temp->bobot,$jumlahBobot);
                if($temp->bobot > 0) BobotKriteria::where('id',$value->id)->update(['eigen' => $temp->bobot/$jumlahBobot]);
            }
        }
        // END // update nilai eigen

        // START // update jumlah dan rata-rata eigen
        foreach ($daftarKriteria as $value) {
            $jumlahEigen = BobotKriteria::where('kriteria1', $value->kode)->sum('eigen');
            Kriteria::where('kode', $value->kode)->update([
                'jumlah_eigen' => $jumlahEigen,
                'rata_eigen' => $jumlahEigen/sizeof($daftarKriteria)
            ]);
        }
        // END // update jumlah  dan rata-rata eigen
    }

    public function datatable(){
        $data = DB::table('kriteria')->orderBy('created_at', 'ASC')
        ->select(
            'kriteria.id',
            'kriteria.kode',
            'kriteria.nama',
        )->get();

        return Datatables::of($data)->addIndexColumn()
            ->addIndexColumn()
            ->addColumn('kode', function ($data) {
                return "K-".$data->kode."";
            })
            ->addColumn('nama', function ($data) {
                return $data->nama;
            })
            ->addColumn('action', function($data){
                return '
                    <a href="javascript:void(0)" data-toggle="tooltip" 
                        data-id="'.$data->id.'" 
                        data-nama="'.$data->nama.'" 
                        data-original-title="Edit" class="edit btn btn-primary btn-sm show-edit-modal"><i class="fas fa-edit"></i></a>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Delete" class="btn btn-danger btn-sm show-delete-modal"><i class="fas fa-trash-alt"></i></a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

}
