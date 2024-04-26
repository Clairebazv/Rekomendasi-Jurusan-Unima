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
      {{-- <h2 class="section-title">This is Example Page</h2>
      <p class="section-lead">This page is just an example for you to create your own page.</p> --}}
      <div class="card">
        <div class="card-header">
          <h4>Cari Jurusan / Program Studi Sesuai Kriteria</h4>
        </div>

        <div class="card-body">
          <div>
            <span class="badge badge-info" style="border-radius: 5px">Pilih kriteria yang diinginkan</span>
          </div>
          <br>
          <form method="POST" action="{{ route('rekomendasi.store') }}">
            @csrf
            @foreach ($daftarKriteria as $key => $value)                                    
                <div class="form-group">
                    <label>{{ $value['nama_kriteria'] }}</label>
                    <select name={{$value['kriteria_id']}} class="custom-select">
                      <option value="">Pilih</option>
                      @foreach ($value['subkriteria'] as $item)
                        <option value={{$item->id}} {{ old($value['kriteria_id']) == $item->id ? 'selected' : '' }}>{{$item->nama}}</option>
                      @endforeach
                    </select>
                    @if ($errors->has($value['kriteria_id']))
                        <label class="" style="color: red">
                            {{ $errors->first($value['kriteria_id']) }}
                        </label>
                    @endif
                </div>
            @endforeach
            <div class="text-right">
                <button type="submit" class="btn btn-primary">Proses</button>
            </div>
          </form>
        </div>

      </div>

      <div class="card">
        <div class="card-header">
          <h4>Daftar Jurusan/Program Studi Sesuai Kriteria</h4>
        </div>
        <div class="card-body">
          @if(count($rekomendasi) <=0)              
            <div class="alert alert-light text-center">
              Tidak tersedia.
            </div>
          @else
            <div>
              @foreach ($kriteriaUser as $item)
                <span class="badge badge-light" style="border-radius: 5px">{{$item->kriteria}} : {{$item->subkriteria}}</span>
              @endforeach
            </div>
            <br>
            <table style="" class="table table-bordered table-md text-center">
              <tr class="">
                <th style="width: 50px">No</th>
                <th>Nama</th>
              </tr>
              <?php 
                $no = 0;
              ?>
              @foreach ($rekomendasi as $item)
              <?php $no++; ?>       
                <tr>
                  <td>{{$no}}</td>
                  <td>{{$item->nama}}</td>
                </tr>
              @endforeach
            </table>
          @endif

          <a href="{{route('cetak')}}" class="btn btn-primary" target="blink">Cetak</a>

        </div>
      </div>
    </div>
  </section>

@endsection