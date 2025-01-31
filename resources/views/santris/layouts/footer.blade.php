<footer class="main-footer">
    <div class="footer-left">
        Copyright &copy; {{ date('Y') }}
        <div class="bullet"></div> Allumqniyyah | Made by <a href="https://github.com/wahdie" target="_blank">PSBSB LQ</a></a>
    </div>
    <div class="footer-right">
        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
    </div>
</footer>
</div>
</div>

@yield('modal')
<script>
    // Inisialisasi DataTables untuk semua tabel
    $(document).ready(function() {
        $('table').DataTable({
            paging: true, // Menambahkan pagination
            searching: true, // Menambahkan fitur pencarian
            lengthChange: false, // Menonaktifkan perubahan jumlah data per halaman
            pageLength: 10, // Menentukan jumlah data per halaman
            order: [[0, 'asc']], // Mengurutkan berdasarkan kolom pertama
            responsive: true, // Membuat tabel responsif
            autoWidth: false // Menonaktifkan auto width
        });
    });

</script>
<!-- General JS Scripts -->
<script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
<script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
<script src="{{ asset('assets/modules/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/stisla.js') }}"></script>
<script src="{{ asset('assets/js/digital-sign.js') }}"></script>
<script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js') }}"></script>
<script src="{{ asset('assets/modules/jquery-ui/jquery-ui.min.js') }}"></script>

<!-- JS Libraies -->
<script src="{{ asset('assets/js/page/modules-datatables.js') }}"></script>

<!-- Data Tables -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- Page Specific JS File -->
@yield('script')

<script>
    $('.alert').alert()
</script>

<!-- Template JS File -->
<script src="{{ asset('assets/js/scripts.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
</body>

</html>
