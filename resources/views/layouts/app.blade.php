<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light">

    <title>GuidanceCare</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- GuidanceCare shared UI rules -->
    @include('layouts.gc-ui')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="gc-shell">
        @include('layouts.sidebar')

        <div class="gc-shell-main">
            <main>
                @yield('content')
            </main>

            {{-- One footer for every signed-in page --}}
            <footer class="gc-site-footer">
                GuidanceCare System • STI College Global City • Centralized Student Support &amp; Privacy Compliant
            </footer>
        </div>
    </div>

    {{--
        Mobile table cards: on screens under 640px every list table turns into stacked cards.
        This script copies each column heading into its cells (data-label) so the card can
        show "Status: Pending" without changing any page's table markup.
    --}}
    <script>
        (function () {
            function cardify() {
                document.querySelectorAll('.gc-shell-main table').forEach(function (table) {
                    var head = table.tHead && table.tHead.rows[0];
                    if (!head || table.classList.contains('gc-cards')) return;
                    var labels = [];
                    Array.prototype.forEach.call(head.cells, function (th) {
                        for (var k = 0; k < (th.colSpan || 1); k++) labels.push(th.textContent.trim());
                    });
                    table.classList.add('gc-cards');
                    Array.prototype.forEach.call(table.tBodies, function (body) {
                        Array.prototype.forEach.call(body.rows, function (tr) {
                            var col = 0;
                            Array.prototype.forEach.call(tr.cells, function (td, i) {
                                if (td.colSpan > 1) { td.classList.add('gc-cell-full'); col += td.colSpan; return; }
                                td.setAttribute('data-label', labels[col] || '');
                                if (i === 0) td.classList.add('gc-cell-title');
                                var last = i === tr.cells.length - 1;
                                if (last && td.querySelector('a, button, form')) td.classList.add('gc-cell-actions');
                                if (!labels[col]) td.classList.add('gc-cell-nolabel');
                                col++;
                            });
                        });
                    });
                });
            }
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', cardify);
            else cardify();
        })();
    </script>
</body>
</html>
