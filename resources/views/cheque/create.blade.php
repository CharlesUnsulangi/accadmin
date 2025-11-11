@extends('layouts.bootstrap')

@section('title', 'Buat Buku Cheque Baru')

@section('content')
<div class="container-fluid py-4" x-data="createChequeBook()">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-plus-circle text-primary me-2"></i>
                Buat Buku Cheque Baru
            </h2>
            <p class="text-muted mb-0">Input data buku cheque dan generate laporan</p>
        </div>
        <div>
            <a href="{{ route('cheque.management') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="step" :class="step >= 1 ? 'active' : ''">
                        <div class="step-icon mb-2">
                            <i class="fas fa-edit fa-2x" :class="step >= 1 ? 'text-primary' : 'text-muted'"></i>
                        </div>
                        <h6 :class="step >= 1 ? 'text-primary' : 'text-muted'">1. Input Data</h6>
                        <small class="text-muted">Isi informasi buku cheque</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step" :class="step >= 2 ? 'active' : ''">
                        <div class="step-icon mb-2">
                            <i class="fas fa-eye fa-2x" :class="step >= 2 ? 'text-primary' : 'text-muted'"></i>
                        </div>
                        <h6 :class="step >= 2 ? 'text-primary' : 'text-muted'">2. Preview</h6>
                        <small class="text-muted">Cek data sebelum disimpan</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step" :class="step >= 3 ? 'active' : ''">
                        <div class="step-icon mb-2">
                            <i class="fas fa-file-pdf fa-2x" :class="step >= 3 ? 'text-primary' : 'text-muted'"></i>
                        </div>
                        <h6 :class="step >= 3 ? 'text-primary' : 'text-muted'">3. Laporan</h6>
                        <small class="text-muted">Generate & print laporan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 1: Form Input -->
    <div x-show="step === 1" class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Informasi Buku Cheque</h5>
        </div>
        <div class="card-body">
            <form @submit.prevent="goToPreview">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kode Buku <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" x-model="form.cheque_code_h" required
                               placeholder="Misal: CHQ-2025-001">
                        <small class="text-muted">Format: CHQ-YYYY-XXX</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Deskripsi</label>
                        <input type="text" class="form-control" x-model="form.cheque_desc"
                               placeholder="Deskripsi buku cheque">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Bank <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="form.cheque_bank" required>
                            <option value="">-- Pilih Bank --</option>
                            <template x-for="bank in bankList" :key="bank.code">
                                <option :value="bank.name" x-text="bank.name"></option>
                            </template>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Rekening <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" x-model="form.cheque_rek" required
                               placeholder="Nomor rekening">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Cabang Bank</label>
                        <input type="text" class="form-control" x-model="form.cheque_cabang"
                               placeholder="Nama cabang">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kode COA</label>
                        <select class="form-select" x-model="form.cheque_coacode">
                            <option value="">-- Pilih COA --</option>
                            <template x-for="coa in coaList" :key="coa.code">
                                <option :value="coa.code" x-text="coa.label"></option>
                            </template>
                        </select>
                        <small class="text-muted">COA terpilih: <strong x-text="form.cheque_coacode || '-'"></strong></small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipe Cheque</label>
                        <select class="form-select" x-model="form.cheque_type">
                            <option value="">Pilih Tipe</option>
                            <option value="REGULAR">Regular</option>
                            <option value="CASHIER">Cashier</option>
                            <option value="TRAVELER">Traveler</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nomor Awal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" x-model="form.cheque_startno" required
                               min="1" placeholder="Misal: 1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nomor Akhir <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" x-model="form.cheque_endno" required
                               min="1" placeholder="Misal: 50">
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Info:</strong> Total <span x-text="totalCheques"></span> lembar cek akan di-generate
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-arrow-right me-2"></i>Lanjut ke Preview
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Step 2: Preview -->
    <div x-show="step === 2" class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview Data Buku Cheque</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">Informasi Buku</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><strong>Kode Buku:</strong></td>
                                    <td x-text="form.cheque_code_h"></td>
                                </tr>
                                <tr>
                                    <td><strong>Deskripsi:</strong></td>
                                    <td x-text="form.cheque_desc || '-'"></td>
                                </tr>
                                <tr>
                                    <td><strong>Bank:</strong></td>
                                    <td x-text="form.cheque_bank"></td>
                                </tr>
                                <tr>
                                    <td><strong>Rekening:</strong></td>
                                    <td x-text="form.cheque_rek"></td>
                                </tr>
                                <tr>
                                    <td><strong>Cabang:</strong></td>
                                    <td x-text="form.cheque_cabang || '-'"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">Detail Cheque</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><strong>Kode COA:</strong></td>
                                    <td x-text="form.cheque_coacode || '-'"></td>
                                </tr>
                                <tr>
                                    <td><strong>Tipe:</strong></td>
                                    <td x-text="form.cheque_type || '-'"></td>
                                </tr>
                                <tr>
                                    <td><strong>Nomor Awal:</strong></td>
                                    <td x-text="form.cheque_startno"></td>
                                </tr>
                                <tr>
                                    <td><strong>Nomor Akhir:</strong></td>
                                    <td x-text="form.cheque_endno"></td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>Total Lembar:</strong></td>
                                    <td><strong x-text="totalCheques + ' lembar'"></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Perhatian:</strong> Pastikan semua data sudah benar sebelum menyimpan.
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <button @click="step = 1" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Form
                </button>
                <button @click="saveChequeBook" class="btn btn-success" :disabled="saving">
                    <i class="fas fa-save me-2"></i>
                    <span x-text="saving ? 'Menyimpan...' : 'Simpan & Generate Laporan'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Step 3: Laporan -->
    <div x-show="step === 3">
        <!-- Success Message -->
        <div class="alert alert-success shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-3x me-3"></i>
                <div>
                    <h5 class="mb-1">Buku Cheque Berhasil Dibuat!</h5>
                    <p class="mb-0">Kode: <strong x-text="form.cheque_code_h"></strong> | 
                       Total: <strong x-text="totalCheques + ' lembar cek'"></strong></p>
                </div>
            </div>
        </div>

        <!-- Laporan Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-file-pdf me-2"></i>Laporan Buku Cheque</h5>
            </div>
            <div class="card-body">
                <!-- Print Preview Area -->
                <div id="printArea" class="border p-4 bg-white">
                    <div class="text-center mb-4">
                        <h4 class="mb-0">LAPORAN BUKU CHEQUE</h4>
                        <p class="text-muted">Tanggal: <span x-text="currentDate"></span></p>
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <th width="30%" class="bg-light">Kode Buku</th>
                            <td x-text="form.cheque_code_h"></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Deskripsi</th>
                            <td x-text="form.cheque_desc || '-'"></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Bank</th>
                            <td x-text="form.cheque_bank"></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Nomor Rekening</th>
                            <td x-text="form.cheque_rek"></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Cabang</th>
                            <td x-text="form.cheque_cabang || '-'"></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Kode COA</th>
                            <td x-text="form.cheque_coacode || '-'"></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Tipe Cheque</th>
                            <td x-text="form.cheque_type || '-'"></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Range Nomor</th>
                            <td><span x-text="form.cheque_startno"></span> s/d <span x-text="form.cheque_endno"></span></td>
                        </tr>
                        <tr class="table-success">
                            <th class="bg-light">Total Lembar</th>
                            <td><strong x-text="totalCheques + ' lembar'"></strong></td>
                        </tr>
                    </table>

                    <!-- Signature Area -->
                    <div class="row mt-5 pt-4">
                        <div class="col-6 text-center">
                            <p class="mb-5">Dibuat Oleh,</p>
                            <p class="border-top d-inline-block px-5 pt-2">
                                <strong>{{ Auth::user()->name }}</strong>
                            </p>
                        </div>
                        <div class="col-6 text-center">
                            <p class="mb-5">Disetujui Oleh,</p>
                            <p class="border-top d-inline-block px-5 pt-2">
                                <strong>(...................................)</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 d-flex justify-content-between">
                    <div class="btn-group">
                        <button @click="printReport" class="btn btn-primary">
                            <i class="fas fa-print me-2"></i>Print Laporan
                        </button>
                        <button @click="downloadPDF" class="btn btn-outline-primary">
                            <i class="fas fa-download me-2"></i>Download PDF
                        </button>
                    </div>
                    <div class="btn-group">
                        <button @click="createAnother" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Buat Buku Baru
                        </button>
                        <a href="{{ route('cheque.management') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Lihat Semua Buku
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function createChequeBook() {
    return {
        step: 1,
        saving: false,
        form: {
            cheque_code_h: '',
            cheque_desc: '',
            cheque_bank: '',
            cheque_rek: '',
            cheque_cabang: '',
            cheque_coacode: '',
            cheque_type: '',
            cheque_startno: 1,
            cheque_endno: 50
        },
        currentDate: '',
        
        // Master Data
        coaList: @json($coaList),
        bankList: @json($bankList),

        get totalCheques() {
            const start = parseInt(this.form.cheque_startno) || 0;
            const end = parseInt(this.form.cheque_endno) || 0;
            return end >= start ? (end - start + 1) : 0;
        },

        init() {
            this.currentDate = new Date().toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        },

        goToPreview() {
            if (this.totalCheques <= 0) {
                alert('Nomor akhir harus lebih besar atau sama dengan nomor awal!');
                return;
            }
            this.step = 2;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        async saveChequeBook() {
            this.saving = true;
            
            try {
                const response = await fetch('/api/cheque/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                const result = await response.json();

                if (result.success) {
                    this.step = 3;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    alert('Error: ' + (result.message || 'Gagal menyimpan buku cheque'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            } finally {
                this.saving = false;
            }
        },

        printReport() {
            const printContent = document.getElementById('printArea').innerHTML;
            const originalContent = document.body.innerHTML;
            
            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        },

        downloadPDF() {
            alert('Fitur download PDF akan segera tersedia');
            // TODO: Implement PDF generation
        },

        createAnother() {
            // Reset form
            this.form = {
                cheque_code_h: '',
                cheque_desc: '',
                cheque_bank: '',
                cheque_rek: '',
                cheque_cabang: '',
                cheque_coacode: '',
                cheque_type: '',
                cheque_startno: 1,
                cheque_endno: 50
            };
            this.step = 1;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printArea, #printArea * {
        visibility: visible;
    }
    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}

.step.active .step-icon {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}
</style>
@endsection
