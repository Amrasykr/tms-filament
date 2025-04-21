@php
    $sessionCount = $sessions->count();
@endphp

<div class="{{ $sessionCount > 6 ? 'overflow-x-auto' : '' }} rounded-xl border border-gray-200 shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 bg-white rounded-xl border-collapse w-full">
        <thead class="bg-gray-100 text-sm font-semibold text-gray-700">
            <tr>
                <th class="px-4 py-3 text-left sticky left-0 bg-gray-100 z-10 min-w-[200px] rounded-tl-xl">
                    Nama Siswa
                </th>
                @foreach ($sessions as $session)
                    @php
                        $sessionStatus = $session->status ?? 'pending'; // default jika tidak ada status
                        $badge = match($sessionStatus) {
                            'completed' => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-700'],
                            'pending' => ['label' => 'Terjadwal', 'class' => 'bg-gray-100 text-gray-600'],
                            default => ['label' => ucfirst($sessionStatus), 'class' => 'bg-gray-100 text-gray-600'],
                        };
                    @endphp
                    <th class="px-4 py-3 text-center whitespace-nowrap min-w-[120px]">
                        <div class="flex flex-col items-center gap-1">
                            <x-heroicon-o-calendar class="w-5 h-5 text-gray-500" />
                            <span>{{ \Carbon\Carbon::parse($session->session_date)->translatedFormat('d M') }}</span>
                            <span class="mt-1 px-2 py-0.5 rounded-full text-[10px] font-medium {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm text-gray-800">
            @foreach ($students as $student)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium sticky left-0 bg-white min-w-[300px] border-t border-gray-100">
                        {{ $student->name }}
                    </td>
                    @foreach ($sessions as $session)
                        @php
                            $attendance = $session->attendances->firstWhere('student_id', $student->id);
                            $status = match(optional($attendance)->status) {
                                'present' => ['Hadir', 'bg-green-100 text-green-700'],
                                'absent' => ['Tidak Hadir', 'bg-red-100 text-red-700'],
                                'sick' => ['Sakit', 'bg-yellow-100 text-yellow-700'],
                                'permission' => ['Izin', 'bg-blue-100 text-blue-700'],
                                default => ['-', 'text-gray-400'],
                            };
                        @endphp
                        <td class="px-4 py-2 text-center border-t border-gray-100">
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $status[1] }}">
                                {{ $status[0] }}
                            </span>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
