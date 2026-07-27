@php
    $level = (int) $item->level;
    $indent = max(0, ($level - 1) * 28);
    $hasChildren = (int) $item->child_count > 0;
    $badgeStyles = [
        'Seksi' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'Divisi' => 'border-orange-200 bg-orange-50 text-orange-700',
        'Kelompok' => 'border-sky-200 bg-sky-50 text-sky-700',
        'Kelas' => 'border-red-200 bg-red-50 text-red-700',
        'Subkelas' => 'border-violet-200 bg-violet-50 text-violet-700',
        'Kelompok Komoditas' => 'border-pink-200 bg-pink-50 text-pink-700',
        'Komoditas' => 'border-cyan-200 bg-cyan-50 text-cyan-700',
    ];
    $badgeStyle = $badgeStyles[$item->struktur] ?? 'border-slate-200 bg-slate-50 text-slate-700';
    $isActive = ($item->status ?? 'Aktif') === 'Aktif';
@endphp
<tr data-kbki-row data-code="{{ $item->kode }}" data-parent="{{ $item->kode_induk }}" data-level="{{ $level }}"
    @if ($level === 1) id="seksi-{{ $item->kode }}" @endif
    class="transition {{ $level === 1 ? 'bg-slate-50/80 hover:bg-emerald-50/60' : 'hover:bg-slate-50/80' }}">
    <td class="kbki-tree-cell px-5 py-4">
        @for ($treeLevel = 1; $treeLevel < $level; $treeLevel++)
            <span class="kbki-tree-line" style="left: {{ 22 + (($treeLevel - 1) * 28) }}px"></span>
        @endfor
        @if ($level > 1)
            <span class="kbki-tree-elbow" style="left: {{ 22 + (($level - 2) * 28) }}px; width: 20px"></span>
        @endif
        <div class="relative flex items-center gap-2" style="padding-left: {{ $indent }}px">
            @if ($hasChildren && $hierarchyMode)
                <button type="button" data-tree-toggle="{{ $item->kode }}" data-loaded="0" data-expanded="0"
                    aria-label="Buka atau tutup turunan {{ $item->kode }}" aria-expanded="false"
                    class="inline-flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-[10px] text-slate-500 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-600">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            @else
                <span class="inline-flex h-7 w-7 flex-shrink-0 items-center justify-center text-[8px] text-slate-300"><i class="fa-solid fa-circle"></i></span>
            @endif
            <span class="inline-flex whitespace-nowrap rounded-full border px-2.5 py-1 text-[11px] font-bold {{ $badgeStyle }}">{{ $item->struktur }}</span>
        </div>
    </td>
    <td class="px-4 py-4"><span class="inline-flex rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 font-mono text-xs font-black text-emerald-700">{{ $item->kode }}</span></td>
    <td class="px-4 py-4">
        <div class="kbki-clamp-2 font-bold leading-6 text-slate-700" title="{{ $item->judul }}">{{ $item->judul }}</div>
        @if ($item->catatan)
            <div class="mt-1 text-xs leading-5 text-slate-400" title="{{ $item->catatan }}">{{ \Illuminate\Support\Str::limit($item->catatan, 95) }}</div>
        @endif
    </td>
    <td class="px-4 py-4 text-center text-sm font-semibold text-slate-500">{{ $item->halaman ?: '-' }}</td>
    <td class="px-4 py-4">
        <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-bold {{ $isActive ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' }}">
            <span class="h-1.5 w-1.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-red-500' }}"></span>{{ $item->status ?? 'Aktif' }}
        </span>
    </td>
    <td class="px-4 py-4 text-xs font-medium text-slate-500">{{ $item->sumber_sheet ?: '-' }}</td>
    <td class="px-4 py-4">
        <div class="flex items-center justify-center gap-2">
            <a href="{{ route('admin.data-kbki.index', array_merge(request()->query(), ['edit' => $item->id, 'mode' => 'edit'])) }}" title="Edit KBKI"
               class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-600">
                <i class="fa-regular fa-pen-to-square"></i>
            </a>
            <button type="button" title="Hapus KBKI" data-delete-kbki
                data-delete-url="{{ route('admin.data-kbki.destroy', $item->id) }}"
                data-delete-code="{{ $item->kode }}" data-delete-title="{{ $item->judul }}"
                data-delete-children="{{ (int) $item->child_count }}"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-100 bg-white text-red-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                <i class="fa-regular fa-trash-can"></i>
            </button>
        </div>
    </td>
</tr>
