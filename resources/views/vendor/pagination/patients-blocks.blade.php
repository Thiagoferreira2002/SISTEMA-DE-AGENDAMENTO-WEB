@if ($paginator->hasPages())
    @php
        $groupSize = 6;
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $currentGroup = (int) ceil($currentPage / $groupSize);
        $startPage = (($currentGroup - 1) * $groupSize) + 1;
        $endPage = min($startPage + $groupSize - 1, $lastPage);
        $previousGroupPage = max(1, $startPage - $groupSize);
        $nextGroupPage = min($lastPage, $endPage + 1);
    @endphp

    <nav aria-label="Paginacao de pacientes">
        <ul class="pagination mb-0">
            <li class="page-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
                <a
                    class="page-link"
                    href="{{ $currentPage <= 1 ? '#' : $paginator->url(1) }}"
                    aria-label="Primeira pagina"
                >
                    <span aria-hidden="true">&lt;&lt;&lt;</span>
                </a>
            </li>

            <li class="page-item {{ $startPage <= 1 ? 'disabled' : '' }}">
                <a
                    class="page-link"
                    href="{{ $startPage <= 1 ? '#' : $paginator->url($previousGroupPage) }}"
                    aria-label="Paginas anteriores"
                >
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            @for ($page = $startPage; $page <= $endPage; $page++)
                <li class="page-item {{ $page === $currentPage ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                </li>
            @endfor

            <li class="page-item {{ $endPage >= $lastPage ? 'disabled' : '' }}">
                <a
                    class="page-link"
                    href="{{ $endPage >= $lastPage ? '#' : $paginator->url($nextGroupPage) }}"
                    aria-label="Proximas paginas"
                >
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>

            <li class="page-item {{ $currentPage >= $lastPage ? 'disabled' : '' }}">
                <a
                    class="page-link"
                    href="{{ $currentPage >= $lastPage ? '#' : $paginator->url($lastPage) }}"
                    aria-label="Ultima pagina"
                >
                    <span aria-hidden="true">&gt;&gt;&gt;</span>
                </a>
            </li>
        </ul>
    </nav>
@endif
