<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Contacts & Accounts') }}
            </h2>
            <a href="{{ route('contacts.export') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded text-xs font-semibold uppercase hover:bg-gray-700">
                Export to CSV &darr;
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 text-sm text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Add Contact Form -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4">Add Contact</h3>
                    <form action="{{ route('contacts.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">First Name</label>
                                <input type="text" name="first_name" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Last Name</label>
                                <input type="text" name="last_name" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Email</label>
                            <input type="email" name="email" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Phone</label>
                            <input type="text" name="phone" class="mt-1 block w-full border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Job Title</label>
                            <input type="text" name="job_title" placeholder="VP of Sales" class="mt-1 block w-full border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Company Account</label>
                            <select name="crm_company_id" class="mt-1 block w-full border-gray-300 rounded text-sm">
                                <option value="">-- No Company --</option>
                                @foreach ($companies as $comp)
                                    <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded text-sm">+ Save Contact</button>
                    </form>

                    <!-- Add Company -->
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Add Company / Account</h4>
                        <form action="{{ route('companies.store') }}" method="POST" class="space-y-2">
                            @csrf
                            <input type="text" name="name" placeholder="Acme International" class="block w-full border-gray-300 rounded text-sm" required>
                            <input type="text" name="industry" placeholder="SaaS / Healthcare / Logistics" class="block w-full border-gray-300 rounded text-sm">
                            <button type="submit" class="w-full py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded text-xs">+ Save Company</button>
                        </form>
                    </div>
                </div>

                <!-- Contacts Table -->
                <div class="col-span-2 bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4">Contact Directory</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                                <tr>
                                    <th class="px-4 py-2 text-left">Contact Name</th>
                                    <th class="px-4 py-2 text-left">Company</th>
                                    <th class="px-4 py-2 text-left">Job Title</th>
                                    <th class="px-4 py-2 text-left">Email / Phone</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($contacts as $c)
                                    <tr>
                                        <td class="px-4 py-3 font-bold text-gray-900">{{ $c->full_name }}</td>
                                        <td class="px-4 py-3 text-indigo-600">{{ $c->company->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $c->job_title ?? '—' }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-600">
                                            <div>{{ $c->email }}</div>
                                            <div>{{ $c->phone }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-center text-gray-500">No contacts recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $contacts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
