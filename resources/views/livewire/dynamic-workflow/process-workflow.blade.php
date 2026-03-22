<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-tasks text-indigo-600"></i>Pending Applications
                </h3>
            </div>
            @if(count($requests) > 0)
                @foreach($requests as $req)
                <div wire:click="viewDetails({{ $req->id }})" class="bg-white rounded-xl shadow-lg border-l-4 {{ $req->status == 'pending' ? 'border-amber-400' : 'border-emerald-400' }} p-5 cursor-pointer hover:shadow-2xl transition-all {{ $selectedRequest && $selectedRequest->id == $req->id ? 'ring-2 ring-indigo-500' : '' }}">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">{{ $req->module->module_name }}</span>
                        <div class="flex flex-col items-end">
                        </div>
                    </div>
                    <h5 class="font-bold text-gray-900 mb-1">Ref ID: {{ $req->ref_id }}</h5>
                </div>
                @endforeach
            @else
                <div class="p-12 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 text-center text-gray-400">
                    <i class="fas fa-check-circle text-4xl mb-3 text-emerald-300"></i>
                    <p>No pending workflow actions found for your role.</p>
                </div>
            @endif
        </div>

        <!-- ⚙️ (RIGHT) ACTION PANEL -->
        <div class="space-y-6">
            @if($selectedRequest)
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100 animate__animated animate__fadeInRight">
                <div class="bg-dark px-8 py-5 flex items-center justify-between text-white border-b border-gray-700">
                    <div class="flex flex-col">
                        <h5 class="font-black uppercase tracking-widest text-xs">Requested Changes</h5>
                    </div>
                    <button wire:click="$set('selectedRequest', null)" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
                </div>

                <div class="p-8 space-y-8">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Proposed Data Changes</h4>
                    
                    <div class="space-y-4">
                        @foreach($selectedRequest->new_data as $field => $newValue)
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <label class="block text-xs font-black text-indigo-600 uppercase mb-2">{{ str_replace('_', ' ', $field) }}</label>
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <span class="text-xs text-gray-400 block mb-1 font-bold">Old Value</span>
                                    <div class="px-3 py-2 bg-red-50 text-red-600 rounded text-sm font-medium border border-red-100">{{ $selectedRequest->old_data[$field] ?? 'N/A' }}</div>
                                </div>
                                <div class="px-2 text-indigo-400"><i class="fas fa-long-arrow-alt-right fa-lg"></i></div>
                                <div class="flex-1">
                                    <span class="text-xs text-gray-400 block mb-1 font-bold">New Value (To Update)</span>
                                    <div class="px-3 py-2 bg-emerald-50 text-emerald-700 rounded text-sm font-bold border border-emerald-100">{{ $newValue }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="space-y-3 pt-6 border-top border-gray-100">
                        <label class="block text-sm font-bold text-gray-700 uppercase">Action Remark</label>
                        <textarea wire:model="remark" class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-100 rounded-xl focus:bg-white focus:border-indigo-500 outline-none transition-all h-24" placeholder="Enter your decision remark..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-6">
                        <button wire:click="processAction('approve')" class="col-span-1 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl shadow-xl transition-all active:scale-95 uppercase tracking-tighter shadow-emerald-100-md">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ $selectedRequest->step->is_final_step ? 'Process' : 'Approve' }}
                        </button>

                        <button wire:click="processAction('reject')" class="col-span-2 py-4 bg-red-100 text-red-600 font-bold rounded-xl hover:bg-red-200 transition-all transition-all active:scale-95 uppercase">
                             Cancel
                        </button>
                    </div>
                </div>
            </div>
            @else
            <div class="h-96 bg-gray-100 border-2 border-dashed border-gray-300 rounded-2xl flex flex-col items-center justify-center text-gray-400 text-center">
                <i class="fas fa-mouse-pointer text-5xl mb-4"></i>
                <h4 class="font-bold text-gray-500">Selection Required</h4>
                <p class="max-w-xs text-sm">Select a pending request from the left list to review data and take action.</p>
            </div>
            @endif
        </div>
    </div>
</div>
