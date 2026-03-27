<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Praise Reports</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite('resources/css/app.css')
    <style>
        [x-cloak] { display: none !important; }
        .glass-panel { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

        .shimmer {
            background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.05) 50%, rgba(255,255,255,0) 100%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-200 font-sans antialiased min-h-screen" x-data="dashboard()">

    <div class="fixed bottom-6 right-6 z-[150] flex flex-col gap-3">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-10 opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 :class="toast.type === 'success' ? 'border-emerald-500/50 bg-emerald-500/10' : 'border-red-500/50 bg-red-500/10'"
                 class="flex items-center gap-3 px-6 py-4 rounded-2xl border backdrop-blur-xl shadow-2xl min-w-[300px]">
                <i :class="toast.type === 'success' ? 'text-emerald-400' : 'text-red-400'"
                   class="w-5 h-5" :data-lucide="toast.type === 'success' ? 'check-circle' : 'alert-circle'"></i>
                <span class="text-sm font-medium text-white" x-text="toast.message"></span>
            </div>
        </template>
    </div>

    <div x-show="showEditModal"
         class="fixed inset-0 z-[110] flex items-center justify-center px-4 overflow-hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md" @click="showEditModal = false"></div>
        <div class="glass-panel w-full max-w-lg p-8 rounded-[2.5rem] relative z-10 border-white/10 shadow-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100">

            <h3 class="text-2xl font-bold mb-6 flex items-center gap-3">
                <i data-lucide="edit-3" class="text-indigo-400"></i> Edit Report
            </h3>

            <div class="space-y-4">
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-slate-500 font-black mb-2 block ml-1">Church Name</label>
                    <input type="text" x-model="editForm.church" class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 focus:ring-2 focus:ring-indigo-500/40 outline-none transition-all">
                </div>
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-slate-500 font-black mb-2 block ml-1">Group Name</label>
                    <input type="text" x-model="editForm.group" class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 focus:ring-2 focus:ring-indigo-500/40 outline-none transition-all">
                </div>
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-slate-500 font-black mb-2 block ml-1">Meeting Date</label>
                    <input type="date" x-model="editForm.meeting_date" class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 focus:ring-2 focus:ring-indigo-500/40 outline-none transition-all text-white">
                </div>
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-slate-500 font-black mb-2 block ml-1">Report Link</label>
                    <input type="url" x-model="editForm.prayer_link" class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 focus:ring-2 focus:ring-indigo-500/40 outline-none transition-all">
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button @click="showEditModal = false" class="flex-1 px-6 py-4 rounded-2xl bg-white/5 hover:bg-white/10 font-bold transition-all border border-white/10 text-slate-300">Cancel</button>
                <button @click="confirmUpdate" class="flex-1 px-6 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold transition-all shadow-lg shadow-indigo-900/40">Update Changes</button>
            </div>
        </div>
    </div>

    <div x-show="showDeleteModal"
         class="fixed inset-0 z-[110] flex items-center justify-center px-4 overflow-hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>

        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md" @click="showDeleteModal = false"></div>

        <div class="glass-panel w-full max-w-md p-8 rounded-[2.5rem] relative z-10 border-white/10 shadow-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="scale-90 opacity-0"
             x-transition:enter-end="scale-100 opacity-100">

            <div class="flex flex-col items-center text-center">
                <div class="w-20 h-20 rounded-full bg-red-500/10 flex items-center justify-center text-red-500 mb-6 border border-red-500/20">
                    <i data-lucide="trash-2" class="w-10 h-10"></i>
                </div>
                <h3 class="text-2xl font-bold mb-2">Confirm Deletion</h3>
                <p class="text-slate-400 text-sm mb-8 leading-relaxed">
                    This action is permanent. You are about to delete the report for <span class="text-white font-semibold" x-text="reportToDelete?.church"></span>.
                </p>

                <div class="flex gap-4 w-full">
                    <button @click="showDeleteModal = false"
                            class="flex-1 px-6 py-4 rounded-2xl bg-white/5 hover:bg-white/10 font-bold transition-all border border-white/10 text-slate-300">
                        Cancel
                    </button>
                    <button @click="confirmDelete"
                            class="flex-1 px-6 py-4 rounded-2xl bg-red-600 hover:bg-red-500 text-white font-bold transition-all shadow-lg shadow-red-900/40">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="showTestimonyModal"
         class="fixed inset-0 z-[120] flex items-center justify-center px-4 overflow-hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        <div class="absolute inset-0 bg-slate-950/90 backdrop-blur-xl" @click="showTestimonyModal = false"></div>
        <div class="glass-panel w-full max-w-2xl p-10 rounded-[2.5rem] relative z-10 border-white/10 shadow-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100">

            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-white mb-1" x-text="selectedTestimony?.church"></h3>
                    <p class="text-xs font-black text-indigo-400 uppercase tracking-widest" x-text="selectedTestimony?.group"></p>
                </div>
                <button @click="showTestimonyModal = false" class="p-2 hover:bg-white/5 rounded-full transition-colors text-slate-500 hover:text-white">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <div class="bg-white/5 rounded-3xl p-6 border border-white/5 max-h-[50vh] overflow-y-auto">
                <p class="text-slate-300 leading-relaxed whitespace-pre-line text-lg" x-text="selectedTestimony?.testimony"></p>
            </div>

            <div class="mt-8 flex justify-end">
                <button @click="showTestimonyModal = false" class="px-8 py-3.5 rounded-2xl bg-white/5 hover:bg-white/10 font-bold transition-all border border-white/10">Close</button>
            </div>
        </div>
    </div>

    <nav class="border-b border-white/5 bg-slate-950/50 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-500/10 rounded-xl border border-indigo-500/20">
                    <img src="/images/lz5.png" class="w-6 h-6 object-contain" alt="Logo">
                </div>
                <span class="font-bold text-xl tracking-tight">Celz5 <span class="text-indigo-500">Early Morning Reports</span></span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-white transition-all hover:bg-white/5 px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-medium">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <template x-for="stat in stats" :key="stat.label">
                <div class="glass-panel p-8 rounded-[2rem] relative overflow-hidden group hover:border-white/20 transition-all cursor-default">
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <p class="text-slate-500 text-xs uppercase tracking-[0.2em] font-black mb-2" x-text="stat.label"></p>
                            <h3 class="text-4xl font-bold tracking-tight text-white" x-text="stat.value"></h3>
                        </div>
                        <div class="p-4 rounded-2xl bg-indigo-500/10 text-indigo-400 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-500">
                            <i :data-lucide="stat.icon" class="w-7 h-7"></i>
                        </div>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-[0.02] group-hover:opacity-[0.05] transition-opacity">
                         <i :data-lucide="stat.icon" class="w-32 h-32"></i>
                    </div>
                </div>
            </template>
        </div>

        <div class="flex flex-col lg:flex-row gap-4 mb-8">
            <div class="relative flex-grow">
                <i data-lucide="search" class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500"></i>
                <input type="text" x-model="search" @input="currentPage = 1" placeholder="Search church or group names..."
                       class="w-full bg-white/5 border border-white/10 rounded-[1.25rem] pl-14 pr-6 py-4 focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 outline-none transition-all placeholder:text-slate-600">
            </div>
            <div class="flex flex-wrap gap-4">
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"></i>
                    <input type="date" x-model="filterDate" @change="currentPage = 1"
                           class="bg-white/5 border border-white/10 rounded-[1.25rem] pl-12 pr-6 py-4 outline-none focus:ring-2 focus:ring-indigo-500/40 cursor-pointer text-white">
                </div>
                <button @click="resetFilters" class="glass-panel px-8 rounded-[1.25rem] hover:bg-white/10 transition-all text-sm font-bold text-slate-400 hover:text-white">
                    Reset
                </button>
                <button @click="fetchReports(true)" class="glass-panel px-6 rounded-[1.25rem] hover:bg-indigo-500/20 transition-all border-indigo-500/20 group">
                    <i data-lucide="refresh-cw" class="w-5 h-5 text-indigo-400 group-hover:rotate-180 transition-transform duration-500" :class="loading ? 'animate-spin' : ''"></i>
                </button>
            </div>
        </div>

        <div class="glass-panel rounded-[2.5rem] overflow-hidden relative shadow-2xl border-white/5">
            <div x-show="loading" class="absolute inset-0 bg-slate-950/40 backdrop-blur-sm z-20 flex items-center justify-center" x-cloak>
                <div class="flex flex-col items-center gap-4">
                    <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-xs font-black text-indigo-400 uppercase tracking-[0.3em]">Syncing Records</span>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[400px]">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-white/5 bg-white/[0.01]">
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.2em] text-slate-500 font-black">Group / Church</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.2em] text-slate-500 font-black">Attendance</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.2em] text-slate-500 font-black">Testimony</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.2em] text-slate-500 font-black">Meeting Date</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.2em] text-slate-500 font-black">Report Link</th>
                            <th class="px-8 py-6 text-xs uppercase tracking-[0.2em] text-slate-500 font-black text-right">Options</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="report in paginatedReports" :key="report.id">
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="font-bold text-white text-base" x-text="report.church"></div>
                                    <div class="text-[10px] text-indigo-400 font-black uppercase tracking-widest mt-1 opacity-70" x-text="report.group"></div>
                                </td>

                                <td class="px-8 py-6">
                                    <span class="text-emerald-500/80 font-bold" x-text="report.attendance"></span>
                                </td>

                                <td class="px-8 py-6">
                                    <p @click="openTestimony(report)" class="text-sm text-slate-400 max-w-xs truncate cursor-pointer hover:text-slate-200 transition-colors" x-text="report.testimony"></p>
                                </td>

                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2 text-sm text-slate-400">
                                        <i data-lucide="calendar-days" class="w-4 h-4 opacity-50"></i>
                                        <span x-text="formatDate(report.meeting_date)"></span>
                                    </div>
                                </td>

                                <td class="px-8 py-6">
                                    <a :href="report.prayer_link" target="_blank"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-500/5 text-indigo-400 hover:bg-indigo-500 hover:text-white transition-all text-xs font-bold border border-indigo-500/10">
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i> View Link
                                    </a>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="triggerEdit(report)"
                                                class="text-slate-600 hover:text-indigo-400 hover:bg-indigo-500/10 p-3 rounded-xl transition-all">
                                            <i data-lucide="edit-3" class="w-5 h-5"></i>
                                        </button>
                                        <button @click="triggerDelete(report)"
                                                class="text-slate-600 hover:text-red-500 hover:bg-red-500/10 p-3 rounded-xl transition-all">
                                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredReports.length === 0 && !loading">
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center opacity-40">
                                    <i data-lucide="database-zap" class="w-12 h-12 mb-4"></i>
                                    <p class="text-lg font-bold">No records found</p>
                                    <p class="text-sm">Try adjusting your filters or search keywords</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-6 border-t border-white/5 flex items-center justify-between bg-white/[0.01]">
                <div class="flex items-center gap-4">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest"
                          x-text="`Page ${currentPage} of ${totalPages}`"></span>
                    <div class="h-1 w-24 bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500 transition-all duration-500"
                             :style="`width: ${(currentPage / totalPages) * 100}%`"></div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button @click="prevPage" :disabled="currentPage === 1"
                            class="p-3 rounded-xl glass-panel hover:bg-white/10 disabled:opacity-10 transition-all">
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    </button>
                    <button @click="nextPage" :disabled="currentPage === totalPages"
                            class="p-3 rounded-xl glass-panel hover:bg-white/10 disabled:opacity-10 transition-all">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        function dashboard() {
            return {
                reports: [],
                loading: true,
                search: '',
                filterDate: '',
                currentPage: 1,
                pageSize: 8,
                toasts: [],

                // Modal States
                showDeleteModal: false,
                reportToDelete: null,
                showEditModal: false,
                showTestimonyModal: false,
                selectedTestimony: null,
                editForm: { id: null, church: '', group: '', meeting_date: '', prayer_link: '', attendance: 0, testimony: '' },

                async init() {
                    await this.fetchReports();
                    this.refreshIcons();
                },

                refreshIcons() {
                    setTimeout(() => lucide.createIcons(), 50);
                },

                formatDate(dateString) {
                    if(!dateString || dateString === '0000-00-00') return 'N/A';

                    try {
                        // Creating date in UTC to avoid timezone shift issues
                        const date = new Date(dateString);
                        if (isNaN(date.getTime())) return 'N/A';

                        return date.toLocaleDateString('en-US', {
                            month: 'short', day: 'numeric', year: 'numeric', timeZone: 'UTC'
                        });
                    } catch (e) {
                        return 'N/A';
                    }
                },

                addToast(message, type = 'success') {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    this.refreshIcons();
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 4000);
                },

                openTestimony(report) {
                    this.selectedTestimony = report;
                    this.showTestimonyModal = true;
                    this.refreshIcons();
                },

                async fetchReports(manual = false) {
                    this.loading = true;
                    try {
                        const response = await fetch('/api-v1/prayer-reports');
                        if (!response.ok) throw new Error();
                        const result = await response.json();
                        this.reports = result.data ? result.data : result;
                        if(manual) this.addToast('Database synchronized');
                    } catch (e) {
                        this.addToast('Connection failed. Please retry.', 'error');
                    }
                    this.loading = false;
                    this.refreshIcons();
                },

                // Update Logic
                triggerEdit(report) {
                    this.editForm = { ...report };
                    if (this.editForm.meeting_date) {
                        try {
                            this.editForm.meeting_date = new Date(this.editForm.meeting_date).toISOString().split('T')[0];
                        } catch(e) {
                            this.editForm.meeting_date = '';
                        }
                    }
                    this.showEditModal = true;
                    this.refreshIcons();
                },

                async confirmUpdate() {
                    this.loading = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const response = await fetch(`/api-v1/prayer-reports/${this.editForm.id}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.editForm)
                        });

                        if (response.ok) {
                            const updatedData = await response.json();
                            const index = this.reports.findIndex(r => r.id === this.editForm.id);
                            if (index !== -1) {
                                this.reports[index] = updatedData.data || updatedData;
                            }
                            this.addToast('Report updated successfully');
                            this.showEditModal = false;
                        } else {
                            throw new Error();
                        }
                    } catch (e) {
                        this.addToast('Failed to update record', 'error');
                    } finally {
                        this.loading = false;
                        this.refreshIcons();
                    }
                },

                // Delete Logic
                triggerDelete(report) {
                    this.reportToDelete = report;
                    this.showDeleteModal = true;
                    this.refreshIcons();
                },

                async confirmDelete() {
                    if(!this.reportToDelete) return;
                    const id = this.reportToDelete.id;
                    this.showDeleteModal = false;

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const response = await fetch(`/api-v1/prayer-reports/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });

                        if(response.ok) {
                            this.reports = this.reports.filter(r => r.id !== id);
                            this.addToast('Entry removed permanently');
                            if (this.paginatedReports.length === 0 && this.currentPage > 1) this.currentPage--;
                        } else {
                            throw new Error();
                        }
                    } catch (e) {
                        this.addToast('Unable to delete record', 'error');
                    } finally {
                        this.reportToDelete = null;
                        this.refreshIcons();
                    }
                },

                resetFilters() {
                    this.search = '';
                    this.filterDate = '';
                    this.currentPage = 1;
                    this.refreshIcons();
                },

                get filteredReports() {
                    return this.reports.filter(r => {
                        const matchesSearch = r.church.toLowerCase().includes(this.search.toLowerCase()) ||
                                              r.group.toLowerCase().includes(this.search.toLowerCase());

                        let matchesDate = true;
                        if (this.filterDate) {
                            const reportDate = new Date(r.meeting_date).toDateString();
                            const filterDate = new Date(this.filterDate).toDateString();
                            matchesDate = reportDate === filterDate;
                        }
                        return matchesSearch && matchesDate;
                    });
                },

                get paginatedReports() {
                    let start = (this.currentPage - 1) * this.pageSize;
                    return this.filteredReports.slice(start, start + this.pageSize);
                },

                get totalPages() {
                    return Math.ceil(this.filteredReports.length / this.pageSize) || 1;
                },

                nextPage() {
                    if(this.currentPage < this.totalPages) {
                        this.currentPage++;
                        this.refreshIcons();
                    }
                },

                prevPage() {
                    if(this.currentPage > 1) {
                        this.currentPage--;
                        this.refreshIcons();
                    }
                },

                get stats() {
                    const totalAttendance = this.reports.reduce((sum, r) => sum + (parseInt(r.attendance) || 0), 0);
                    const groups = [...new Set(this.reports.map(r => r.group))].length;

                    return [
                        { label: 'Cumulative Reports', value: this.reports.length, icon: 'file-text' },
                        { label: 'Reporting Groups', value: groups, icon: 'users' },
                        { label: 'Total Attendance', value: totalAttendance.toLocaleString(), icon: 'trending-up' }
                    ];
                }
            }
        }
    </script>
</body>
</html>
