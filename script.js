document.addEventListener("DOMContentLoaded", function() {
    const switchers = document.querySelectorAll('.switcher');

    switchers.forEach(switcher => {
        switcher.addEventListener('click', function(event) {
            event.preventDefault(); // Mencegah navigasi jika menggunakan <a> sebagai switcher

            // Hapus class 'is-active' dari semua wrapper
            document.querySelectorAll('.form-wrapper').forEach(wrapper => {
                wrapper.classList.remove('is-active');
            });

            // Tambahkan class 'is-active' ke form yang sesuai
            const target = this.getAttribute('data-target'); // Pastikan elemen memiliki atribut data-target
            document.querySelector(`.${target}`).classList.add('is-active');
        });
    });
});
