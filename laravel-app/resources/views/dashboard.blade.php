<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- filter form -->
            <form method="GET" class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap gap-4 items-end">
                    <select name="status" class="border rounded px-3 py-2">
                        <option value="">All status</option>
                        @foreach(['pending','in_progress','completed'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ str_replace('_',' ', ucfirst($s)) }}</option>
                        @endforeach
                    </select>
                    <select name="priority" class="border rounded px-3 py-2">
                        <option value="">All priority</option>
                        @foreach(['low','medium','high'] as $p)
                            <option value="{{ $p }}" {{ request('priority') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                    <select name="assignee" class="border rounded px-3 py-2">
                        <option value="">Any assignee</option>
                        @foreach(\App\Models\User::orderBy('name')->get() as $u)
                            <option value="{{ $u->id }}" {{ request('assignee') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="due_before" value="{{ request('due_before') }}" class="border rounded px-3 py-2" placeholder="Due before" />
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
                    <a href="{{ route('dashboard') }}" class="text-gray-500 underline">Reset</a>
                </div>
            </form>

            <!-- KPIs with drill-down links -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php $q = request()->query(); @endphp
                <a href="{{ route('tasks.index', $q) }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-gray-50">
                    <div class="text-lg font-medium text-gray-700">Total Tasks</div>
                    <div class="text-3xl font-bold mt-2">{{ $totals['total'] }}</div>
                </a>
                <a href="{{ route('tasks.index', ['status'=>'pending']) }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-gray-50">
                    <div class="text-lg font-medium text-gray-700">Pending</div>
                    <div class="text-3xl font-bold mt-2">{{ $totals['pending'] }}</div>
                </a>
                <a href="{{ route('tasks.index', ['status'=>'completed']) }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-gray-50">
                    <div class="text-lg font-medium text-gray-700">Completed</div>
                    <div class="text-3xl font-bold mt-2">{{ $totals['completed'] }}</div>
                </a>
                <a href="{{ route('tasks.index', ['status'=>'pending','due_before'=>now()->toDateString()]) }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-gray-50">
                    <div class="text-lg font-medium text-gray-700">Overdue</div>
                    <div class="text-3xl font-bold mt-2">{{ $totals['overdue'] }}</div>
                </a>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Tasks by Status</h3>
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Priority Distribution</h3>
                    <canvas id="priorityChart"></canvas>
                </div>
            </div>

            <!-- Recent activity placeholder -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-xl font-medium mb-4">Recent Activity</h3>
                <p class="text-gray-600">(activity feed coming soon)</p>
            </div>
        </div>
    </div>

    {{-- charts script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            const priorityCtx = document.getElementById('priorityChart').getContext('2d');

            const statusData = {!! json_encode($statusData) !!};
            const priorityData = {!! json_encode($priorityData) !!};

            new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(statusData),
                    datasets: [{
                        data: Object.values(statusData),
                        backgroundColor: ['#3b82f6', '#10b981', '#f97316', '#ef4444'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            new Chart(priorityCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(priorityData),
                    datasets: [{
                        label: 'Count',
                        data: Object.values(priorityData),
                        backgroundColor: '#6366f1'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
</x-app-layout>
