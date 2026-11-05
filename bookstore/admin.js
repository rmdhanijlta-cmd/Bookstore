/* Filter cepat di sisi browser: <input data-filter="#daftar"> menyaring elemen [data-cari] di dalam #daftar */
document.querySelectorAll('input[data-filter]').forEach(function (input) {
    var target = document.querySelector(input.dataset.filter);
    if (!target) return;

    input.addEventListener('input', function () {
        var q = input.value.trim().toLowerCase();
        target.querySelectorAll('[data-cari]').forEach(function (el) {
            el.style.display = el.dataset.cari.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
        });
    });
});

/* Chat: langsung scroll ke pesan terbaru */
var pesan = document.querySelector('.messages');
if (pesan) pesan.scrollTop = pesan.scrollHeight;
