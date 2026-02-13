<x-layouts.app>

<div class="max-w-4xl mx-auto py-8">

    <div class="bg-white shadow-xl rounded-2xl p-6 space-y-4">

        <!-- Step 1 -->
        <a href="{{ route('role-rank-management') }}"
           class="block border rounded-xl p-4 transition
           {{ request()->routeIs('role-rank-management') ? 'border-purple-600 bg-purple-50' : 'border-gray-200 hover:bg-gray-50' }}">

            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 flex items-center justify-center rounded-full
                    {{ request()->routeIs('role-rank-management') ? 'bg-purple-600 text-white' : 'bg-gray-300 text-white' }}">
                    1
                </div>

                <div>
                    <h3 class="font-semibold text-lg">Role Rank Management</h3>
                    <p class="text-sm text-gray-500">
                        Configure role hierarchy and define ranking order for workflow approval.
                    </p>
                </div>
            </div>
        </a>

        <!-- Step 2 -->
        <a href="{{ route('create-steps') }}"
           class="block border rounded-xl p-4 transition
           {{ request()->routeIs('create-steps') ? 'border-purple-600 bg-purple-50' : 'border-gray-200 hover:bg-gray-50' }}">

            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 flex items-center justify-center rounded-full
                    {{ request()->routeIs('create-steps') ? 'bg-purple-600 text-white' : 'bg-gray-300 text-white' }}">
                    2
                </div>

                <div>
                    <h3 class="font-semibold text-lg">Create Workflow Steps</h3>
                    <p class="text-sm text-gray-500">
                        Define the number of steps required in the workflow process.
                    </p>
                </div>
            </div>
        </a>

        <!-- Step 3 -->
        <a href="{{ route('assign-workflow') }}"
           class="block border rounded-xl p-4 transition
           {{ request()->routeIs('assign-workflow') ? 'border-purple-600 bg-purple-50' : 'border-gray-200 hover:bg-gray-50' }}">

            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 flex items-center justify-center rounded-full
                    {{ request()->routeIs('assign-workflow') ? 'bg-purple-600 text-white' : 'bg-gray-300 text-white' }}">
                    3
                </div>

                <div>
                    <h3 class="font-semibold text-lg">Assign Role to Steps</h3>
                    <p class="text-sm text-gray-500">
                        Assign specific roles to each workflow step for structured approvals.
                    </p>
                </div>
            </div>
        </a>

        <!-- Step 4 -->
        <a href="{{ route('duplicate-checks') }}"
           class="block border rounded-xl p-4 transition
           {{ request()->routeIs('duplicate-checks') ? 'border-purple-600 bg-purple-50' : 'border-gray-200 hover:bg-gray-50' }}">

            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 flex items-center justify-center rounded-full
                    {{ request()->routeIs('duplicate-checks') ? 'bg-purple-600 text-white' : 'bg-gray-300 text-white' }}">
                    4
                </div>

                <div>
                    <h3 class="font-semibold text-lg">Duplicate Check Configuration</h3>
                    <p class="text-sm text-gray-500">
                        Set rules to prevent duplicate entries in the system.
                    </p>
                </div>
            </div>
        </a>

        <!-- Step 5 -->
        <a href="{{ route('age-management') }}"
           class="block border rounded-xl p-4 transition
           {{ request()->routeIs('age-management') ? 'border-purple-600 bg-purple-50' : 'border-gray-200 hover:bg-gray-50' }}">

            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 flex items-center justify-center rounded-full
                    {{ request()->routeIs('age-management') ? 'bg-purple-600 text-white' : 'bg-gray-300 text-white' }}">
                    5
                </div>

                <div>
                    <h3 class="font-semibold text-lg">Age Management Configuration</h3>
                    <p class="text-sm text-gray-500">
                        Define age validation rules and eligibility conditions.
                    </p>
                </div>
            </div>
        </a>

    </div>

</div>

</x-layouts.app>
