document.addEventListener('DOMContentLoaded', function() {
    // --- ELEMENT SELECTORS ---
    const switchViewBtn = document.getElementById('switchViewBtn');
    const dailyView = document.getElementById('dailyView');
    const weeklyView = document.getElementById('weeklyView');
    const dailyScheduleContainer = document.getElementById('dailyScheduleContainer');
    const currentDayEl = document.getElementById('current-day');
    const prevDayBtn = document.querySelector('.prev-day');
    const nextDayBtn = document.querySelector('.next-day');

    // --- DATA & STATE ---
    // Reads schedule data embedded in the timetable.php file by the server
    const scheduleDataEl = document.getElementById('schedule-data');
    const scheduleData = JSON.parse(scheduleDataEl.textContent || '{}');
    const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    let currentDayIndex = new Date().getDay(); // Start with today

    /**
     * Renders the schedule for a given day index (0=Sun, 1=Mon, etc.)
     * @param {number} dayIndex - The index of the day to display.
     */
    function renderDailySchedule(dayIndex) {
        if (!dailyScheduleContainer || !currentDayEl) return;

        // Clear previous content
        dailyScheduleContainer.innerHTML = '';
        currentDayEl.textContent = dayNames[dayIndex];
        
        const dayKey = dayIndex + 1; // Database uses 1-7 for DayOfWeek

        if (scheduleData[dayKey] && scheduleData[dayKey].length > 0) {
            scheduleData[dayKey].forEach(cls => {
                const card = document.createElement('div');
                card.className = 'class-block'; // Use the same class as the weekly view for consistency
                card.innerHTML = `
                    <div class="block-time">${new Date('1970-01-01T' + cls.TimeOnly).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                    <div class="block-title">${cls.Class_Name}</div>
                    <div class="block-meta">${cls.Venue_Name}</div>
                `;
                dailyScheduleContainer.appendChild(card);
            });
        } else {
            dailyScheduleContainer.innerHTML = '<p class="no-classes-msg">No classes scheduled for this day.</p>';
        }
    }

    /**
     * Sets up all event listeners for the page.
     */
    function initializeTimetable() {
        // --- View Switching Logic ---
        if (switchViewBtn && dailyView && weeklyView) {
            weeklyView.style.display = 'none'; // Default to daily view
            switchViewBtn.addEventListener('click', () => {
                const isDailyView = dailyView.style.display !== 'none';
                dailyView.style.display = isDailyView ? 'none' : 'block';
                weeklyView.style.display = isDailyView ? 'block' : 'none';
                switchViewBtn.textContent = isDailyView ? 'Day View' : 'Week View';
            });
        }

        // --- Day Navigation Logic ---
        if (prevDayBtn && nextDayBtn) {
            prevDayBtn.addEventListener('click', () => {
                currentDayIndex = (currentDayIndex - 1 + 7) % 7;
                renderDailySchedule(currentDayIndex);
            });

            nextDayBtn.addEventListener('click', () => {
                currentDayIndex = (currentDayIndex + 1) % 7;
                renderDailySchedule(currentDayIndex);
            });
        }
        
        // Initial render for the current day
        renderDailySchedule(currentDayIndex);
    }

    // Run the initializer
    initializeTimetable();
});