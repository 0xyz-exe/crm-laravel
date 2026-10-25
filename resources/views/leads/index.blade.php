<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leads Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 text-sm text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Add Lead Form -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4">Capture New Lead</h3>
                    <form action="{{ route('leads.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Prospect Name</label>
                            <input type="text" name="name" class="mt-1 block w-full border-gray-300 rounded text-sm" placeholder="Johnathan Smith" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Email Address</label>
                            <input type="email" name="email" class="mt-1 block w-full border-gray-300 rounded text-sm" placeholder="jsmith@enterprise.com">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Phone</label>
                            <input type="text" name="phone" class="mt-1 block w-full border-gray-300 rounded text-sm" placeholder="+1 (555) 000-1122">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Lead Source</label>
                            <select name="source" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                                <option value="website">Inbound Website</option>
                                <option value="referral">Referral / Network</option>
                                <option value="cold_call">Outbound Cold Call</option>
                                <option value="social">Social Media (LinkedIn/X)</option>
                                <option value="advertisement">Paid Advertisement</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Notes / Context</label>
                            <textarea name="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded text-sm" placeholder="Interested in Enterprise plan demo..."></textarea>
                        </div>
                        <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded text-sm">+ Save Lead</button>
                    </form>
                </div>

                <!-- Leads Table -->
                <div class="col-span-2 bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-900">Prospects & Leads</h3>
                        <div class="flex space-x-2 text-xs">
                            <a href="{{ route('leads.index') }}" class="px-2 py-1 bg-gray-100 rounded">All</a>
                            <a href="{{ route('leads.index', ['status' => 'new']) }}" class="px-2 py-1 bg-blue-50 text-blue-700 rounded">New</a>
                            <a href="{{ route('leads.index', ['status' => 'qualified']) }}" class="px-2 py-1 bg-green-50 text-green-700 rounded">Qualified</a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                                <tr>
                                    <th class="px-4 py-2 text-left">Prospect</th>
                                    <th class="px-4 py-2 text-left">Source</th>
                                    <th class="px-4 py-2 text-center">Status</th>
                                    <th class="px-4 py-2 text-right">Convert to Deal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($leads as $lead)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-gray-900">{{ $lead->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $lead->email }} | {{ $lead->phone ?? 'No phone' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600 uppercase">{{ $lead->source }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $lead->status === 'qualified' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ ucfirst($lead->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if ($lead->status !== 'qualified')
                                                <form action="{{ route('leads.convert', $lead) }}" method="POST" class="inline-flex items-center space-x-1">
                                                    @csrf
                                                    <input type="text" name="deal_title" value="{{ $lead->name }} - Initial Deal" class="w-36 p-1 text-xs border-gray-300 rounded" required>
                                                    <input type="number" step="0.01" name="deal_value" value="5000" class="w-20 p-1 text-xs border-gray-300 rounded" required>
                                                    <button type="submit" class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-bold">Convert &rarr;</button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400">Converted</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-center text-gray-500">No leads recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $leads->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
