document.addEventListener('DOMContentLoaded', function() {
    // === Elements ===
    const uploadBtn = document.getElementById('uploadTimetable');
    const uploadModal = document.getElementById('uploadModal');
    const classModal = document.getElementById('classModal');
    const closeModalBtns = document.querySelectorAll('.close-modal');
    const switchViewBtn = document.getElementById('switchView');
    const dailySchedule = document.querySelector('.daily-schedule');
    const weeklyTimetable = document.querySelector('.weekly-timetable');
    const currentDayEl = document.getElementById('current-day');

    // === State Management ===
    let currentView = 'daily'; // 'daily' or 'weekly'

    // === Helper Functions ===
    function showModal(modal) {
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideModal(modal) {
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // === Initial Setup ===
    function initializeViews() {
        if (!dailySchedule || !weeklyTimetable) return;
        
        dailySchedule.style.display = currentView === 'daily' ? 'block' : 'none';
        weeklyTimetable.style.display = currentView === 'weekly' ? 'block' : 'none';
        
        if (switchViewBtn) {
            switchViewBtn.querySelector('span').textContent = 
                currentView === 'daily' ? 'Week View' : 'Day View';
        }
    }

    // === Day Setup ===
    function setupDayNavigation() {
        if (!currentDayEl) return;
        
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const today = new Date();
        currentDayEl.textContent = days[today.getDay()];

        // Day navigation
        document.querySelectorAll('.day-nav-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const currentIndex = days.indexOf(currentDayEl.textContent);
                const direction = this.classList.contains('prev-day') ? -1 : 1;
                const nextIndex = (currentIndex + direction + 7) % 7;
                currentDayEl.textContent = days[nextIndex];
            });
        });
    }

    // === Modal Handling ===
    function setupModals() {
        // Close modals
        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const modal = this.closest('.modal');
                hideModal(modal);
            });
        });

        // Class blocks
        document.querySelectorAll('.class-card, .class-block').forEach(block => {
            block.addEventListener('click', function() {
                const title = this.querySelector('.class-title, .block-title')?.textContent || 'Class';
                const time = this.querySelector('.class-time, .block-time')?.textContent || '--:--';
                const location = this.querySelector('.class-location, .block-meta')?.textContent || 'Location not specified';

                if (classModal) {
                    document.getElementById('modalClassTitle').textContent = title;
                    document.getElementById('modalClassTime').textContent = time;
                    document.getElementById('modalClassLocation').textContent = location;
                    showModal(classModal);
                }
            });
        });

        // Close when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    hideModal(this);
                }
            });
        });
    }

    // === PDF Upload ===
    function setupFileUpload() {
        if (!uploadBtn || !uploadModal) return;

        uploadBtn.addEventListener('click', () => showModal(uploadModal));

        const pdfUpload = document.getElementById('timetablePDF');
        if (pdfUpload) {
            pdfUpload.addEventListener('change', function() {
                if (this.files?.[0]) {
                    console.log(`Uploaded: ${this.files[0].name}`);
                    setTimeout(() => hideModal(uploadModal), 1500);
                }
            });
        }

        const manualEntryBtn = document.getElementById('manualEntryBtn');
        if (manualEntryBtn) {
            manualEntryBtn.addEventListener('click', () => {
                alert('Manual entry form will open here.');
                hideModal(uploadModal);
            });
        }
    }

    // === View Switching ===
    function setupViewSwitching() {
        if (!switchViewBtn || !dailySchedule || !weeklyTimetable) return;

        switchViewBtn.addEventListener('click', function() {
            currentView = currentView === 'daily' ? 'weekly' : 'daily';
            
            dailySchedule.style.display = currentView === 'daily' ? 'block' : 'none';
            weeklyTimetable.style.display = currentView === 'weekly' ? 'block' : 'none';
            
            this.querySelector('span').textContent = 
                currentView === 'daily' ? 'Week View' : 'Day View';
        });
    }

    // === Week Navigation ===
    function setupWeekNavigation() {
        document.querySelectorAll('.week-nav-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                console.log(`Navigated to: ${this.textContent.trim()}`);
                // In a real app, this would fetch new week data
            });
        });
    }

    // === Initialize Everything ===
    function init() {
        setupDayNavigation();
        setupModals();
        setupFileUpload();
        setupViewSwitching();
        setupWeekNavigation();
        initializeViews();
    }

    init();
});