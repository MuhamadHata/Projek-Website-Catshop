
$(function() {
    console.log("JS Loaded");

    $('.tampilModalTambah').on('click', function() {
        $('#judulModal').html('Tambah Mahasiswa');
        $('.modal form').attr('action', BASEURL + '/mahasiswa/tambah');
    });

    $(document).on('click', '.tampilModalUbah', function () {
        $('#judulModal').html('Ubah Mahasiswa');
        $('.modal-footer button[type=submit]').html('Ubah Data');
        $('.modal-body form').attr('action', BASEURL + '/mahasiswa/ubah');

        const id = $(this).data('id');

        $.ajax({
            url: BASEURL + '/mahasiswa/getubah',
            data: { id: id },
            method: 'post',
            dataType: 'json',
            success: function(data) {
                console.log(BASEURL)
                $('#nama').val(data.nama);
                $('#nim').val(data.nim);
                $('#id').val(data.id);
                $('.modal form').attr('action', BASEURL + '/mahasiswa/ubah/' + id);
            }
        });
    });
});
