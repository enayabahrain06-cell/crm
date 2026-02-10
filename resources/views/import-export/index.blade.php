@extends('layouts.app')

@section('title', 'Import/Export')
@section('page-title', 'Data Import/Export')

@section('content')
<div class="row">
    <!-- Import Section -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Import Data</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('import-export.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Import Type</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select type...</option>
                            <option value="customers">Customers</option>
                            <option value="visits">Visits</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File</label>
                        <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
                        <small class="text-muted">Supported formats: CSV, XLSX, XLS</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-upload me-2"></i>Import
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-download me-2"></i>Export Data</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('import-export.export') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Export Type</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select type...</option>
                            <option value="customers">Customers</option>
                            <option value="visits">Visits</option>
                            <option value="loyalty">Loyalty</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Format</label>
                        <select name="format" class="form-select">
                            <option value="csv">CSV</option>
                            <option value="xlsx">XLSX</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Recent Exports -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Exports</h5>
    </div>
    <div class="card-body p-0">
        @if(count($recentExports) > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentExports as $file)
                    <tr>
                        <td>
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                            {{ basename($file) }}
                        </td>
                        <td>
                            <a href="{{ route('import-export.download', ['file' => basename($file)]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download me-1"></i>Download
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-folder-x display-4"></i>
            <p class="mt-2">No export files found</p>
        </div>
        @endif
    </div>
</div>
@endsection

