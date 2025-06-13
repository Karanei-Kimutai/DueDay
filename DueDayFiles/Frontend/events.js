document.addEventListener('DOMContentLoaded', function() {
    const eventForm = document.getElementById('event-form');
    const eventsList = document.getElementById('events-list');

    // Function to get events from localStorage
    const getEvents = () => {
        const events = localStorage.getItem('events');
        return events ? JSON.parse(events) : [];
    };

    // Function to save events to localStorage
    const saveEvents = (events) => {
        localStorage.setItem('events', JSON.stringify(events));
    };

    // Function to display a single event on the coordinator's page
    const displayEvent = (event) => {
        const eventElement = document.createElement('div');
        eventElement.classList.add('event-list-item');
        // Add a data-id attribute to easily identify the event for deletion
        eventElement.setAttribute('data-id', event.id);

        // Format date for display
        const displayDate = new Date(event.date).toLocaleDateString('en-US', {
            year: 'numeric', month: 'long', day: 'numeric'
        });

        eventElement.innerHTML = `
            <p><strong>Title:</strong> ${event.title}</p>
            <p><strong>Venue:</strong> ${event.venue}</p>
            <p><strong>Date:</strong> ${displayDate}</p>
            <p><strong>Description:</strong> ${event.description}</p>
            <div class="event-actions">
                <button class="rsvp-btn-small">RSVP</button>
                <button class="update-btn">Update</button>
                <button class="delete-btn">Delete</button>
            </div>
        `;
        eventsList.appendChild(eventElement);

        // Add event listener for the new delete button
        eventElement.querySelector('.delete-btn').addEventListener('click', function() {
            deleteEvent(event.id);
        });
    };

    // Function to delete an event
    const deleteEvent = (eventId) => {
        let events = getEvents();
        // Filter out the event with the matching id
        events = events.filter(event => event.id !== eventId);
        saveEvents(events);
        // Re-render the events list
        loadEvents();
    };

    // Function to load and display all events when the page starts
    const loadEvents = () => {
        // Clear the current list in the HTML
        eventsList.innerHTML = '';
        const events = getEvents();
        events.forEach(event => displayEvent(event));
    };

    // Handle the form submission
    eventForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Create a new event object
        const newEvent = {
            id: Date.now(), // Use timestamp as a unique ID
            title: document.getElementById('event-title').value,
            venue: document.getElementById('event-venue').value,
            description: document.getElementById('event-description').value,
            // Store date in a standard format
            date: document.getElementById('event-date').value
        };

        const events = getEvents();
        events.push(newEvent);
        saveEvents(events);

        displayEvent(newEvent); // Display the new event on the page
        eventForm.reset();
    });

    // Initial load of events from localStorage
    loadEvents();
});