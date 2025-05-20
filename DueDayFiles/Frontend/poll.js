document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const showCreatePollBtn = document.getElementById('showCreatePoll');
    const showViewPollsBtn = document.getElementById('showViewPolls');
    const pollCreateSection = document.querySelector('.poll-create-section');
    const pollsViewSection = document.querySelector('.polls-view-section');
    const cancelPollBtn = document.getElementById('cancelPollBtn');
    const addOptionBtn = document.getElementById('addOptionBtn');
    const pollOptionsContainer = document.getElementById('pollOptionsContainer');
    const createPollSubmit = document.getElementById('createPollSubmit');
    const viewResultsBtns = document.querySelectorAll('.view-results-btn');
    const resultsModal = document.getElementById('resultsModal');
    const resultsContainer = document.getElementById('resultsContainer');
    const resultsTitle = document.getElementById('resultsTitle');
    const submitVoteBtns = document.querySelectorAll('.submit-vote-btn');
    const closeModalBtn = document.querySelector('.close-modal');

    // Sample poll data
    const polls = [
        {
            id: 1,
            title: "OOP Feedback",
            class: "ICS 2102",
            description: "We want your input on the test structure.",
            options: ["Too Hard", "Fair", "Going Well"],
            votes: [15, 32, 28],
            expiry: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000), // 2 days from now
            anonymous: true,
            multipleChoices: false
        },
        {
            id: 2,
            title: "Preferred IDE",
            class: "ICS 2101",
            description: "Which IDE do you prefer for web development?",
            options: ["VS Code", "WebStorm", "Sublime Text", "Atom"],
            votes: [42, 38, 20, 15],
            expiry: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000), // 3 days ago
            anonymous: false,
            multipleChoices: false
        },
        {
            id: 3,
            title: "Programming Language Preference",
            class: "CS 101",
            description: "Which programming language do you enjoy working with most?",
            options: ["JavaScript", "Python", "Java", "C++"],
            votes: [45, 38, 52, 60],
            expiry: new Date(Date.now() + 5 * 24 * 60 * 60 * 1000), // 5 days from now
            anonymous: true,
            multipleChoices: false
        }
    ];

    // Initialize view (show polls by default)
    showViewPollsBtn.classList.add('active');
    pollCreateSection.style.display = 'none';
    pollsViewSection.style.display = 'block';

    // Toggle between create and view sections
    showCreatePollBtn.addEventListener('click', function() {
        pollCreateSection.style.display = 'block';
        pollsViewSection.style.display = 'none';
        this.classList.add('active');
        showViewPollsBtn.classList.remove('active');
    });

    showViewPollsBtn.addEventListener('click', function() {
        pollCreateSection.style.display = 'none';
        pollsViewSection.style.display = 'block';
        this.classList.add('active');
        showCreatePollBtn.classList.remove('active');
    });

    // Cancel poll creation
    cancelPollBtn.addEventListener('click', function() {
        pollCreateSection.style.display = 'none';
        pollsViewSection.style.display = 'block';
        showViewPollsBtn.classList.add('active');
        showCreatePollBtn.classList.remove('active');
    });

    // Add poll option
    addOptionBtn.addEventListener('click', function() {
        const optionCount = pollOptionsContainer.children.length + 1;
        const optionDiv = document.createElement('div');
        optionDiv.className = 'poll-option';
        optionDiv.innerHTML = `
            <input type="text" class="option-input" placeholder="Option ${optionCount}">
            <button class="remove-option-btn">&times;</button>
        `;
        pollOptionsContainer.appendChild(optionDiv);
        
        // Add event to remove button
        optionDiv.querySelector('.remove-option-btn').addEventListener('click', function() {
            if (pollOptionsContainer.children.length > 2) {
                pollOptionsContainer.removeChild(optionDiv);
            } else {
                alert('A poll must have at least two options');
            }
        });
    });

    // Submit vote
    submitVoteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const pollCard = this.closest('.poll-card');
            const pollId = parseInt(pollCard.dataset.pollId);
            const selectedPoll = polls.find(poll => poll.id === pollId);
            
            if (selectedPoll) {
                if (selectedPoll.multipleChoices) {
                    // Handle multiple choice selection
                    alert('Thank you for your multiple choice votes!');
                } else {
                    // Handle single choice selection
                    const selectedOption = pollCard.querySelector('input[type="radio"]:checked');
                    if (selectedOption) {
                        alert('Thank you for your vote!');
                    } else {
                        alert('Please select an option before submitting');
                    }
                }
            }
        });
    });

    // View results
    viewResultsBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const pollCard = this.closest('.poll-card');
            const pollId = parseInt(pollCard.dataset.pollId);
            const selectedPoll = polls.find(poll => poll.id === pollId);
            
            if (selectedPoll) {
                showResults(selectedPoll);
            }
        });
    });

    // Show results in modal
    function showResults(poll) {
        resultsTitle.textContent = `${poll.title} Results`;
        resultsContainer.innerHTML = '';
        
        const totalVotes = poll.votes.reduce((sum, votes) => sum + votes, 0);
        
        poll.options.forEach((option, index) => {
            const votes = poll.votes[index];
            const percentage = totalVotes > 0 ? Math.round((votes / totalVotes) * 100) : 0;
            
            const resultItem = document.createElement('div');
            resultItem.className = 'result-item';
            resultItem.innerHTML = `
                <div class="result-label">
                    <span>${option}</span>
                    <span class="result-percentage">${percentage}% (${votes} votes)</span>
                </div>
                <div class="result-bar-container">
                    <div class="result-bar" style="width: ${percentage}%"></div>
                </div>
            `;
            resultsContainer.appendChild(resultItem);
        });
        
        resultsModal.style.display = 'block';
    }

    // Close results modal
    closeModalBtn.addEventListener('click', function() {
        resultsModal.style.display = 'none';
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === resultsModal) {
            resultsModal.style.display = 'none';
        }
    });

    // Create new poll
    createPollSubmit.addEventListener('click', function() {
        const title = document.getElementById('pollTitle').value.trim();
        const description = document.getElementById('pollDescription').value.trim();
        const course = document.getElementById('pollCourse').value;
        const expiry = document.getElementById('pollExpiry').value;
        const anonymous = document.getElementById('anonymousPoll').checked;
        const multipleChoices = document.getElementById('multipleChoices').checked;
        
        // Get all poll options
        const optionInputs = document.querySelectorAll('.option-input');
        const options = Array.from(optionInputs).map(input => input.value.trim()).filter(opt => opt !== '');
        
        // Validate inputs
        if (!title) {
            alert('Please enter a poll title');
            return;
        }
        
        if (!course) {
            alert('Please select a course');
            return;
        }
        
        if (options.length < 2) {
            alert('A poll must have at least two options');
            return;
        }
        
        if (!expiry) {
            alert('Please set an expiry date/time');
            return;
        }
        
        // Create new poll object
        const newPoll = {
            id: polls.length + 1,
            title,
            class: course,
            description,
            options,
            votes: new Array(options.length).fill(0),
            expiry: new Date(expiry),
            anonymous,
            multipleChoices
        };
        
        // Add to polls array
        polls.push(newPoll);
        
        // Reset form and show view
        document.getElementById('pollTitle').value = '';
        document.getElementById('pollDescription').value = '';
        document.getElementById('pollCourse').value = '';
        document.getElementById('pollExpiry').value = '';
        document.getElementById('anonymousPoll').checked = false;
        document.getElementById('multipleChoices').checked = false;
        
        // Remove all but two options
        while (pollOptionsContainer.children.length > 2) {
            pollOptionsContainer.removeChild(pollOptionsContainer.lastChild);
        }
        
        // Reset the first two options
        const optionInputsReset = document.querySelectorAll('.option-input');
        optionInputsReset[0].value = '';
        optionInputsReset[1].value = '';
        
        // Show success message and switch to view
        alert('Poll created successfully!');
        pollCreateSection.style.display = 'none';
        pollsViewSection.style.display = 'block';
        showViewPollsBtn.classList.add('active');
        showCreatePollBtn.classList.remove('active');
    });

    // Initialize poll options remove buttons
    document.querySelectorAll('.remove-option-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (pollOptionsContainer.children.length > 2) {
                this.parentElement.remove();
            } else {
                alert('A poll must have at least two options');
            }
        });
    });

    // Set data-poll-id attributes (for demo purposes)
    document.querySelectorAll('.poll-card').forEach((card, index) => {
        card.dataset.pollId = index + 1;
    });
});