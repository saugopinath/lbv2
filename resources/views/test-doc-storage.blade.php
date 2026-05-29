<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Storage API Test Sandbox</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="text-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-10">
            <span class="px-3 py-1 text-xs font-semibold text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 rounded-full uppercase tracking-wider">
                Developer Sandbox
            </span>
            <h1 class="mt-3 text-4xl font-bold tracking-tight bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                Document Storage API Tester
            </h1>
            <p class="mt-2 text-sm text-slate-400">
                Test upload, download, and delete operations with the Document Vault API in real-time.
            </p>
        </div>

        @if(session('success') || session('error'))
            <!-- Status Alerts -->
            <div class="mb-6 p-4 rounded-xl border {{ session('success') ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-300' : 'bg-rose-500/10 border-rose-500/20 text-rose-300' }}">
                <div class="flex items-center">
                    <span class="mr-2 text-xl">{{ session('success') ? '✓' : '⚠' }}</span>
                    <span class="font-medium">{{ session('success') ?: session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Column: Form -->
            <div class="space-y-6">
                <div class="glass-card rounded-2xl p-6 shadow-2xl">
                    <h2 class="text-lg font-semibold text-indigo-300 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        API Configuration
                    </h2>
                    
                    <form action="{{ route('test-doc-storage.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Base URL</label>
                            <input type="text" name="base_url" value="{{ $baseUrl }}" class="w-full bg-slate-900/50 border border-slate-700/50 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition-colors" placeholder="e.g. http://10.176.100.17:5000">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">App ID</label>
                                <input type="text" name="app_id" value="{{ $appId }}" class="w-full bg-slate-900/50 border border-slate-700/50 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition-colors" placeholder="e.g. 1">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Client Secret</label>
                                <input type="password" name="client_secret" value="{{ $clientSecret }}" class="w-full bg-slate-900/50 border border-slate-700/50 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition-colors" placeholder="UUID String">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Created By (User ID)</label>
                            <input type="number" name="created_by" value="{{ old('created_by', 1) }}" class="w-full bg-slate-900/50 border border-slate-700/50 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition-colors">
                        </div>

                        <hr class="border-slate-800 my-4">

                        <h2 class="text-sm font-semibold text-slate-300 mb-2">Select Document to Upload</h2>
                        
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-700 border-dashed rounded-xl cursor-pointer bg-slate-900/20 hover:bg-slate-900/40 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="text-xs text-slate-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p id="file-chosen" class="text-xs text-indigo-400 font-medium mt-1">No file chosen</p>
                                </div>
                                <input type="file" name="file" id="file-input" class="hidden" onchange="document.getElementById('file-chosen').textContent = this.files[0] ? this.files[0].name : 'No file chosen'">
                            </label>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-2.5 px-4 rounded-lg shadow-lg hover:shadow-indigo-500/20 active:translate-y-px transition-all">
                            Upload Document
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Results & Logs -->
            <div class="space-y-6">
                <!-- If a Document ID is active in session -->
                @php
                    $activeDocId = session('documentId') ?: request('documentId');
                @endphp
                
                @if($activeDocId)
                    <div class="glass-card rounded-2xl p-6 border-indigo-500/30">
                        <span class="inline-block px-2 py-0.5 text-[10px] font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-full uppercase tracking-wider mb-2">
                            Active Session File
                        </span>
                        <h3 class="text-md font-semibold text-slate-200 mb-1">Uploaded Document ID:</h3>
                        <code class="block bg-slate-950 p-2 rounded text-indigo-300 font-mono text-xs select-all mb-4 break-all">{{ $activeDocId }}</code>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Download Link -->
                            <a href="{{ route('test-doc-storage.download', ['id' => $activeDocId, 'base_url' => $baseUrl, 'app_id' => $appId, 'client_secret' => $clientSecret]) }}" 
                               class="flex items-center justify-center bg-emerald-600 hover:bg-emerald-500 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors text-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download Test
                            </a>

                            <!-- Delete Form -->
                            <form action="{{ route('test-doc-storage.delete', ['id' => $activeDocId]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document from storage?')">
                                @csrf
                                <input type="hidden" name="base_url" value="{{ $baseUrl }}">
                                <input type="hidden" name="app_id" value="{{ $appId }}">
                                <input type="hidden" name="client_secret" value="{{ $clientSecret }}">
                                <button type="submit" class="w-full flex items-center justify-center bg-rose-600/90 hover:bg-rose-500 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors text-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Delete Test
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Request/Response Payload Inspector -->
                @if(session('request_log') || session('upload_response'))
                    <div class="glass-card rounded-2xl p-6 space-y-4">
                        <h2 class="text-md font-semibold text-slate-200">REST Inspector</h2>
                        
                        @if(session('request_log'))
                            <div>
                                <span class="text-xs font-semibold text-slate-400 uppercase">Request Call details</span>
                                <div class="bg-slate-950/80 p-3 rounded-lg mt-1 border border-slate-800 font-mono text-xs text-indigo-300 overflow-x-auto space-y-1">
                                    <div><span class="text-slate-500">URL:</span> {{ session('request_log')['url'] }}</div>
                                    <div><span class="text-slate-500">Method:</span> {{ session('request_log')['method'] ?? 'POST' }}</div>
                                    <div><span class="text-slate-500">Headers:</span> {{ json_encode(session('request_log')['headers']) }}</div>
                                    @if(isset(session('request_log')['file_name']))
                                        <div><span class="text-slate-500">File attached:</span> {{ session('request_log')['file_name'] }} ({{ session('request_log')['file_size'] }})</div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(session('upload_response'))
                            <div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-semibold text-slate-400 uppercase">Response Body</span>
                                    @if(session('response_status'))
                                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded {{ session('response_status') >= 200 && session('response_status') < 300 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                            HTTP {{ session('response_status') }}
                                        </span>
                                    @endif
                                </div>
                                <pre class="bg-slate-950/80 p-3 rounded-lg mt-1 border border-slate-800 font-mono text-xs text-amber-300/90 overflow-x-auto max-h-60">{{ json_encode(session('upload_response'), JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="glass-card rounded-2xl p-6 text-center text-slate-500 flex flex-col items-center justify-center h-48 border-dashed">
                        <svg class="w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm">Perform an upload to see HTTP request/response inspector logs here.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer Info -->
        <div class="mt-12 text-center text-xs text-slate-500">
            Document Vault Test Sandbox &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
