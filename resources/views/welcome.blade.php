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
        select option { color: #3d2f1f; background-color: #fdf8f2; }
        .glass-card {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(210, 185, 155, 0.35);
            border-radius: 1.5rem;
            box-shadow: 0 4px 32px rgba(160, 120, 70, 0.08), 0 1px 4px rgba(160,120,70,0.06);
        }
        [x-cloak] { display: none !important; }

        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0.4;
            cursor: pointer;
        }

        /* Center toast override */
        .toastify {
            left: 50% !important;
            right: auto !important;
            transform: translateX(-50%) !important;
        }
    </style>
</head>
<body class="font-sans antialiased overflow-x-hidden" style="background-color: #faf6ef; color: #3d2f1f;">

    {{-- Warm ambient blobs --}}
    <div style="position:fixed;inset:0;pointer-events:none;overflow:hidden;z-index:0;">
        <div style="position:absolute;top:-8%;left:-8%;width:45%;height:45%;background:radial-gradient(circle,rgba(214,174,120,0.18) 0%,transparent 70%);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-8%;right:-8%;width:45%;height:45%;background:radial-gradient(circle,rgba(188,145,100,0.14) 0%,transparent 70%);border-radius:50%;"></div>
        <div style="position:absolute;top:40%;left:55%;width:30%;height:30%;background:radial-gradient(circle,rgba(230,200,160,0.12) 0%,transparent 70%);border-radius:50%;"></div>
    </div>

    <div class="min-h-screen flex items-center justify-center p-4 sm:p-8 relative" style="z-index:1;">

        <main class="w-full max-w-2xl" x-data="reportForm()">

            {{-- Header --}}
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4" style="background:rgba(180,140,90,0.12);border:1px solid rgba(180,140,90,0.25);">
                    <img src="/images/lz5.png" alt="Logo" class="w-10 h-10 object-contain">
                </div>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight" style="color:#2c1f0f;">
                    Early Morning Prayer
                    <span class="block" style="color:#7a5230;">& Answer Meeting {{ ucfirst($type ?? 'Praise') }} Report</span>
                </h1>
            </div>

            {{-- Form card --}}
            <form @submit.prevent="submitReport" class="glass-card p-8 md:p-10 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    {{-- Group --}}
                    <div class="space-y-3 md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-semibold" style="color:#7a5230;">
                            <i data-lucide="layers" class="w-4 h-4" style="color:#b8894a;"></i> Select Group
                        </label>
                        <select
                            x-model="formData.group"
                            @change="updateChurches()"
                            required
                            class="w-full rounded-xl px-4 py-4 focus:outline-none transition-all appearance-none cursor-pointer"
                            style="background:rgba(250,246,239,0.8);border:1px solid rgba(180,140,90,0.3);color:#3d2f1f;box-shadow:inset 0 1px 3px rgba(160,120,70,0.06);"
                        >
                            <option value="" style="color:#9a7a55;">Choose a group...</option>
                            <template x-for="(churches, group) in data" :key="group">
                                <option :value="group" x-text="group"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Church --}}
                    <div class="space-y-3 md:col-span-2" x-show="formData.group" x-transition.opacity x-cloak>
                        <label class="flex items-center gap-2 text-sm font-semibold" style="color:#7a5230;">
                            <i data-lucide="church" class="w-4 h-4" style="color:#b8894a;"></i> Select Church
                        </label>
                        <select
                            x-model="formData.church"
                            required
                            class="w-full rounded-xl px-4 py-4 focus:outline-none transition-all appearance-none cursor-pointer"
                            style="background:rgba(250,246,239,0.8);border:1px solid rgba(180,140,90,0.3);color:#3d2f1f;box-shadow:inset 0 1px 3px rgba(160,120,70,0.06);"
                        >
                            <option value="" style="color:#9a7a55;">Choose your local church...</option>
                            <template x-for="church in availableChurches" :key="church">
                                <option :value="church" x-text="church"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Date --}}
                    <div class="space-y-3">
                        <label class="flex items-center gap-2 text-sm font-semibold" style="color:#7a5230;">
                            <i data-lucide="calendar" class="w-4 h-4" style="color:#b8894a;"></i> Meeting Date
                        </label>
                        <input
                            type="date"
                            x-model="formData.meeting_date"
                            required
                            class="w-full rounded-xl px-4 py-4 focus:outline-none transition-all"
                            style="background:rgba(250,246,239,0.8);border:1px solid rgba(180,140,90,0.3);color:#3d2f1f;box-shadow:inset 0 1px 3px rgba(160,120,70,0.06);"
                        >
                    </div>

                    {{-- Attendance --}}
                    <div class="space-y-3">
                        <label class="flex items-center gap-2 text-sm font-semibold" style="color:#7a5230;">
                            <i data-lucide="users" class="w-4 h-4" style="color:#b8894a;"></i> Total Attendance
                        </label>
                        <input
                            type="number"
                            x-model="formData.attendance"
                            required
                            min="1"
                            placeholder="e.g. 45"
                            class="w-full rounded-xl px-4 py-4 focus:outline-none transition-all"
                            style="background:rgba(250,246,239,0.8);border:1px solid rgba(180,140,90,0.3);color:#3d2f1f;box-shadow:inset 0 1px 3px rgba(160,120,70,0.06);"
                        >
                    </div>

                    {{-- Prayer link --}}
                    <div class="space-y-3 md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-semibold" style="color:#7a5230;">
                            <i data-lucide="link" class="w-4 h-4" style="color:#b8894a;"></i> Prayer Link
                        </label>
                        <input
                            type="url"
                            x-model="formData.prayer_link"
                            required
                            placeholder="https://kingsconference.org/..."
                            class="w-full rounded-xl px-4 py-4 focus:outline-none transition-all"
                            style="background:rgba(250,246,239,0.8);border:1px solid rgba(180,140,90,0.3);color:#3d2f1f;box-shadow:inset 0 1px 3px rgba(160,120,70,0.06);"
                        >
                    </div>

                    {{-- Testimony --}}
                    <div class="space-y-3 md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-semibold" style="color:#7a5230;">
                            <i data-lucide="message-square-quote" class="w-4 h-4" style="color:#b8894a;"></i> Testimony <span style="color:#b89a72;font-weight:400;">(Optional)</span>
                        </label>
                        <textarea
                            x-model="formData.testimony"
                            rows="4"
                            placeholder="Share your testimony here..."
                            class="w-full rounded-xl px-4 py-4 focus:outline-none transition-all resize-none"
                            style="background:rgba(250,246,239,0.8);border:1px solid rgba(180,140,90,0.3);color:#3d2f1f;box-shadow:inset 0 1px 3px rgba(160,120,70,0.06);"
                        ></textarea>
                    </div>
                </div>

                {{-- Submit button --}}
                <button
                    type="submit"
                    :disabled="loading"
                    class="group relative w-full font-semibold py-4 rounded-xl transition-all flex items-center justify-center gap-2 overflow-hidden"
                    style="background:#7a5230;color:#fdf8f2;box-shadow:0 4px 20px rgba(122,82,48,0.25);"
                    onmouseover="this.style.background='#8f6238'"
                    onmouseout="this.style.background='#7a5230'"
                >
                    <span class="relative z-10" x-text="loading ? 'Submitting...' : 'Submit {{ ucfirst($type ?? 'Praise') }} Report'"></span>
                    <i data-lucide="chevron-right" x-show="!loading" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <p class="text-center text-xs mt-8 tracking-widest uppercase" style="color:#c4a882;">
                &copy; 2026 Prayer & Answer Report
            </p>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        function showToast(text, background) {
            Toastify({
                text: text,
                duration: 5000,
                gravity: "top",
                position: "center",
                style: {
                    background: background,
                    borderRadius: "12px",
                    padding: "14px 28px",
                    fontSize: "15px",
                    fontWeight: "600",
                    boxShadow: "0 8px 32px rgba(0,0,0,0.14)",
                }
            }).showToast();
        }

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
                            showToast(
                                '✓  ' + this.reportType.charAt(0).toUpperCase() + this.reportType.slice(1) + ' Report Submitted Successfully!',
                                'linear-gradient(to right, #7a5230, #b8894a)'
                            );

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
                        showToast('✕  Error: ' + error.message, '#b84a4a');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>