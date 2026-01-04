<div class="mb-4 border-bottom">
    <div class="d-flex gap-4">
        {{-- 3. Supervisor Assigned (NEW) --}}
        <a href="{{ route('admin.org.structure.assignments') }}" 
           class="text-decoration-none pb-2 fw-bold {{ Route::is('admin.org.structure.assignments') ? 'text-dark border-bottom border-3 border-dark' : 'text-muted' }}"
           style="font-size: 0.95rem;">
           <i class="bi bi-person-check me-2"></i>Supervisor Assigned
        </a>

        {{-- 4. Team Department (NEW) --}}
        <a href="{{ route('admin.org.structure.teams') }}" 
           class="text-decoration-none pb-2 fw-bold {{ Route::is('admin.org.structure.teams') ? 'text-dark border-bottom border-3 border-dark' : 'text-muted' }}"
           style="font-size: 0.95rem;">
           <i class="bi bi-people me-2"></i>Team Department
        </a>
    </div>
</div>