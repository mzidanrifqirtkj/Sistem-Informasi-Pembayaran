<!-- resources/views/biaya_santri/create.blade.php -->
@extends('layouts.home')
@section('title_page', 'Tambah Biaya Santri')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Biaya Santri</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('biaya-santris.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="santri_id">Pilih Santri</label>
                    <select class="form-control select2" name="santri_id" id="santri_id" required>
                        <option value="">-- Pilih Santri --</option>
                        @foreach ($santris as $santri)
                            <option value="{{ $santri->id_santri }}">{{ $santri->nama_santri }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Pilih Biaya</label>
                    <div class="position-relative">
                        <input type="text" class="form-control" id="biaya_search" placeholder="Cari biaya...">
                        <div id="biaya_dropdown" class="dropdown-menu w-100"
                            style="display: none; max-height: 200px; overflow-y: auto;"></div>
                    </div>

                    <div class="selected-biaya mt-3">
                        <h6>Biaya Terpilih:</h6>
                        <div id="selected_biaya_list"></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('biaya-santris.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            let selectedBiayaIds = []; // Track selected biaya IDs
            let allBiayas = []; // Store all biaya data

            // Load all biaya data on page load
            loadAllBiayas();

            function loadAllBiayas() {
                $.get("{{ route('biaya-santris.search-biaya') }}", {
                    q: ''
                }, function(data) {
                    allBiayas = data;
                });
            }

            // Biaya search with dropdown
            $('#biaya_search').on('focus input', function() {
                let query = $(this).val().toLowerCase();
                showBiayaDropdown(query);
            });

            // Hide dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#biaya_search, #biaya_dropdown').length) {
                    $('#biaya_dropdown').hide();
                }
            });

            function showBiayaDropdown(query = '') {
                // Filter biaya based on query and exclude selected ones
                let filteredBiayas = allBiayas.filter(biaya => {
                    let matchesQuery = query === '' ||
                        biaya.kategori_biaya.nama_kategori.toLowerCase().includes(query);
                    let notSelected = !selectedBiayaIds.includes(biaya.id_daftar_biaya);
                    return matchesQuery && notSelected;
                });

                if (filteredBiayas.length === 0) {
                    $('#biaya_dropdown').hide();
                    return;
                }

                let html = '';
                filteredBiayas.forEach(biaya => {
                    html += `
                        <a href="#" class="dropdown-item biaya-dropdown-item"
                           data-id="${biaya.id_daftar_biaya}"
                           data-nama="${biaya.kategori_biaya.nama_kategori}"
                           data-nominal="${biaya.nominal}">
                            ${biaya.kategori_biaya.nama_kategori}
                        </a>
                    `;
                });

                $('#biaya_dropdown').html(html).show();
            }

            // Handle biaya selection from dropdown
            $(document).on('click', '.biaya-dropdown-item', function(e) {
                e.preventDefault();

                let id = $(this).data('id');
                let nama = $(this).data('nama');
                let nominal = $(this).data('nominal');

                // Add to selected biaya
                addSelectedBiaya(id, nama, nominal);

                // Clear search input and hide dropdown
                $('#biaya_search').val('');
                $('#biaya_dropdown').hide();
            });

            function addSelectedBiaya(id, nama, nominal) {
                // Add to selected IDs array
                selectedBiayaIds.push(id);

                let html = `
                    <div class="card mb-2" id="selected_biaya_${id}">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-1">${nama}</h6>
                                    <p class="mb-0 text-muted">Rp ${parseInt(nominal).toLocaleString('id-ID')}</p>
                                    <input type="hidden" name="biaya[${id}][id]" value="${id}">
                                </div>
                                <div class="col-md-4">
                                    <label class="small">Jumlah:</label>
                                    <input type="number" name="biaya[${id}][jumlah]" class="form-control" value="1" min="1">
                                </div>
                                <div class="col-md-2 text-right">
                                    <button type="button" class="btn btn-danger btn-sm remove-biaya" data-id="${id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('#selected_biaya_list').append(html);
            }

            // Remove selected biaya
            $(document).on('click', '.remove-biaya', function() {
                let id = parseInt($(this).data('id'));

                // Remove from selected IDs array
                selectedBiayaIds = selectedBiayaIds.filter(selectedId => selectedId !== id);

                // Remove card
                $(`#selected_biaya_${id}`).remove();

                // Refresh dropdown if search is active
                if ($('#biaya_search').is(':focus')) {
                    showBiayaDropdown($('#biaya_search').val().toLowerCase());
                }
            });

            // Enhanced form validation
            $('form').on('submit', function(e) {
                if (selectedBiayaIds.length === 0) {
                    e.preventDefault();
                    alert('Pilih minimal satu biaya untuk santri');
                    return false;
                }
            });
        });
    </script>
@endsection

@section('css_inline')
    <style>
        /* Enhanced dropdown styling */
        #biaya_dropdown {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1000;
            display: block;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        #biaya_dropdown.show {
            display: block !important;
        }

        .biaya-dropdown-item {
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #f8f9fa;
            transition: background-color 0.15s ease-in-out;
        }

        .biaya-dropdown-item:hover {
            background-color: #f8f9fa;
            text-decoration: none;
        }

        .biaya-dropdown-item:last-child {
            border-bottom: none;
        }

        /* Selected biaya cards styling */
        #selected_biaya_list .card {
            border-left: 3px solid #007bff;
            transition: all 0.3s ease;
        }

        #selected_biaya_list .card:hover {
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.15);
        }

        /* Input focus enhancement */
        #biaya_search:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Remove button styling */
        .remove-biaya {
            border-radius: 50%;
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-biaya:hover {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }

        /* Form enhancements */
        .selected-biaya h6 {
            color: #495057;
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #selected_biaya_list .row {
                flex-direction: column;
            }

            #selected_biaya_list .col-md-2 {
                text-align: left !important;
                margin-top: 10px;
            }
        }
    </style>
@endsection
