{{--
    Student picker: every student has a school-provisioned account, so referrals and cases
    always link to a real student. Type in the search box to filter by name, student number or email.
    Needs: $students (id, name, email, student_number).
--}}
@php $pickField = 'width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px; box-sizing:border-box;'; @endphp
<div style="margin-bottom: 16px;">
    <label for="student_search" style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Student *</label>
    <input type="text" id="student_search" placeholder="Search by name, student number or school email"
           autocomplete="off" style="{{ $pickField }} margin-bottom:8px;">
    <select name="student_id" id="student_id" required size="6" style="{{ $pickField }} background:white;">
        @forelse ($students as $s)
            <option value="{{ $s->id }}" @selected(old('student_id') == $s->id)
                    data-search="{{ strtolower($s->name . ' ' . $s->student_number . ' ' . $s->email) }}">
                {{ $s->name }} — {{ $s->student_number ?: 'no student no.' }} — {{ $s->email }}
            </option>
        @empty
            <option value="" disabled>No student accounts yet. Ask the Admin to create them.</option>
        @endforelse
    </select>
    <p style="margin:6px 0 0; font-size:12px; color:#6b7280;">Only students with a school account appear here.</p>
</div>
<script>
    (function () {
        var box = document.getElementById('student_search');
        var sel = document.getElementById('student_id');
        if (!box || !sel) return;
        box.addEventListener('input', function () {
            var q = box.value.trim().toLowerCase();
            Array.prototype.forEach.call(sel.options, function (o) {
                var hay = o.getAttribute('data-search') || '';
                o.hidden = q !== '' && hay.indexOf(q) === -1;
            });
        });
    })();
</script>
