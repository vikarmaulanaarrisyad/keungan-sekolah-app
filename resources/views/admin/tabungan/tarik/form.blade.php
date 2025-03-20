<x-modal data-backdrop="static" data-keyboard="false" size="modal-md">
    <x-slot name="title">
        Tambah
    </x-slot>

    @method('POST')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
            <div class="form-group">
                <label for="rombel_id">Rombel Kelas <span class="text-danger">*</span></label>
                <select name="rombel_id" id="rombel_id" class="form-control">
                    <option disabled selected>Pilih salah satu</option>
                    @foreach ($rombels as $rombel)
                        <option value="{{ $rombel->id }}">{{ $rombel->kelas->nama_kelas }} {{ $rombel->nama_rombel }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <input type="hidden" name="type" value="tarik">

    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
            <div class="form-group">
                <label for="siswa_id">Nama Siswa <span class="text-danger">*</span></label>
                <select name="siswa_id" id="siswa_id" class="form-control" disabled>
                    <option disabled selected>Pilih Rombel terlebih dahulu</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
            <div class="form-group">
                <label for="saldo">Saldo Terakhir<span class="text-danger">*</span></label>
                <input id="saldo" class="form-control" type="text" name="saldo" disabled>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
            <div class="form-group">
                <label for="jumlah">Jumlah<span class="text-danger">*</span></label>
                <input id="jumlah" class="form-control" type="text" name="jumlah" onkeyup="format_uang(this)">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
            <div class="form-group">
                <label for="keterangan">Keterangan<span class="text-danger">*</span></label>
                <textarea name="keterangan" id="keterangan" cols="5" rows="5" class="form-control"></textarea>
            </div>
        </div>
    </div>


    <x-slot name="footer">
        <button type="button" onclick="submitForm(this.form)" class="btn btn-sm btn-outline-success" id="submitBtn">
            <span id="spinner-border" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <i class="fas fa-save mr-1"></i>
            Simpan</button>
        <button type="button" data-dismiss="modal" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-times"></i>
            Close
        </button>
    </x-slot>
</x-modal>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#rombel_id').on('change', function() {
                let rombelId = $(this).val();
                let siswaSelect = $('#siswa_id');

                // Kosongkan dan disabled dropdown siswa saat fetching
                siswaSelect.prop('disabled', true).html('<option disabled selected>Loading...</option>');

                // Tampilkan loading dengan SweetAlert2
                Swal.fire({
                    title: 'Mohon Tunggu',
                    text: 'Mengambil data siswa...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Panggil AJAX untuk mengambil data siswa berdasarkan rombel_id
                $.ajax({
                    url: `/admin/get-siswa/${rombelId}`, // Pastikan route ini ada di backend
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        siswaSelect.html('<option disabled selected>Pilih Siswa</option>');
                        $.each(response, function(key, siswa) {
                            siswaSelect.append(
                                `<option value="${siswa.id}">${siswa.nama_siswa}</option>`
                            );
                            // Perbaikan penulisan template literal
                            $('#saldo').val(`${siswa.saldo}`);
                        });

                        // Enable dropdown setelah data tersedia
                        siswaSelect.prop('disabled', false);

                        // Tutup Swal Loading
                        Swal.close();
                    },
                    error: function() {
                        siswaSelect.html(
                            '<option disabled selected>Gagal mengambil data</option>');
                        Swal.fire('Error', 'Gagal mengambil data siswa', 'error');
                    }
                });
            });
        });
    </script>
@endpush
