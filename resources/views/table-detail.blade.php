@extends('layouts.admin')

@section('content')
<div x-data="tableDetail()" x-init="init('{{ $tableName }}')" class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-table text-primary me-2"></i>
                {{ $tableName }}
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('database.tables') }}">Database Tables</a></li>
                    <li class="breadcrumb-item active">{{ $tableName }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('database.tables') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
        <button @click="showCreateTableScript()" class="btn btn-success">
            <i class="fas fa-code me-2"></i>View CREATE TABLE Script
        </button>
    </div>

    <!-- Table Information Card -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Table Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Table ID:</td>
                                    <td><code>{{ $tableInfo->tr_aplikasi_table_id ?? '-' }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Table Name:</td>
                                    <td><strong>{{ $tableInfo->table_name ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Table Type:</td>
                                    <td>{{ $tableInfo->table_type ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Schema Description:</td>
                                    <td>{{ $tableInfo->note_schema ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Total Records:</td>
                                    <td><span class="badge bg-info">{{ number_format($tableInfo->record ?? 0) }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">First Record:</td>
                                    <td>{{ $tableInfo->record_date_start ? \Carbon\Carbon::parse($tableInfo->record_date_start)->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Last Record:</td>
                                    <td>{{ $tableInfo->record_date_last ? \Carbon\Carbon::parse($tableInfo->record_date_last)->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Last Updated:</td>
                                    <td>{{ $tableInfo->date_updated ? \Carbon\Carbon::parse($tableInfo->date_updated)->format('d M Y H:i') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @if($tableInfo->table_note)
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-sticky-note me-2"></i>
                        <strong>Note:</strong> {{ $tableInfo->table_note }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Table Schema Card -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-database me-2"></i>Table Schema ({{ count($columns) }} columns)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Column Name</th>
                                    <th>Data Type</th>
                                    <th>Max Length</th>
                                    <th>Nullable</th>
                                    <th>Default</th>
                                    <th>Key</th>
                                    <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($columns as $index => $column)
                                <tr @click="editComment('{{ $column->COLUMN_NAME }}', '{{ addslashes($column->COLUMN_COMMENT ?? '') }}')" 
                                    style="cursor: pointer;"
                                    title="Click to add/edit comment">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <code>{{ $column->COLUMN_NAME }}</code>
                                        @if($column->IS_PRIMARY_KEY)
                                            <i class="fas fa-key text-warning ms-1" title="Primary Key"></i>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $column->DATA_TYPE }}</span></td>
                                    <td>{{ $column->CHARACTER_MAXIMUM_LENGTH ?? '-' }}</td>
                                    <td>
                                        @if($column->IS_NULLABLE === 'YES')
                                            <span class="badge bg-warning">YES</span>
                                        @else
                                            <span class="badge bg-success">NO</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $column->COLUMN_DEFAULT ?? '-' }}</small></td>
                                    <td>
                                        @if($column->IS_PRIMARY_KEY)
                                            <span class="badge bg-warning">PK</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted" x-text="getColumnComment('{{ $column->COLUMN_NAME }}')">
                                            {{ $column->COLUMN_COMMENT ?? 'Click to add comment...' }}
                                        </small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No columns found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Data Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Data (Top 100 Most Recent Records)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>#</th>
                                    @foreach($columns as $column)
                                    <th>{{ $column->COLUMN_NAME }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentData as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    @foreach($columns as $column)
                                    <td>
                                        @php
                                            $value = $row->{$column->COLUMN_NAME} ?? '-';
                                            if ($value instanceof \DateTime) {
                                                $value = $value->format('Y-m-d H:i:s');
                                            } elseif (is_string($value) && strlen($value) > 50) {
                                                $value = substr($value, 0, 50) . '...';
                                            }
                                        @endphp
                                        <small>{{ $value }}</small>
                                    </td>
                                    @endforeach
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ count($columns) + 1 }}" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                                        <p class="mb-0">No data available in this table</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(count($recentData) > 0)
                <div class="card-footer text-muted">
                    <small><i class="fas fa-info-circle me-1"></i>Showing {{ count($recentData) }} most recent records</small>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- CREATE TABLE Script Modal -->
    <div class="modal fade" id="scriptModal" tabindex="-1" aria-labelledby="scriptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="scriptModalLabel">
                        <i class="fas fa-code me-2"></i>
                        CREATE TABLE Script: <span x-text="tableName"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Copy this script to recreate the table structure
                        </span>
                        <button @click="copyScript()" class="btn btn-sm btn-primary">
                            <i class="fas fa-copy me-1"></i>Copy to Clipboard
                        </button>
                    </div>
                    <pre class="bg-dark text-light p-3 rounded" style="max-height: 500px; overflow-y: auto;"><code x-text="createTableScript"></code></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Comment Modal - Bootstrap Native -->
    <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="commentModalLabel">
                        <i class="fas fa-comment-dots me-2"></i>
                        Edit Column Comment: <span x-text="commentModal.columnName"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Comment:</label>
                        <textarea class="form-control" 
                                  rows="4" 
                                  x-model="commentModal.comment"
                                  @input="commentModal.comment = $event.target.value"
                                  placeholder="Enter comment for this column..."
                                  :disabled="commentModal.submitting"
                                  id="commentTextarea"></textarea>
                        <small class="text-muted">Current value: <span x-text="commentModal.comment"></span></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" 
                            class="btn btn-primary" 
                            @click="saveComment()"
                            :disabled="commentModal.submitting">
                        <span x-show="!commentModal.submitting">
                            <i class="fas fa-save me-1"></i>Save Comment
                        </span>
                        <span x-show="commentModal.submitting">
                            <i class="fas fa-spinner fa-spin me-1"></i>Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function tableDetail() {
    return {
        tableName: '',
        createTableScript: '',
        commentModal: {
            columnName: '',
            comment: '',
            submitting: false
        },
        columnComments: {},
        
        init(tableName) {
            this.tableName = tableName;
            // Load existing comments
            @foreach($columns as $column)
                @if($column->COLUMN_COMMENT)
                    this.columnComments['{{ $column->COLUMN_NAME }}'] = '{{ addslashes($column->COLUMN_COMMENT) }}';
                @endif
            @endforeach
        },
        
        showCreateTableScript() {
            this.generateCreateTableScript();
            const modal = new bootstrap.Modal(document.getElementById('scriptModal'));
            modal.show();
        },
        
        generateCreateTableScript() {
            let script = `CREATE TABLE [dbo].[${this.tableName}] (\n`;
            
            const columns = @json($columns);
            
            columns.forEach((col, index) => {
                script += `    [${col.COLUMN_NAME}] ${col.DATA_TYPE.toUpperCase()}`;
                
                // Add length for varchar, nvarchar, char, etc
                if (col.CHARACTER_MAXIMUM_LENGTH) {
                    if (col.CHARACTER_MAXIMUM_LENGTH === -1) {
                        script += `(MAX)`;
                    } else {
                        script += `(${col.CHARACTER_MAXIMUM_LENGTH})`;
                    }
                }
                
                // Add NULL/NOT NULL
                script += col.IS_NULLABLE === 'YES' ? ' NULL' : ' NOT NULL';
                
                // Add default
                if (col.COLUMN_DEFAULT) {
                    script += ` DEFAULT ${col.COLUMN_DEFAULT}`;
                }
                
                // Add comma if not last column
                if (index < columns.length - 1) {
                    script += ',';
                }
                
                // Add comment if exists
                if (col.COLUMN_COMMENT) {
                    script += ` -- ${col.COLUMN_COMMENT}`;
                }
                
                script += '\n';
            });
            
            // Add primary key constraint
            const pkColumns = columns.filter(col => col.IS_PRIMARY_KEY);
            if (pkColumns.length > 0) {
                const pkColumnNames = pkColumns.map(col => `[${col.COLUMN_NAME}]`).join(', ');
                script += `,\n    CONSTRAINT [PK_${this.tableName}] PRIMARY KEY CLUSTERED (${pkColumnNames})\n`;
            }
            
            script += `);\n`;
            
            // Add extended properties for column comments
            columns.forEach(col => {
                if (col.COLUMN_COMMENT) {
                    script += `\nEXEC sp_addextendedproperty `;
                    script += `@name = N'MS_Description', @value = N'${col.COLUMN_COMMENT.replace(/'/g, "''")}', `;
                    script += `@level0type = N'SCHEMA', @level0name = 'dbo', `;
                    script += `@level1type = N'TABLE', @level1name = '${this.tableName}', `;
                    script += `@level2type = N'COLUMN', @level2name = '${col.COLUMN_NAME}';`;
                }
            });
            
            this.createTableScript = script;
        },
        
        copyScript() {
            navigator.clipboard.writeText(this.createTableScript).then(() => {
                alert('Script copied to clipboard!');
            }).catch(err => {
                alert('Failed to copy script: ' + err);
            });
        },
        
        editComment(columnName, currentComment) {
            this.commentModal.columnName = columnName;
            this.commentModal.comment = currentComment || '';
            
            const modal = new bootstrap.Modal(document.getElementById('commentModal'));
            modal.show();
            
            // Focus textarea after modal is shown
            setTimeout(() => {
                const textarea = document.getElementById('commentTextarea');
                if (textarea) {
                    textarea.focus();
                    textarea.select();
                }
            }, 500);
        },
        
        async saveComment() {
            this.commentModal.submitting = true;
            
            try {
                const response = await fetch(`/api/table-columns/update-comment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        table_name: this.tableName,
                        column_name: this.commentModal.columnName,
                        comment: this.commentModal.comment
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update local state
                    this.columnComments[this.commentModal.columnName] = this.commentModal.comment;
                    
                    // Close modal
                    const modalElement = document.getElementById('commentModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Show success message
                    alert('Comment saved successfully!');
                } else {
                    alert('Failed to save comment: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                alert('Error saving comment: ' + error.message);
            } finally {
                this.commentModal.submitting = false;
            }
        },
        
        getColumnComment(columnName) {
            return this.columnComments[columnName] || 'Click to add comment...';
        }
    }
}
</script>

<style>
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>
@endsection
