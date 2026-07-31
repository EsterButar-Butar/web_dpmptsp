@php
    $level = (int) $item->level;
    $indent = max(0, ($level - 1) * 28);
    $hasChildren = (int) $item->child_count > 0;
    $badgeStyles = [
        'Kategori' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'Golongan Pokok' => 'border-orange-200 bg-orange-50 text-orange-700',
        'Golongan' => 'border-sky-200 bg-sky-50 text-sky-700',
        'Subgolongan' => 'border-red-200 bg-red-50 text-red-700',
        'Kelompok' => 'border-violet-200 bg-violet-50 text-violet-700',
    ];
    $badgeStyle = $badgeStyles[$item->struktur] ?? 'border-slate-200 bg-slate-50 text-slate-700';
@endphp
<tr data-kbli-row data-code="{{ $item->kode }}" data-parent="{{ $item->kode_induk }}" data-level="{{ $level }}"
    @if ($level === 1) id="kategori-{{ $item->kode }}" @endif
    class="transition {{ $level === 1 ? 'bg-slate-50/80 hover:bg-emerald-50/60' : 'hover:bg-slate-50/80' }}">
    <td class="kbli-tree-cell px-5 py-4">
        @for ($treeLevel = 1; $treeLevel < $level; $treeLevel++)
            <span class="kbli-tree-line" style="left: {{ 22 + (($treeLevel - 1) * 28) }}px"></span>
        @endfor
        @if ($level > 1)
            <span class="kbli-tree-elbow" style="left: {{ 22 + (($level - 2) * 28) }}px; width: 20px"></span>
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
        <div class="max-w-[390px] font-semibold leading-relaxed text-slate-700" title="{{ $item->judul }}">{{ $item->judul }}</div>
        @if ($item->catatan)
            <div class="mt-2 inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-2 py-1 text-[10px] font-semibold text-amber-700" title="{{ $item->catatan }}"><i class="fa-solid fa-circle-info"></i> Ada catatan data</div>
        @endif
    </td>
    <td class="px-4 py-4 align-top"><div class="kbli-clamp-2 max-w-[320px] text-xs leading-relaxed text-slate-500" title="{{ $item->cakupan }}">{{ $item->cakupan ?: '-' }}</div></td>
    <td class="px-4 py-4 align-top"><div class="kbli-clamp-2 max-w-[320px] text-xs leading-relaxed text-slate-500" title="{{ $item->tidak_cakupan }}">{{ $item->tidak_cakupan ?: '-' }}</div></td>
    <td class="px-4 py-4">
        <div class="flex items-center justify-center gap-2">
            <a href="{{ route('admin.data-kbli.index', array_merge(request()->query(), ['edit' => $item->id, 'mode' => 'edit'])) }}" title="Edit data"
               class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-600">
                <i class="fa-regular fa-pen-to-square"></i>
            </a>
            <button type="button" title="Hapus data" data-delete-kbli
                data-delete-url="{{ route('admin.data-kbli.destroy', $item->id) }}"
                data-delete-code="{{ $item->kode }}" data-delete-title="{{ $item->judul }}"
                data-delete-children="{{ (int) $item->child_count }}"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-red-100 bg-white text-red-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                <i class="fa-regular fa-trash-can"></i>
            </button>
        </div>
    </td>
</tr>
