{{--
    Student picker: every student has a school-provisioned account, so referrals and cases
    always link to a real student. Type to filter by name, student number or school email.
    Each row: name (bold) / student number (and year level when known) / email (muted).
    Submits the chosen account as student_id (a radio group, so the browser enforces "required").
    Needs: $students (id, name, email, student_number; year_level optional).
--}}
<style>
    .gc-picker { margin-bottom: 16px; }
    .gc-picker-label { display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; }
    .gc-picker-search { width: 100%; padding: 10px 12px 10px 36px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; box-sizing: border-box;
        background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3ccircle cx='9' cy='9' r='5.5' stroke='%239ca3af' stroke-width='1.5'/%3e%3cpath d='M13.5 13.5L17 17' stroke='%239ca3af' stroke-width='1.5' stroke-linecap='round'/%3e%3c/svg%3e") no-repeat 10px center / 18px; }
    .gc-picker-search:focus { outline: none; border-color: var(--gc-primary); box-shadow: 0 0 0 3px rgba(0,56,168,.12); }
    .gc-picker-list { margin-top: 8px; max-height: 264px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 10px; background: #fff; }
    .gc-picker-row { display: flex; align-items: flex-start; gap: 12px; padding: 10px 14px; border-top: 1px solid #f3f4f6; cursor: pointer; }
    .gc-picker-row:first-child { border-top: none; }
    .gc-picker-row:hover { background: #f8fafc; }
    .gc-picker-row input { position: absolute; opacity: 0; pointer-events: none; }
    .gc-picker-dot { flex: 0 0 18px; height: 18px; margin-top: 2px; border: 2px solid #cbd5e1; border-radius: 9999px; box-sizing: border-box; background: #fff; }
    .gc-picker-row:has(input:checked) { background: #eff6ff; box-shadow: inset 3px 0 0 var(--gc-primary); }
    .gc-picker-row:has(input:checked) .gc-picker-dot { border-color: var(--gc-primary); border-width: 5px; }
    .gc-picker-row:has(input:focus-visible) { outline: 2px solid var(--gc-primary); outline-offset: -2px; }
    .gc-picker-name { display: block; font-size: 14px; font-weight: 600; color: #111827; }
    .gc-picker-meta { display: block; font-size: 13px; color: #374151; margin-top: 1px; }
    .gc-picker-mail { display: block; font-size: 12px; color: #9ca3af; margin-top: 1px; }
    .gc-picker-empty { padding: 14px; font-size: 13px; color: #9ca3af; text-align: center; }
</style>

<div class="gc-picker">
    <label for="student_search" class="gc-picker-label">Student *</label>
    <input type="text" id="student_search" class="gc-picker-search" autocomplete="off"
           placeholder="Search by name, student number or school email" aria-controls="student_list">

    <div id="student_list" class="gc-picker-list" role="radiogroup" aria-label="Student">
        @forelse ($students as $s)
            <label class="gc-picker-row" data-search="{{ strtolower($s->name . ' ' . $s->student_number . ' ' . $s->email) }}">
                <input type="radio" name="student_id" value="{{ $s->id }}" required @checked(old('student_id') == $s->id)>
                <span class="gc-picker-dot" aria-hidden="true"></span>
                <span>
                    <span class="gc-picker-name">{{ $s->name }}</span>
                    <span class="gc-picker-meta">
                        {{ $s->student_number ?: 'No student number' }}@if(!empty($s->year_level)) · {{ $s->year_level }}@endif
                    </span>
                    <span class="gc-picker-mail">{{ $s->email }}</span>
                </span>
            </label>
        @empty
            <p class="gc-picker-empty">No student accounts yet. Ask the Admin to create them.</p>
        @endforelse
        <p class="gc-picker-empty" id="student_none" hidden>No student matches your search.</p>
    </div>
    <p class="gc-help">Only students with a school account appear here. You can also search by school email.</p>
</div>

<script>
    (function () {
        var box = document.getElementById('student_search');
        var list = document.getElementById('student_list');
        var none = document.getElementById('student_none');
        if (!box || !list) return;
        var rows = list.querySelectorAll('.gc-picker-row');
        box.addEventListener('input', function () {
            var q = box.value.trim().toLowerCase(), shown = 0;
            rows.forEach(function (row) {
                var match = q === '' || (row.getAttribute('data-search') || '').indexOf(q) !== -1;
                // keep the chosen student visible even when it no longer matches
                var keep = match || row.querySelector('input').checked;
                row.style.display = keep ? '' : 'none';
                if (keep) shown++;
            });
            if (none) none.hidden = shown !== 0;
        });
        var picked = list.querySelector('input:checked');
        if (picked) picked.closest('.gc-picker-row').scrollIntoView({ block: 'nearest' });
    })();
</script>
