// validate upload max file size
$('input[name="zip"]').on('change', function () {
    if (this.files[0].size > MAX_SIZE_UPLOAD * 1024) {
        alert('ファイルのサイズが超えました。' + MAX_SIZE_UPLOAD / 1048576 + 'MB 以下に設定してください');
        $('input[name="zip"]').val('');
        return false;
    }
})