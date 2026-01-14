@extends('layouts.app')

@section('title', 'Critical Path')

@section('content')
<div class="container mt-2">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0d6efd, #1e90ff); color: white;">
            <h6 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Critical Path</h6>
            <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#criticalPathModal">
                <i class="bi bi-plus-lg"></i> Create Milestone
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered align-middle text-center" id="criticalPathTable">
                    <thead class="table-secondary">
                        <tr>
                            <th scope="col">Milestone</th>
                            <th scope="col">Owner</th>
                            <th scope="col">Start Date</th>
                            <th scope="col">Due Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <tr class="text-muted">
                            <td colspan="6" class="py-3">No milestones yet. Click "Create Milestone" to get started.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="criticalPathModal" tabindex="-1" aria-labelledby="criticalPathModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="criticalPathForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="criticalPathModalLabel">Create Critical Path Milestone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="milestoneName" class="form-label small mb-1">Milestone Name *</label>
                            <input type="text" class="form-control form-control-sm" id="milestoneName" name="milestone_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="milestoneOwner" class="form-label small mb-1">Owner</label>
                            <input type="text" class="form-control form-control-sm" id="milestoneOwner" name="milestone_owner">
                        </div>
                        <div class="col-md-6">
                            <label for="milestoneStartDate" class="form-label small mb-1">Start Date</label>
                            <input type="date" class="form-control form-control-sm" id="milestoneStartDate" name="start_date">
                        </div>
                        <div class="col-md-6">
                            <label for="milestoneDueDate" class="form-label small mb-1">Due Date</label>
                            <input type="date" class="form-control form-control-sm" id="milestoneDueDate" name="due_date">
                        </div>
                        <div class="col-12">
                            <label for="milestoneDescription" class="form-label small mb-1">Description</label>
                            <textarea class="form-control form-control-sm" id="milestoneDescription" name="description" rows="3" placeholder="Outline key steps, dependencies, or risk notes for this milestone."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Save Milestone</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
