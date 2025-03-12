<x-modal data-backdrop="static" data-keyboard="false" size="modal-lg">
    <x-slot name="title">
        Tambah
    </x-slot>

    @method('POST')

    <div class="row">
        <div class="col-lg-4 col-md-4 col-4">
            <div class="form-group">
                <label for="nama_guru">Nama Lengkap <span class="text-danger">*</span></label>
                <input id="nama_guru" class="form-control" type="text" name="nama_guru"
                    placeholder="Masukkan nama guru" autocomplete="off">
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-4">
            <div class="form-group">
                <label for="gelar_depan">Gelar Depan</label>
                <input id="gelar_depan" class="form-control" type="text" name="gelar_depan" autocomplete="off">
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-4">
            <div class="form-group">
                <label for="gelar_belakang">Gelar Belakang <span class="text-danger">*</span></label>
                <input id="gelar_belakang" class="form-control" type="text" name="gelar_belakang" autocomplete="off">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-6">
            <div class="form-group">
                <label for="nip_guru">NIP <span class="text-danger">*</span></label>
                <input id="nip_guru" class="form-control" type="text" name="nip_guru"
                    placeholder="Masukkan nip guru" autocomplete="off">
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-6">
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                    <option disabled selected>Pilih salah satu</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-6">
            <div class="form-group">
                <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                <input id="tempat_lahir" class="form-control" type="text" name="tempat_lahir" autocomplete="off">
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-6">
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                <div class="input-group datepicker" id="tanggal_lahir" data-target-input="nearest">
                    <input type="text" name="tanggal_lahir" class="form-control datetimepicker-input"
                        data-target="#tanggal_lahir" data-toggle="datetimepicker" autocomplete="off" />
                    <div class="input-group-append" data-target="#tanggal_lahir" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input id="email" class="form-control" type="text" name="email" autocomplete="off">
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group">
                <label for="username">Username <span class="text-danger">*</span></label>
                <input id="username" class="form-control" type="text" name="username" autocomplete="off">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group">
                <label for="foto">Unggah Foto Guru</label>
                <input type="file" id="foto" class="form-control" name="foto" accept="image/*">
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <button type="button" onclick="submitForm(this.form)" class="btn btn-sm btn-outline-success"
            id="submitBtn">
            <span id="spinner-border" class="spinner-border spinner-border-sm" role="status"
                aria-hidden="true"></span>
            <i class="fas fa-save mr-1"></i>
            Simpan</button>
        <button type="button" data-dismiss="modal" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-times"></i>
            Close
        </button>
    </x-slot>
</x-modal>
