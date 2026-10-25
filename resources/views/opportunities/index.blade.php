<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sales Pipeline / Opportunities') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 text-sm text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Quick Add Deal Form -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Add New Deal / Opportunity</h3>
                <form action="{{ route('opportunities.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    @csrf
                    <div>
                        <input type="text" name="title" placeholder="Deal Name (e.g. Acme Migration)" class="border-gray-300 rounded text-sm w-full" required>
                    </div>
                    <div>
                        <input type="number" step="0.01" name="value" placeholder="Value ($)" class="border-gray-300 rounded text-sm w-full" required>
                    </div>
                    <div>
                        <select name="crm_contact_id" class="border-gray-300 rounded text-sm w-full">
                            <option value="">-- Primary Contact --</option>
                            @foreach ($contacts as $c)
                                <option value="{{ $c->id }}">{{ $c->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="date" name="expected_close_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="border-gray-300 rounded text-sm w-full">
                    </div>
                    <div>
                        <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded text-sm">+ Add Opportunity</button>
                    </div>
                </form>
            </div>

            <!-- Kanban Pipeline Board -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 overflow-x-auto">
                @foreach ($stages as $stage)
                    <div class="bg-gray-100 rounded-lg p-3 min-w-[220px]">
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-bold text-xs text-gray-700 uppercase">{{ $stage->name }}</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-white text-gray-600">{{ $stage->opportunities->count() }}</span>
                        </div>
                        <div class="text-xs font-semibold text-indigo-700 mb-3">
                            Total: ${{ number_format($stage->opportunities->sum('value_cents') / 100, 0) }}
                        </div>

                        <div class="space-y-3">
                            @forelse ($stage->opportunities as $opp)
                                <div class="bg-white p-3 rounded shadow-sm border border-gray-200">
                                    <div class="font-bold text-sm text-gray-900">{{ $opp->title }}</div>
                                    <div class="text-sm font-extrabold text-green-700 mt-1">${{ number_format($opp->value_cents / 100, 2) }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $opp->contact->full_name ?? 'No Contact' }}</div>
                                    
                                    <!-- Stage advance buttons -->
                                    <form action="{{ route('opportunities.move', $opp) }}" method="POST" class="mt-3 pt-2 border-t flex justify-between items-center">
                                        @csrf
                                        <select name="crm_stage_id" onchange="this.form.submit()" class="text-xs border-gray-300 rounded p-1 w-full bg-gray-50">
                                            @foreach ($stages as $s)
                                                <option value="{{ $s->id }}" {{ $s->id === $stage->id ? 'selected' : '' }}>
                                                    &rarr; {{ $s->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            @empty
                                <div class="text-xs text-gray-400 text-center py-4">No deals in stage</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
