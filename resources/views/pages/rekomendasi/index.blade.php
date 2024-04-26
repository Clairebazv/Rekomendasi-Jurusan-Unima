@extends('layouts.app')

@section('content')

  <section class="section">
    <div class="section-header">
      <h1>Rekomendasi</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="#">Layout</a></div>
        <div class="breadcrumb-item">Rekomendasi</div>
      </div>
    </div>

    <div class="section-body">
      <div class="card">
        <div class="card-header">
          <h4>Informasi Perbandingan Kriteria</h4>
          {{-- <span class="badge badge-secondary" style="border-radius: 5px">Informasi Perbandingan Kriteria</span> --}}
        </div>
        <div class="card-body">
          <div>
            <div class="table-responsive">
              <table style="" class="table table-bordered table-md text-center">
                <tr class="">
                  <th>Kriteria</th>
                  <th>---</th>
                  <th>Kriteria</th>
                </tr>
                @foreach ($subkriteria as $item)       
                  <tr>
                    <td>{{$item->kriteria1_nama}}</td>
                    <td>
                      @if(intval($item->bobot) === 1) Sama penting dengan
                      @elseif(intval($item->bobot) === 2) Mendekati sedikit lebih penting dari
                      @elseif(intval($item->bobot) === 3) Sedikit lebih penting dari
                      @elseif(intval($item->bobot) === 4) Mendekati lebih penting dari
                      @elseif(intval($item->bobot) === 5) Lebih penting dari
                      @elseif(intval($item->bobot) === 6) Mendekati sangat penting dari
                      @elseif(intval($item->bobot) === 7) Sangat penting dari
                      @elseif(intval($item->bobot) === 8) Mendekati mutlak dari
                      @elseif(intval($item->bobot) === 9) Mutlak sangat penting dari
                      @endif
                    </td>
                    <td>{{$item->kriteria2_nama}}</td>
                  </tr>
                @endforeach
              </table>
            </div>
          </div>
        
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h4>Urutan Rekomendasi Jurusan/Program Studi</h4>
        </div>
        <div class="card-body">
          <div>
            <div class="alert alert-light alert-has-icon">
              <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
              <div class="alert-body">
                <div class="alert-title">Informasi</div>
                Daftar perankingan Jurusan/Program Studi dibawah ini dihitung berdasarkan perbandingan kriteria pada tabel diatas.
              </div>
            </div>
            <div class="table-responsive">
              <table style="" class="table table-bordered table-md text-center">
                <tr class="">
                  <th style="width: 50px">No</th>
                  <th>Nama</th>
                </tr>
                <?php 
                  $no = 0;
                ?>
                @foreach ($perankingan as $item)
                <?php $no++; ?>       
                  <tr>
                    <td>{{$no}}</td>
                    <td>{{$item->nama}}</td>
                  </tr>
                @endforeach
              </table>
            </div>

            <hr>

            <h6>Cari Jurusan/Program Studi Berdasarkan Kriteria Tertentu.</h6>
            <a href="{{route('rekomendasi.create')}}" class="btn btn-primary">Pilih Kriteria</a>
          </div>
        
        </div>
      </div>
    </div>
  </section>

@endsection