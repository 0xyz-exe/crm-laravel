<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('CRM Activities & Tasks') }}
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
                <!-- Schedule Activity Form -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4">Log Activity / Task</h3>
                    <form action="{{ route('activities.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Type</label>
                            <select name="type" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                                <option value="call">Phone Call</option>
                                <option value="meeting">Client Meeting</option>
                                <option value="task">Follow-up Task</option>
                                <option value="note">Internal Note</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Subject</label>
                            <input type="text" name="subject" placeholder="Discovery call with CTO" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Associated Deal (Optional)</label>
                            <select name="crm_opportunity_id" class="mt-1 block w-full border-gray-300 rounded text-sm">
                                <option value="">-- None --</option>
                                @foreach ($opportunities as $opp)
                                    <option value="{{ $opp->id }}">{{ $opp->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Due Date</label>
                            <input type="date" name="due_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Description</label>
                            <textarea name="description" rows="2" class="mt-1 block w-full border-gray-300 rounded text-sm"></textarea>
                        </div>
                        <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded text-sm">+ Schedule Activity</button>
                    </form>
                </div>

                <!-- Activities List -->
                <div class="col-span-2 bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4">Activity Schedule</h3>
                    <div class="divide-y divide-gray-100">
                        @forelse ($activities as $act)
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
                                    @if ($act->opportunity)
                                        <span class="ml-2 text-xs text-indigo-600 font-medium">[{{ $act->opportunity->title }}]</span>
                                    @endif
                                    <div class="text-xs text-gray-500 mt-1">Due: {{ $act->due_date ? $act->due_date->format('M d, Y') : 'Today' }}</div>
                                </div>
                                <div>
                                    @if ($act->status === 'pending')
                                        <form action="{{ route('activities.complete', $act) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs text-green-700 hover:text-green-900 font-bold border border-green-300 rounded px-2 py-1">Complete &check;</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400 font-semibold">&check; Completed</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 py-3">No activities scheduled.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
