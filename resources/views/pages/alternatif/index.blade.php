@extends('layouts.app')

@section('content')

  <section class="section">
    <div class="section-header">
      <h1>Daftar Alternatif</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="#">Layout</a></div>
        <div class="breadcrumb-item">Daftar Alternatif</div>
      </div>
    </div>

    <div class="section-body">
      {{-- <h2 class="section-title">This is Example Page</h2>
      <p class="section-lead">This page is just an example for you to create your own page.</p> --}}
      <div class="card">
        <div class="card-header">
          <a href="{{ route('alternatif.create',request()->segment(3)) }}" class="btn btn-primary">
            Tambah <i class="fas fa-plus-square"></i>
          </a>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table id="datatable" class="table table-striped table-md text-center">
              <thead>
                  <tr>
                    <th>No</th>
                    <th>Jurusan/Prodi</th>
                    @foreach ($kriteria as $item)    
                      <th>{{$item->nama}}</th>
                    @endforeach
                    <th>Aksi</th>
                  </tr>
              </thead>
              <tbody class="">
                <?php $no=0; ?>
                @foreach ($daftarAlternatif as $value)
                  <?php $no++; ?>      
                  <tr>
                    <th>{{$no}}</th>
                    <th>{{$value['nama_alternatif']}}</th>
                    @foreach ($value['subkriteria'] as $item)
                      <th>{{$item->nama_subkriteria}}</th>
                    @endforeach
                    <th>
                      <a href="{{ route('alternatif.edit',$value['id']) }}" class="edit btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                      <a href="javascript:void(0)" data-alternatif={{$value['id']}} class="btn btn-danger btn-sm show-delete-modal"><i class="fas fa-trash-alt"></i></a>
                    </th>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Modal Delete Alternatif -->
  <div class="modal fade" id="deleteDataModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Hapus Data Alternatif</h5>
        </div>
        <div class="modal-body">
          <input type="hidden" id="deleteDataId">
          Anda yakin akan menghapus?
        </div>
        <div class="modal-footer">
          <button type="button" id="closeDeleteBtn" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
          <button type="button" id="confirmDeleteBtn" class="btn btn-primary">Ya</button>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    $(function () {

      /*------------------------------------------ Render DataTable --------------------------------------------*/ 
      let table = $('#datatable').DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        autoWidth: false,
        columnDefs: [
          {"className": "dt-center", "targets": "_all"}
        ]
      });

      // /*------------------------------------------ Show modal delete Alternatif --------------------------------------------*/ 
      $(document).on('click', '.show-delete-modal', function () {
        $('#deleteDataModal').modal('show');
        $('#deleteDataId').val($(this).data("alternatif"));
      });

      // /*------------------------------------------ Delete data Alternatif --------------------------------------------*/ 
      $('#confirmDeleteBtn').click(function (e) {
        $(this).html('Menghapus...');

        let dataId = $('#deleteDataId').val();
        let url = '{{ route('alternatif.destroy', ':id') }}'; url = url.replace(':id', dataId);

        // disable button while deleting
        $("#confirmDeleteBtn").prop("disabled",true); 
        $("#closeDeleteBtn").prop("disabled",true);

        $.ajax({
          type: "DELETE",
          url : url,
          success: function (data) {
            $('#deleteDataModal').modal('hide');
            location.reload();
          },
          error: function (data) {
            const { status, message } = data.responseJSON;
            Swal.fire({
              title: 'Terjadi kesalahan',
              text: message,
              icon: 'error',
              confirmButtonText: 'OK'
            })
          },
          complete: function(data) {
            $('#confirmDeleteBtn').html('Ya'); 

            // enable button
            $("#confirmDeleteBtn").prop("disabled",false);
            $("#closeDeleteBtn").prop("disabled",false);
          }
        });
      });

    });
  </script>
@endpush