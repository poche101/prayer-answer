<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ucfirst($type ?? 'Praise') }} Report | Early Morning Prayer & Answer</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script src="https://unpkg.com/lucide@latest"></script>
    @vite('resources/css/app.css')

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        select option { color: #000000; background-color: #ffffff; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 1.5rem; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-950 text-slate-200 selection:bg-indigo-500/30 font-sans antialiased overflow-x-hidden">

    <div class="min-h-screen flex items-center justify-center p-4 sm:p-8 relative overflow-hidden">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-cyan-600/10 blur-[120px] rounded-full"></div>

        <main class="w-full max-w-2xl z-10" x-data="reportForm()">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 mb-4">
                    <img src="/images/lz5.png" alt="Logo" class="w-10 h-10 object-contain">
                </div>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight bg-gradient-to-b from-white to-slate-400 bg-clip-text text-transparent">
                    Early Morning Prayer <span class="block">& Answer Meeting {{ ucfirst($type ?? 'Praise') }} Report</span>
                </h1>
                <p class="text-slate-500 mt-3 text-sm uppercase tracking-[0.2em]">Celz5 Documentation System</p>
            </div>

            <form @submit.prevent="submitReport" class="glass-card p-8 md:p-10 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <div class="space-y-3 md:col-span-2">
                        <label class="report-stat-label flex items-center gap-2 text-sm font-semibold text-indigo-300">
                            <i data-lucide="layers" class="w-4 h-4 text-indigo-400"></i> Select Group
                        </label>
                        <select
                            x-model="formData.group"
                            @change="updateChurches()"
                            required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all appearance-none cursor-pointer text-slate-200"
                        >
                            <option value="" class="text-black">Choose a group...</option>
                            <template x-for="(churches, group) in data" :key="group">
                                <option :value="group" x-text="group" class="text-black"></option>
                            </template>
                        </select>
                    </div>

                    <div class="space-y-3 md:col-span-2" x-show="formData.group" x-transition.opacity x-cloak>
                        <label class="report-stat-label flex items-center gap-2 text-sm font-semibold text-indigo-300">
                            <i data-lucide="church" class="w-4 h-4 text-indigo-400"></i> Select Church
                        </label>
                        <select
                            x-model="formData.church"
                            required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all appearance-none cursor-pointer text-slate-200"
                        >
                            <option value="" class="text-black">Choose your local church...</option>
                            <template x-for="church in availableChurches" :key="church">
                                <option :value="church" x-text="church" class="text-black"></option>
                            </template>
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="report-stat-label flex items-center gap-2 text-sm font-semibold text-indigo-300">
                            <i data-lucide="calendar" class="w-4 h-4 text-indigo-400"></i> Meeting Date
                        </label>
                        <input
                            type="date"
                            x-model="formData.meeting_date"
                            required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all text-slate-200"
                        >
                    </div>

                    <div class="space-y-3">
                        <label class="report-stat-label flex items-center gap-2 text-sm font-semibold text-indigo-300">
                            <i data-lucide="users" class="w-4 h-4 text-indigo-400"></i> Total Attendance
                        </label>
                        <input
                            type="number"
                            x-model="formData.attendance"
                            required
                            min="1"
                            placeholder="e.g. 45"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all text-slate-200"
                        >
                    </div>

                    <div class="space-y-3 md:col-span-2">
                        <label class="report-stat-label flex items-center gap-2 text-sm font-semibold text-indigo-300">
                            <i data-lucide="link" class="w-4 h-4 text-indigo-400"></i> Prayer Link
                        </label>
                        <input
                            type="url"
                            x-model="formData.prayer_link"
                            required
                            placeholder="https://kingsconference.org/..."
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all placeholder:text-slate-600 text-slate-200"
                        >
                    </div>

                    <div class="space-y-3 md:col-span-2">
                        <label class="report-stat-label flex items-center gap-2 text-sm font-semibold text-indigo-300">
                            <i data-lucide="message-square-quote" class="w-4 h-4 text-indigo-400"></i> Testimony (Optional)
                        </label>
                        <textarea
                            x-model="formData.testimony"
                            rows="4"
                            placeholder="Share your testimony here..."
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all placeholder:text-slate-600 text-slate-200 resize-none"
                        ></textarea>
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="group relative w-full bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-4 rounded-xl shadow-xl shadow-indigo-500/20 transition-all flex items-center justify-center gap-2 overflow-hidden"
                >
                    <span class="relative z-10" x-text="loading ? 'Submitting...' : 'Submit {{ ucfirst($type ?? 'Praise') }} Report'"></span>
                    <i data-lucide="chevron-right" x-show="!loading" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                </button>
            </form>

            <p class="text-center text-slate-600 text-xs mt-8 tracking-widest uppercase">
                &copy; 2026 Prayer & Answer Report
            </p>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        function reportForm() {
            return {
                loading: false,
                reportType: '{{ $type ?? 'praise' }}',
                availableChurches: [],
                formData: {
                    group: '',
                    church: '',
                    prayer_link: '',
                    attendance: '',
                    meeting_date: new Date().toISOString().split('T')[0],
                    testimony: ''
                },
                data: {
                    "CE LEKKI GROUP": ["PEARL GROUP", "HAVEN OF GRACE", "LIGHT HOUSE", "LAMBANO GROUP", "HOUSE OF GRACE HAVEN", "HAVEN OF LOVE", "SUNRISE CHURCH", "AUTARKIS FELLOWSHIP", "ASP GROUP", "EVER INCREASING GRACE", "GRACE GROUP", "EMPYREAN SUNSHINE", "EMERALD GROUP", "CHILDREN CHURCH", "JOY FELLOWSHIP", "LORDS AND KINGS", "TEENS CHURCH", "FAVOURED FELLOWSHIP"],
                    "CE TEDO GROUP": ["CE TEDO", "CE FIDISO", "CE MIRACLE AVENUE", "CE CORNER BUS-STOP", "CE ATLANTIC ESTATE", "CE ABIJO GRA"],
                    "CE LAGOS ISLAND GROUP": ["CE LAGOS ISLAND", "CE ADENIJI ADELE", "CE TINUBU SQUARE", "CE PELEWURA"],
                    "CE ALASIA GROUP": ["CE OLOGUNFE", "CE OGIDAN", "CE IWEREKU", "CE ELESEKAN", "CE OKO-ADDO (CHARIS CENTER)", "CE NEW ABIJO", "CE GBETU", "CE MONASTREY ROAD", "CE EKO-AKETE", "CE ALASIA", "CE LEWU", "CE BOLONPELU"],
                    "CE LEKKI FREE TRADE ZONE GROUP": ["CE LEKKI TOWN CHURCH", "CE LIMITLESS CHURCH", "CE ELEKO CHURCH", "CE TIYE CHURCH", "CE IDIROKO CHURCH"],
                    "CE VICTORIA ISLAND GROUP": ["CE VICTORIA ISLAND CHURCH 1", "CE VICTORIA ISLAND CHURCH 2", "CE ADETOKUNBO ADEMOLA", "CE SAKA TINUBU", "CE TIAMIYU SAVAGE", "CE TAKWA BAY", "CE TOMARO", "CE MANAGER", "CE SAGBOKOJI", "CE ITUNAGAN", "CE AGALA 1", "CE AGALA 2", "CE IGBOLOGUN(SNAKE ISLAND)"],
                    "CE YOUTH GROUP": ["CE LEKKI YOUTH CHURCH", "CE IKOYI 2 YOUTH CHURCH", "CE VICTORIA ISLAND YOUTH CHURCH", "CE AJAH YOUTH CHURCH", "CE OGOMBO YOUTH CHURCH", "CE BADORE YOUTH CHURCH", "CE LAKOWE YOUTH CHURCH", "CE CHARIS CENTER YOUTH CHURCH", "CE ONOSA YOUTH CHURCH", "CE AWOYAYA YOUTH CHURCH", "CE GBETU YOUTH CHURCH", "CE BOGIJE YOUTH CHURCH", "CE EPUTU YOUTH CHURCH", "CE MOBIL ROAD YOUTH CHURCH", "CE GLORIOUS LIGHT YOUTH CHURCH", "CE CHEVRON YOUTH CHURCH"],
                    "CE IKOYI 1 GROUP": ["CE IKOYI", "CE GLOVER", "CE AWOLOWO", "CE PARKVIEW"],
                    "CE ONISHON GROUP": ["CE ONISHON", "CE BOGIJE", "CE ORIBANWA 1", "CE ADEBA", "CE OGUNFAYO", "CE ORIBANWA 2"],
                    "CE AJIWE GROUP": ["CE AJIWE", "CE ALAGUNTAN", "CE ILAJE", "CE THOMAS ESTATE", "CE OWONIKOKO", "CE ATLANTIC ESTATE", "CE AGBATIONIKA LANE", "CE ILANLA CHURCH"],
                    "CE LEKKI PHASE 1 GROUP": ["CE ADMIRALTY, LEKKI", "CE BDE, LEKKI PHASE 1", "CE SPRING, IKATE"],
                    "CE AJAH GROUP": ["CE AJAH GROUP CHURCH", "CE ADDO ROAD CHURCH", "CE BERGER ROAD CHURCH", "CE AJAH SUNRISE CHURCH", "OKEIRANLA CHURCH", "AJAH LANGUAGE CHURCH", "MIMSHACK 2 CHURCH, BADORE"],
                    "CE CHEVRON GROUP": ["CHEVRON GROUP CHURCH", "CE CHEVRON 2 IKOTA", "CE CHEVRON 3 ALPHA BEACH", "CE ORCHID ROAD", "CE AGUNGI", "CE KINGS WEALTH"],
                    "CE IKOYI 2 GROUP": ["CE AWOLOWO ROAD", "CE DOLPHIN ESTATES", "CE OSHODI", "MOSALSHI SERVICE CENTER", "SURA SERVICE CENTER"],
                    "CE MOBIL ROAD GROUP": ["CE MOBIL ROAD CHURCH", "CE OKUN AJAH", "CE OKUN MOPOL", "CE LATEST BASE"],
                    "CE EPUTU GROUP": ["CE OASIS OF GRACE", "CE AWOYAYA", "CE IMEDU", "CE GLOBAL", "CE PARAPO", "CE EPUTU 2", "CE LABORA", "CE GARRISON", "CE GENESIS"],
                    "CE KAJOLA GROUP": ["CE KAJOLA", "CE IBEJU-AGBE", "CE ONOSA", "CE SHAPATI", "CE IGBOJIA", "CE DESA 1", "CE DESA2", "CE AIYETEJU", "CE BABA ADISA", "CE IGANDO", "CE ALAHUN"],
                    "CE EPE GROUP": ["CE EPE CENTRAL", "CE IRAYE", "CE TEMU", "CE EREDO", "CE KETU OMU IJEBU", "CE PAPA", "CE MARINA", "CE ODOMOLA", "CE OKEGUN", "CE IGBODU", "CE SHALA", "CE AGBOWA", "CE MOJODA"],
                    "CE OBALENDE GROUP": ["CE LEWIS STREET"],
                    "CE OWODE-BADORE GROUP": ["CE OWODE", "CE BADORE", "CE SEASIDE", "CE LANGBASA"],
                    "CE OGOMBO GROUP": ["CE OGOMBO ROAD 1", "CE OGOMBO 2", "CE TERRA ANNEX", "CE NEWTOWN 1", "CE NEWTOWN 2", "CE OGOMBO CENTRAL", "CE OGOMBO YORUBA LANGUAGE CHURCH"],
                    "CE ABIJO SUBGROUP": ["CE ABIJO", "CE MAJEK"]
                },

                updateChurches() {
                    this.availableChurches = this.formData.group ? this.data[this.formData.group] : [];
                    this.formData.church = '';
                },

                async submitReport() {
                    this.loading = true;
                    try {
                        // Dynamically choose endpoint based on reportType
                        const endpoint = this.reportType === 'praise' ? '/api/submit-praise' : '/api/submit-prayer';

                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const result = await response.json();

                        if (response.ok) {
                            Toastify({
                                text: `${this.reportType.charAt(0).toUpperCase() + this.reportType.slice(1)} Report Submitted!`,
                                duration: 5000,
                                gravity: "top",
                                position: "right",
                                style: { background: "linear-gradient(to right, #4f46e5, #06b6d4)" }
                            }).showToast();

                            this.formData = {
                                group: '',
                                church: '',
                                prayer_link: '',
                                attendance: '',
                                meeting_date: new Date().toISOString().split('T')[0],
                                testimony: ''
                            };
                        } else {
                            const errorMsg = result.errors ? Object.values(result.errors).flat().join(' ') : (result.message || "Submission failed");
                            throw new Error(errorMsg);
                        }
                    } catch (error) {
                        Toastify({
                            text: "Error: " + error.message,
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            style: { background: "#ef4444" }
                        }).showToast();
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
