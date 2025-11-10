<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-table text-primary me-2"></i>
                Database Tables
            </h2>
            <p class="text-muted mb-0">Browse and explore database table structures</p>
        </div>
        
        <!-- Database Selector -->
        <div style="min-width: 250px;">
            <label class="form-label small text-muted mb-1">
                <i class="fas fa-database me-1"></i>Database Connection
            </label>
            <select wire:model.live="selectedConnection" class="form-select form-select-lg">
                @foreach($availableConnections as $connName => $dbName)
                    <option value="{{ $connName }}">{{ $dbName }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Search Tables</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" 
                           placeholder="Search by table name...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Per Page</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="$set('search', '')" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo me-2"></i>Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="alert alert-info mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle me-2"></i>
                <strong>Total Tables:</strong> {{ number_format($total) }}
            </div>
            <div class="text-end small">
                <i class="fas fa-database text-muted me-1"></i>
                <strong>{{ $availableConnections[$selectedConnection] ?? $selectedConnection }}</strong>
            </div>
        </div>
    </div>

    <!-- Tables List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Schema</th>
                            <th>Table Name</th>
                            <th>Type</th>
                            <th class="text-end">Row Count</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tables as $index => $table)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td><span class="badge bg-secondary">{{ $table->TABLE_SCHEMA }}</span></td>
                                <td>
                                    <code class="text-primary">{{ $table->TABLE_NAME }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $table->TABLE_TYPE }}</span>
                                </td>
                                <td class="text-end">
                                    <strong>{{ number_format($table->row_count ?? 0) }}</strong>
                                </td>
                                <td class="text-center">
                                    <button wire:click="viewTableSchema('{{ $table->TABLE_NAME }}')" 
                                            class="btn btn-sm btn-outline-primary"
                                            title="View Schema">
                                        <i class="fas fa-eye"></i> Schema
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-inbox fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No tables found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $tables instanceof \Illuminate\Pagination\LengthAwarePaginator ? $tables->links() : '' }}
            </div>
        </div>
    </div>

    <!-- Table Schema Modal -->
    @if($selectedTable)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-database me-2"></i>
                            Table Schema: <code class="text-white">{{ $selectedTable }}</code>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeTableSchema"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Flash Message -->
                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Table Info -->
                        @if($tableInfo)
                        <div class="alert alert-info mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Table Name:</strong> {{ $tableInfo->TABLE_NAME }}
                                </div>
                                <div class="col-md-2">
                                    <strong>Type:</strong> {{ $tableInfo->TABLE_TYPE }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Row Count:</strong> {{ number_format($tableInfo->row_count ?? 0) }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Last Record:</strong> 
                                    @if($lastRecordDate)
                                        <span class="badge bg-success">{{ \Carbon\Carbon::parse($lastRecordDate)->format('d M Y H:i') }}</span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Table Note Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-warning bg-opacity-10">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-sticky-note me-2"></i>Table Notes
                                    </h6>
                                    @if(!$editingNote)
                                        <button wire:click="toggleEditNote" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit me-1"></i>{{ empty($tableNote) ? 'Add Note' : 'Edit Note' }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                @if($editingNote)
                                    <div>
                                        <textarea wire:model="tableNote" class="form-control mb-3" rows="4" 
                                                  placeholder="Add notes about this table (purpose, relationships, important fields, etc.)"></textarea>
                                        
                                        <!-- Option to save to table description column -->
                                        @if($hasDescriptionColumn)
                                        <div class="alert alert-info py-2 mb-3">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" wire:model="saveToTableDesc" id="saveToTableDesc">
                                                <label class="form-check-label" for="saveToTableDesc">
                                                    <i class="fas fa-database me-1"></i>
                                                    <strong>Also save to table's metadata (Extended Property)</strong>
                                                    <small class="text-muted d-block mt-1">
                                                        💡 This will save the note as MS_Description property on the table itself in SQL Server
                                                        <br>📝 Viewable in SSMS Object Explorer → Table → Properties → Extended Properties
                                                    </small>
                                                </label>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <div class="d-flex gap-2">
                                            <button wire:click="saveNote" class="btn btn-success btn-sm">
                                                <i class="fas fa-save me-1"></i>Save Note
                                            </button>
                                            <button wire:click="toggleEditNote" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-times me-1"></i>Cancel
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    @if(!empty($tableNote))
                                        <div class="bg-light p-3 rounded">
                                            <pre class="mb-0" style="white-space: pre-wrap;">{{ $tableNote }}</pre>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0 fst-italic">No notes added yet. Click "Add Note" to document this table.</p>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Columns Table -->
                        <h6 class="mb-3"><i class="fas fa-columns me-2"></i>Columns ({{ count($tableColumns) }})</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Column Name</th>
                                        <th>Data Type</th>
                                        <th>Length/Precision</th>
                                        <th class="text-center">Nullable</th>
                                        <th class="text-center">PK</th>
                                        <th class="text-center">FK</th>
                                        <th class="text-center">Identity</th>
                                        <th>Default</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tableColumns as $column)
                                        <tr>
                                            <td class="text-muted">{{ $column->ORDINAL_POSITION }}</td>
                                            <td>
                                                <strong>{{ $column->COLUMN_NAME }}</strong>
                                                @if($column->IS_PRIMARY_KEY === 'YES')
                                                    <i class="fas fa-key text-warning ms-1" title="Primary Key"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ strtoupper($column->DATA_TYPE) }}</span>
                                            </td>
                                            <td>
                                                @if($column->CHARACTER_MAXIMUM_LENGTH)
                                                    {{ $column->CHARACTER_MAXIMUM_LENGTH }}
                                                @elseif($column->NUMERIC_PRECISION)
                                                    {{ $column->NUMERIC_PRECISION }},{{ $column->NUMERIC_SCALE }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($column->IS_NULLABLE === 'YES')
                                                    <span class="badge bg-success">YES</span>
                                                @else
                                                    <span class="badge bg-danger">NO</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($column->IS_PRIMARY_KEY === 'YES')
                                                    <i class="fas fa-check text-success"></i>
                                                @else
                                                    <i class="fas fa-times text-muted"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($column->IS_FOREIGN_KEY === 'YES')
                                                    <i class="fas fa-check text-info"></i>
                                                @else
                                                    <i class="fas fa-times text-muted"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($column->IS_IDENTITY === 'YES')
                                                    <i class="fas fa-check text-warning"></i>
                                                @else
                                                    <i class="fas fa-times text-muted"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $column->COLUMN_DEFAULT ?? '-' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- CREATE TABLE Script -->
                        <div class="mt-4">
                            <h6 class="mb-3">
                                <i class="fas fa-code me-2"></i>CREATE TABLE Script
                                <button class="btn btn-sm btn-outline-secondary float-end" 
                                        onclick="copyToClipboard('createScript{{ $selectedTable }}')">
                                    <i class="fas fa-copy me-1"></i>Copy
                                </button>
                            </h6>
                            <pre class="bg-dark text-light p-3 rounded" id="createScript{{ $selectedTable }}" style="max-height: 400px; overflow-y: auto;"><code>{{ $this->getCreateTableScript() }}</code></pre>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeTableSchema">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const text = element.textContent;
            navigator.clipboard.writeText(text).then(() => {
                alert('Script copied to clipboard!');
            });
        }
    </script>
</div>
