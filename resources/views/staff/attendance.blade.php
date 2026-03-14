<style>
    /* ── Blurry arrow button ── */
    .blur-btn {
        border: none;
        border-radius: 12px;
        background: rgba(112, 112, 112, 0.612);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
    }

    .blur-btn:hover {
        background: rgba(108, 108, 108, 0.458);
        transform: scale(1.05);
    }

    /* ── Status pill ── */
    .status-pill {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 10px;
        border: 1.5px solid #bbf7d0;
        background: #f0fdf4;
        transition: background 0.2s, border-color 0.2s;
        user-select: none;
        white-space: nowrap;
    }

    @media (min-width: 400px) {
        .status-pill {
            gap: 8px;
            padding: 8px 14px;
        }
    }

    .status-pill.absent {
        border-color: #fecaca;
        background: #fef2f2;
    }

    .status-pill.checked-out {
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .status-indicator {
        position: relative;
        width: 8px;
        height: 8px;
        flex-shrink: 0;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #16a34a;
        position: absolute;
        inset: 0;
    }

    .status-dot.absent {
        background: #dc2626;
    }

    .status-dot.out {
        background: #2563eb;
    }

    .status-ring {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        border: 2px solid #16a34a;
        position: absolute;
        inset: 0;
        animation: ring-pulse 2s ease-out infinite;
        opacity: 0;
    }

    .status-ring.absent {
        border-color: #dc2626;
    }

    .status-ring.out {
        border-color: #2563eb;
        animation: none;
    }

    @keyframes ring-pulse {
        0% {
            transform: scale(1);
            opacity: 0.7;
        }

        100% {
            transform: scale(2.6);
            opacity: 0;
        }
    }

    .status-label-sub {
        font-size: 9px;
        font-weight: 500;
        color: #4ade80;
        line-height: 1;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    @media (min-width: 400px) {
        .status-label-sub {
            font-size: 10px;
        }
    }

    .status-pill.absent .status-label-sub {
        color: #f87171;
    }

    .status-pill.checked-out .status-label-sub {
        color: #60a5fa;
    }

    .status-label-main {
        font-size: 11px;
        font-weight: 700;
        color: #15803d;
        line-height: 1;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    @media (min-width: 400px) {
        .status-label-main {
            font-size: 12px;
        }
    }

    .status-pill.absent .status-label-main {
        color: #b91c1c;
    }

    .status-pill.checked-out .status-label-main {
        color: #1d4ed8;
    }

    /* ── Action button ── */
    .checkin-btn {
        width: 100%;
        padding: 14px 16px;
        border-radius: 12px;
        border: none;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.2s, filter 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        letter-spacing: -0.1px;
    }

    @media (min-width: 400px) {
        .checkin-btn {
            padding: 16px;
            font-size: 16px;
        }
    }

    .checkin-btn.in {
        background: #16a34a;
        color: #fff;
        box-shadow: 0 4px 16px rgba(22, 163, 74, 0.25);
    }

    .checkin-btn.out {
        background: #dc2626;
        color: #fff;
        box-shadow: 0 4px 16px rgba(220, 38, 38, 0.22);
    }

    .checkin-btn.done {
        background: #e5e7eb;
        color: #9ca3af;
        cursor: default;
    }

    .checkin-btn:not(.done):hover {
        transform: translateY(-1px);
        filter: brightness(1.07);
    }

    .checkin-btn:not(.done):active {
        transform: translateY(0);
    }

    /* ── Stat cards ── */
    .stat-card {
        background: #f9fafb;
        border-radius: 12px;
        padding: 12px 8px;
        text-align: center;
        border: 1.5px solid #f3f4f6;
        transition: border-color 0.2s, box-shadow 0.15s;
    }

    @media (min-width: 400px) {
        .stat-card {
            padding: 14px 10px;
        }
    }

    .stat-card:hover {
        border-color: #e0e3ef;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    /* ── Time rows ── */
    .time-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .time-row:last-child {
        border-bottom: none;
    }

    /* ── Calendar ── */
    .cal-day {
        aspect-ratio: 1;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 600;
        transition: transform 0.1s;
    }

    @media (min-width: 360px) {
        .cal-day {
            font-size: 11px;
            border-radius: 7px;
        }
    }

    @media (min-width: 400px) {
        .cal-day {
            font-size: 12px;
            border-radius: 8px;
        }
    }

    .cal-day:hover {
        transform: scale(1.08);
    }

    .cal-day.present {
        background: #dcfce7;
        color: #15803d;
    }

    .cal-day.absent {
        background: #fee2e2;
        color: #b91c1c;
    }

    .cal-day.late {
        background: #fef3c7;
        color: #92400e;
    }

    .cal-day.today {
        outline: 2px solid #2563eb;
        outline-offset: 1px;
    }

    .cal-day.future {
        background: #f9fafb;
        color: #d1d5db;
    }

    .cal-day.empty {
        background: transparent;
    }

    /* ── Drawer ── */
    #drawer {
        position: fixed;
        top: 70px;
        left: 0;
        height: 100%;
        background: white;
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
        overflow-y: auto;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 40;
        /* Full-width on very small screens, capped at 24rem on larger */
        width: 100%;
    }

    @media (min-width: 420px) {
        #drawer {
            width: 380px;
        }
    }

    @media (min-width: 640px) {
        #drawer {
            width: 400px;
        }
    }

    #drawerBackdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 39;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    #drawerBackdrop.open {
        display: block;
        opacity: 1;
    }
</style>


{{-- ── TRIGGER BUTTON ── --}}
<div class="arrow-main">
    <button class="blur-btn fixed left-2 top-1/2 -translate-y-1/2 text-white px-1 py-5 md:py-6 rounded-lg shadow-lg z-30"
        onclick="openDrawer()" aria-label="Open attendance drawer">
        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#333">
            <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z" />
        </svg>
    </button>
</div>

{{-- ── BACKDROP ── --}}
<div id="drawerBackdrop" onclick="closeDrawer()"></div>

{{-- ── ATTENDANCE DRAWER ── --}}
<div id="drawer">

    <div class="p-4 pb-12">

        {{-- HEADER --}}
        <header class="flex justify-between items-start gap-3 mb-5">
            <div class="min-w-0">
                <h1 class="text-lg sm:text-xl font-extrabold text-gray-800 leading-tight">
                    Hello, <span class="user-name"></span> 👋
                </h1>
                <p class="restroName text-xs sm:text-sm text-gray-500 font-medium mt-0.5 truncate">loading…</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                {{-- Status pill --}}
                <div class="status-pill absent" id="statusPill">
                    <div class="status-indicator">
                        <div class="status-dot absent" id="statusDot"></div>
                        <div class="status-ring absent" id="statusRing"></div>
                    </div>
                    <div>
                        <div class="status-label-sub" id="statusSub">Status</div>
                        <div class="status-label-main" id="statusMain">Not In</div>
                    </div>
                </div>
                {{-- Close button --}}
                <button type="button" onclick="closeDrawer()"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-1.5 transition flex-shrink-0">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </header>

        {{-- DATE + ACTION CARD --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4">
            <div class="flex justify-between items-center mb-4 sm:mb-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Today's Date</p>
                    <p class="text-xs sm:text-sm font-bold text-gray-800 mt-0.5" id="todayDate">—</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Live Time</p>
                    <p class="text-xs sm:text-sm font-bold text-gray-800 mt-0.5 tabular-nums" id="liveClock">--:--:--
                    </p>
                </div>
            </div>
            <button class="checkin-btn in" id="mainBtn" onclick="handleAttendance()">
                <span id="btnIcon">→</span>
                <span id="btnLabel">Clock In</span>
            </button>
        </div>

        {{-- WORKING HOURS --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5 mb-4">
            <h3 class="font-extrabold text-gray-800 text-sm sm:text-base mb-0.5">Today's Working Time</h3>
            <p class="text-xs text-gray-400 mb-3 sm:mb-4">Your shift log for today</p>
            <div class="time-row">
                <span class="text-xs sm:text-sm text-gray-500">Check In</span>
                <span class="text-xs sm:text-sm font-bold text-gray-800" id="checkInDisplay">--:--</span>
            </div>
            <div class="time-row">
                <span class="text-xs sm:text-sm text-gray-500">Check Out</span>
                <span class="text-xs sm:text-sm font-bold text-gray-800" id="checkOutDisplay">--:--</span>
            </div>
            <div class="time-row">
                <span class="text-xs sm:text-sm font-semibold text-gray-600">Total Hours</span>
                <span class="text-xs sm:text-sm font-extrabold text-blue-600" id="totalHours">0.00 hrs</span>
            </div>
        </div>

        {{-- MONTHLY SUMMARY --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5 mb-4">
            <div class="flex justify-between items-start gap-2 mb-3 sm:mb-4">
                <div>
                    <h3 class="font-extrabold text-gray-800 text-sm sm:text-base">This Month</h3>
                    <p class="text-xs text-gray-400" id="monthLabel">—</p>
                </div>
                <span
                    class="text-xs font-bold text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-full whitespace-nowrap flex-shrink-0">
                    <span id="workingDaysCount">0</span> working days
                </span>
            </div>
            <div class="grid grid-cols-3 gap-2 sm:gap-3 text-center">
                <div class="stat-card">
                    <p class="text-xs text-gray-500 mb-1">Present</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-gray-800" id="presentCount">0</p>
                    <p class="text-xs text-green-600 font-semibold mt-1" id="presentPct">0%</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs text-gray-500 mb-1">Late</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-yellow-500" id="lateCount">0</p>
                    <p class="text-xs text-yellow-500 font-semibold mt-1" id="latePct">0%</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs text-gray-500 mb-1">Absent</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-red-500" id="absentCount">0</p>
                    <p class="text-xs text-red-500 font-semibold mt-1" id="absentPct">0%</p>
                </div>
            </div>
        </div>

        {{-- CALENDAR --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5">
            <h3 class="font-extrabold text-gray-800 text-sm sm:text-base mb-3 sm:mb-4">Attendance Calendar</h3>
            <div class="flex flex-wrap gap-2 sm:gap-3 text-xs font-semibold mb-3 sm:mb-4">
                <span class="flex items-center gap-1">
                    <span
                        class="w-2.5 h-2.5 rounded-sm bg-green-100 inline-block border border-green-200"></span>Present
                </span>
                <span class="flex items-center gap-1">
                    <span
                        class="w-2.5 h-2.5 rounded-sm bg-yellow-100 inline-block border border-yellow-200"></span>Late
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-2.5 h-2.5 rounded-sm bg-red-100 inline-block border border-red-200"></span>Absent
                </span>
            </div>
            {{-- Day headers --}}
            <div class="grid grid-cols-7 gap-1 mb-1">
                @foreach (['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $day)
                    <div class="text-center text-[10px] font-bold text-gray-400 pb-1">{{ $day }}</div>
                @endforeach
            </div>
            <div class="grid grid-cols-7 gap-1" id="calGrid"></div>
        </div>

    </div>
</div>


<script>
    const restro_url = localStorage.getItem('restro_url');

    // ── Drawer open/close ─────────────────────────────────────────
    function openDrawer() {
        document.getElementById('drawer').style.transform = 'translateX(0)';
        document.getElementById('drawerBackdrop').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        document.getElementById('drawer').style.transform = 'translateX(-100%)';
        document.getElementById('drawerBackdrop').classList.remove('open');
        document.body.style.overflow = '';
    }
    // Close on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeDrawer();
    });

    // ── Mock history ──────────────────────────────────────────────
    function buildMockHistory() {
        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth();
        const today = now.getDate();
        const history = {};
        const statuses = ['present', 'present', 'present', 'present', 'late', 'present', 'absent', 'present', 'present',
            'late', 'present', 'present'
        ];
        let si = 0;
        for (let d = 1; d < today; d++) {
            const date = new Date(year, month, d);
            if (date.getDay() !== 0 && date.getDay() !== 6) {
                const key = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                history[key] = statuses[si % statuses.length];
                si++;
            }
        }
        return history;
    }

    // ── State ─────────────────────────────────────────────────────
    const todayKey = `mt-att-${new Date().toISOString().slice(0,10)}`;
    let att = JSON.parse(localStorage.getItem(todayKey)) || {
        checkIn: null,
        checkOut: null,
        totalHours: 0
    };
    const mockHistory = buildMockHistory();
    const now = new Date();

    // ── Clock ─────────────────────────────────────────────────────
    function tick() {
        document.getElementById('liveClock').textContent = new Date().toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
    setInterval(tick, 1000);
    tick();

    // ── Date labels ───────────────────────────────────────────────
    document.getElementById('todayDate').textContent = now.toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    });
    document.getElementById('monthLabel').textContent = now.toLocaleDateString('en-US', {
        month: 'long',
        year: 'numeric'
    });

    // ── Render UI ─────────────────────────────────────────────────
    function renderUI() {
        const btn = document.getElementById('mainBtn');
        const pill = document.getElementById('statusPill');
        const dot = document.getElementById('statusDot');
        const ring = document.getElementById('statusRing');
        const sub = document.getElementById('statusSub');
        const main = document.getElementById('statusMain');

        document.getElementById('checkInDisplay').textContent = att.checkIn || '--:--';
        document.getElementById('checkOutDisplay').textContent = att.checkOut || '--:--';
        document.getElementById('totalHours').textContent = att.totalHours.toFixed(2) + ' hrs';

        if (!att.checkIn) {
            pill.className = 'status-pill absent';
            dot.className = 'status-dot absent';
            ring.className = 'status-ring absent';
            sub.textContent = 'Status';
            main.textContent = 'Not In';
            btn.className = 'checkin-btn in';
            document.getElementById('btnIcon').textContent = '→';
            document.getElementById('btnLabel').textContent = 'Clock In';
            btn.disabled = false;
        } else if (!att.checkOut) {
            pill.className = 'status-pill';
            dot.className = 'status-dot';
            ring.className = 'status-ring';
            sub.textContent = 'Shift';
            main.textContent = 'On Duty';
            btn.className = 'checkin-btn out';
            document.getElementById('btnIcon').textContent = '✓';
            document.getElementById('btnLabel').textContent = 'Clock Out';
            btn.disabled = false;
        } else {
            pill.className = 'status-pill checked-out';
            dot.className = 'status-dot out';
            ring.className = 'status-ring out';
            sub.textContent = 'Shift';
            main.textContent = 'Complete';
            btn.className = 'checkin-btn done';
            document.getElementById('btnIcon').textContent = '✓';
            document.getElementById('btnLabel').textContent = 'Shift Completed';
            btn.disabled = true;
        }

        renderStats();
        renderCalendar();
    }

    // ── Attendance action ─────────────────────────────────────────
    async function handleAttendance() {
        const now = new Date();
        let url = '',
            method = '',
            message = '';

        if (!att.checkIn) {
            url = `/api/v1/staff/${restro_url}/attendance/check-in`;
            method = 'POST';
            att.checkIn = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
            message = '✅ Clocked in — have a great shift!';
        } else if (!att.checkOut) {
            url = `/api/v1/staff/${restro_url}/attendance/check-out`;
            method = 'POST';
            const [time, period] = att.checkIn.split(' ');
            const [h, m] = time.split(':').map(Number);
            const d = new Date();
            let hrs = h;
            if (period === 'PM' && h !== 12) hrs += 12;
            if (period === 'AM' && h === 12) hrs = 0;
            d.setHours(hrs, m, 0, 0);
            att.totalHours = Math.max(0, (now - d) / 3600000);
            att.checkOut = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
            message = '👋 Clocked out · ' + att.totalHours.toFixed(2) + ' hrs worked';
        }

        try {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            const data = await res.json();
            if (!data.success) {
                showToast(data.message || 'Something went wrong ❌', 'error');
                return;
            }
            localStorage.setItem(todayKey, JSON.stringify(att));
            showToast(message, 'success');
            renderUI();
        } catch (err) {
            console.error(err);
            showToast('Something went wrong ❌', 'error');
        }
    }

    // ── Stats ─────────────────────────────────────────────────────
    function renderStats() {
        const history = {
            ...mockHistory
        };
        const tk = new Date().toISOString().slice(0, 10);
        if (att.checkIn) {
            const [time, period] = att.checkIn.split(' ');
            const [h] = time.split(':').map(Number);
            let hr = h;
            if (period === 'PM' && h !== 12) hr += 12;
            if (period === 'AM' && h === 12) hr = 0;
            history[tk] = hr >= 10 ? 'late' : 'present';
        }
        const vals = Object.values(history);
        const present = vals.filter(v => v === 'present').length;
        const late = vals.filter(v => v === 'late').length;
        const absent = vals.filter(v => v === 'absent').length;
        const total = vals.length || 1;

        document.getElementById('presentCount').textContent = present;
        document.getElementById('lateCount').textContent = late;
        document.getElementById('absentCount').textContent = absent;
        document.getElementById('presentPct').textContent = Math.round(present / total * 100) + '%';
        document.getElementById('latePct').textContent = Math.round(late / total * 100) + '%';
        document.getElementById('absentPct').textContent = Math.round(absent / total * 100) + '%';
        document.getElementById('workingDaysCount').textContent = total;
    }

    // ── Calendar ──────────────────────────────────────────────────
    function renderCalendar() {
        const history = {
            ...mockHistory
        };
        const tk = new Date().toISOString().slice(0, 10);
        if (att.checkIn) {
            const [time, period] = att.checkIn.split(' ');
            const [h] = time.split(':').map(Number);
            let hr = h;
            if (period === 'PM' && h !== 12) hr += 12;
            if (period === 'AM' && h === 12) hr = 0;
            history[tk] = hr >= 10 ? 'late' : 'present';
        }

        const grid = document.getElementById('calGrid');
        const year = now.getFullYear();
        const month = now.getMonth();
        const today = now.getDate();
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        let html = '';
        for (let i = 0; i < firstDay; i++) html += `<div class="cal-day empty"></div>`;

        for (let d = 1; d <= daysInMonth; d++) {
            const key = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const status = history[key];
            const isToday = d === today;
            const isFuture = d > today;
            let cls = 'cal-day ';
            let title = '';
            if (isFuture) {
                cls += 'future';
            } else if (status === 'present') {
                cls += 'present';
                title = 'Present';
            } else if (status === 'late') {
                cls += 'late';
                title = 'Late';
            } else if (status === 'absent') {
                cls += 'absent';
                title = 'Absent';
            } else {
                cls += 'future';
            }
            if (isToday) cls += ' today';
            html += `<div class="${cls}" title="${title || 'Day ' + d}">${d}</div>`;
        }
        grid.innerHTML = html;
    }

    // ── Init ──────────────────────────────────────────────────────
    renderUI();

    // If there is no attendance, then it opens up the attendance drawer.
    if (!localStorage.getItem(todayKey)) {
        openDrawer()
    }
</script>
