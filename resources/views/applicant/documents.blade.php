@extends('layouts.app', ['title' => 'My Requirements Vault - FindJobPh'])

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ uploadModalOpen: false, selectedTypeId: '', selectedTypeName: '' }">
    
    <!-- Vault Header & Readiness Meter -->
    <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-900/20">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/10 text-blue-200 backdrop-blur-xs mb-2">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    Philippine Verified Requirements Vault
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">My Requirements Vault</h1>
                <p class="text-xs sm:text-sm text-blue-100/90 mt-1 max-w-xl">
                    Store your pre-verified Philippine employment documents once. When you apply to jobs, your documents automatically satisfy employer prerequisites!
                </p>
            </div>

            <!-- Readiness Ring / Badge -->
            <div class="shrink-0 bg-white/10 backdrop-blur-md rounded-2xl p-4 text-center border border-white/10 min-w-[140px]">
                <div class="text-3xl font-black text-amber-300">{{ $completionRate }}%</div>
                <div class="text-xs text-blue-100 font-semibold mt-0.5">{{ $uploadedCount }} of {{ $totalTypes }} Uploaded</div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-6">
            <div class="w-full bg-white/20 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-amber-400 to-emerald-400 h-3 rounded-full transition-all duration-500" style="width: {{ $completionRate }}%"></div>
            </div>
        </div>
    </div>

    <!-- Documents Grid -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Document Credentials Checklist</h2>
                <p class="text-xs text-slate-500">Employers specify which of these documents are required for each job</p>
            </div>
            <a href="{{ route('jobs.feed') }}" class="text-xs font-bold text-blue-600 hover:underline">
                Browse Feed with Your Credentials &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($requirementTypes as $type)
                @php
                    $doc = $userDocuments->get($type->id);
                    $hasUploaded = $doc !== null;
                @endphp

                <div class="bg-white rounded-2xl border p-5 flex flex-col justify-between transition {{ $hasUploaded ? 'border-emerald-300 shadow-xs' : 'border-slate-200' }}">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 font-bold {{ $hasUploaded ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    @if($hasUploaded)
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-bold text-sm text-slate-900">{{ $type->name }}</h3>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        @if($type->is_default)
                                            <span class="inline-flex text-[10px] font-bold px-1.5 py-0.2 rounded bg-blue-50 text-blue-700 border border-blue-200">Standard</span>
                                        @endif
                                        <span class="text-[11px] text-slate-500">{{ $hasUploaded ? 'Uploaded & Active' : 'Not yet uploaded' }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($hasUploaded)
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    Verified
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                    Empty
                                </span>
                            @endif
                        </div>

                        <p class="text-xs text-slate-500 mt-3 leading-relaxed">{{ $type->description }}</p>

                        @if($hasUploaded)
                            <div class="mt-3 p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-slate-700 truncate max-w-[220px]">📎 {{ $doc->file_name }}</span>
                                    <span class="text-slate-400 font-medium text-[11px]">{{ $doc->formattedSize() }}</span>
                                </div>
                                @if($doc->notes)
                                    <p class="text-[11px] text-slate-500 italic">"{{ $doc->notes }}"</p>
                                @endif
                                <div class="text-[10px] text-slate-400 pt-1 border-t border-slate-200/60">
                                    Last updated: {{ $doc->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2 text-xs">
                        @if($hasUploaded)
                            <div class="flex items-center gap-2">
                                <a href="{{ $doc->getUrl() }}" target="_blank" class="font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    View File
                                </a>
                                <span>•</span>
                                <button type="button" @click="uploadModalOpen = true; selectedTypeId = '{{ $type->id }}'; selectedTypeName = '{{ addslashes($type->name) }}'"
                                    class="text-slate-600 hover:text-blue-600 font-semibold">
                                    Replace
                                </button>
                            </div>

                            <form action="{{ route('applicant.documents.delete', $doc) }}" method="POST" onsubmit="return confirm('Remove this document from your vault?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 font-semibold text-[11px]">
                                    Remove
                                </button>
                            </form>
                        @else
                            <button type="button" @click="uploadModalOpen = true; selectedTypeId = '{{ $type->id }}'; selectedTypeName = '{{ addslashes($type->name) }}'"
                                class="w-full py-2 px-3 rounded-xl font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition text-center flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Upload {{ $type->name }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Upload/Replace Modal -->
    <div x-show="uploadModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop: dark overlay without backdrop-filter blur bleed -->
        <div x-show="uploadModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/70 transition-opacity" 
             @click="uploadModalOpen = false"
             aria-hidden="true"></div>

        <!-- Centering Wrapper -->
        <div class="fixed inset-0 z-10 overflow-y-auto flex min-h-full items-center justify-center p-4 text-center">
            <!-- Modal Card (Crystal-clear, crisp, solid white, elevated, relative z-20) -->
            <div x-show="uploadModalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.stop
                 class="relative z-20 w-full max-w-lg p-6 overflow-hidden text-left bg-white shadow-2xl rounded-3xl border border-slate-100 transition-all">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-xl bg-blue-100 text-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Upload to Vault</h3>
                            <p class="text-xs text-slate-500" x-text="selectedTypeName"></p>
                        </div>
                    </div>
                    <button type="button" @click="uploadModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">&times;</button>
                </div>

                <form action="{{ route('applicant.documents.upload') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="requirement_type_id" :value="selectedTypeId">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Select Document File (PDF, JPG, PNG)</label>
                        <input type="file" name="document_file" required accept=".pdf,.jpg,.jpeg,.png,.webp"
                            class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 rounded-xl p-2 cursor-pointer bg-slate-50">
                        <p class="text-[11px] text-slate-500 mt-1">Maximum file size: 10MB.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Optional Reference / Document Number</label>
                        <input type="text" name="notes" placeholder="e.g. License ID: N01-22-334455"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-hidden focus:bg-white focus:border-blue-600">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="uploadModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition">
                            Save to Vault
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
