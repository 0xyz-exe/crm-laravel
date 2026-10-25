<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('CRM Sales & Deals Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Metric Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Leads</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ $metrics['total_leads'] }}</div>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Open Deals</div>
                    <div class="mt-2 text-2xl font-bold text-indigo-600">{{ $metrics['open_opportunities'] }}</div>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pipeline Value</div>
                    <div class="mt-2 text-2xl font-bold text-blue-600">${{ number_format($metrics['pipeline_value_cents'] / 100, 2) }}</div>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Won Revenue</div>
                    <div class="mt-2 text-2xl font-bold text-green-600">${{ number_format($metrics['won_value_cents'] / 100, 2) }}</div>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending Tasks</div>
                    <div class="mt-2 text-2xl font-bold text-amber-600">{{ $metrics['pending_activities_count'] }}</div>
                </div>
            </div>

            <!-- Pipeline Funnel Summary -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-900">Sales Funnel: {{ $pipeline->name }}</h3>
                    <a href="{{ route('opportunities.index') }}" class="text-sm text-indigo-600 hover:underline">Full Kanban Board &rarr;</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                    @foreach ($stages as $stage)
                        <div class="bg-gray-50 p-4 rounded border border-gray-200 text-center">
                            <div class="text-xs font-bold text-gray-600 uppercase">{{ $stage->name }}</div>
                            <div class="mt-2 text-xl font-extrabold text-gray-900">{{ $stage->opportunities->count() }} Deals</div>
                            <div class="mt-1 text-xs text-indigo-600 font-semibold">
                                ${{ number_format($stage->opportunities->sum('value_cents') / 100, 0) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pending Activities / Tasks -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-900">Follow-up Activities & Reminders</h3>
                    <a href="{{ route('activities.index') }}" class="text-sm text-indigo-600 hover:underline">Schedule Activity &rarr;</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($pendingActivities as $act)
                        <div class="py-3 flex justify-between items-center text-sm">
                            <div>
                                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                                    @if($act->type === 'call') bg-blue-100 text-blue-800
                                    @elseif($act->type === 'meeting') bg-purple-100 text-purple-800
                                    @elseif($act->type === 'task') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $act->type }}
                                </span>
                                <span class="ml-2 font-semibold text-gray-900">{{ $act->subject }}</span>
                                <span class="text-gray-500 text-xs ml-2">Due: {{ $act->due_date ? $act->due_date->format('M d, Y') : 'Today' }}</span>
                            </div>
                            <form action="{{ route('activities.complete', $act) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-xs text-green-700 hover:text-green-900 font-bold border border-green-300 rounded px-2 py-1">Mark Done &check;</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 py-3">No pending tasks or calls.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
