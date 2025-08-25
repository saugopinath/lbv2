  <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
      <!-- Top Controls: Search, Per Page -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div class="flex gap-2">
              <x-button.primary wire:click="export"
                  class="flex items-center gap-2 px-1.5 py-1 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-5 h-5" fill="currentColor">
                      <path
                          d="M128 128C128 92.7 156.7 64 192 64L341.5 64C358.5 64 374.8 70.7 386.8 82.7L493.3 189.3C505.3 201.3 512 217.6 512 234.6L512 512C512 547.3 483.3 576 448 576L192 576C156.7 576 128 547.3 128 512L128 128zM336 122.5L336 216C336 229.3 346.7 240 360 240L453.5 240L336 122.5zM292 330.7C284.6 319.7 269.7 316.7 258.7 324C247.7 331.3 244.7 346.3 252 357.3L291.2 416L252 474.7C244.6 485.7 247.6 500.6 258.7 508C269.8 515.4 284.6 512.4 292 501.3L320 459.3L348 501.3C355.4 512.3 370.3 515.3 381.3 508C392.3 500.7 395.3 485.7 388 474.7L348.8 416L388 357.3C395.4 346.3 392.4 331.4 381.3 324C370.2 316.6 355.4 319.6 348 330.7L320 372.7L292 330.7z" />
                  </svg>
              </x-button.primary>
          </div>

          <div class="flex justify-end">
              <select wire:model.live="perPage"
                  class="w-48 px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm">
                  <option value="5">5 per page</option>
                  <option value="10">10 per page</option>
                  <option value="25">25 per page</option>
                  <option value="50">50 per page</option>
              </select>
          </div>

          <div class="flex justify-end w-full md:w-auto">
              <div class="relative w-full md:w-64">
                  <input type="text" wire:model.live="search" placeholder="Search..."
                      class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm">
                  <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
              </div>
          </div>
      </div>

      <!-- Data Table -->
      <div class="overflow-x-auto border rounded-lg shadow-sm">
          <table class="min-w-full text-sm text-gray-700 text-center">
              <thead class="bg-violet-800 text-xs uppercase py-3 text-white">
                  <tr>
                      <th class="py-3">Application ID</th>
                       <th class="py-3">Beneficiary ID</th>
                       <th class="py-3">Applicant Name</th>
                       <th class="py-3">Mobile No</th>
                       <th class="py-3">Bank AC No</th>
                       <th class="py-3">IFSC</th>
                       <th class="py-3">Branch</th>
                       <th class="py-3">Bank Name</th>
                       <th class="py-3">Type</th>
                  </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white y-overflow-y-auto">
                  @forelse($rows as $row)
                      <tr class="hover:bg-gray-50">
                         
                              <td class="py-3">{{ $row->sourceable->application_id ?? 'N/A' }}</td>
                              <td class="py-3">{{ $row->sourceable->beneficiary_id ?? 'N/A' }}</td>
                              <td class="py-3">{{ $row->sourceable->full_name ?? 'N/A' }}</td>
                              <td class="py-3">{{ $row->sourceable->mobile_no ?? 'N/A'}}</td>
                              <td class="py-3">{{ $row->sourceable->bank->bank_account_number ?? 'N/A' }}</td>
                              <td class="py-3">{{ $row->sourceable->bank->ifsc ?? 'N/A' }}</td>
                              <td class="py-3">{{ $row->sourceable->bank->ifscMaster->branch ?? 'N/A' }}</td>
                              <td class="py-3">{{ $row->sourceable->bank->ifscMaster->bankmaster->name ?? 'N/A'}}</td>
                              <td class="py-3">{{ class_basename($row->sourceable_type)}}</td>
                          
                      </tr>
                  @empty
                      <tr>
                          <td class="text-center py-4">No data found.</td>
                      </tr>
                  @endforelse
              </tbody>
          </table>
      </div>

      <!-- Pagination -->
      <div class="flex flex-col md:flex-row items-center justify-between mt-4 space-y-2 md:space-y-0">
          <div class="text-sm text-gray-600">
              Showing {{ $rows->firstItem() ?? 0 }} to {{ $rows->lastItem() ?? 0 }} of {{ $rows->total() }} entries
          </div>
          <div>{{ $rows->links() }}</div>
      </div>
  </div>
