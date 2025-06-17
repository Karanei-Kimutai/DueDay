document.addEventListener('DOMContentLoaded', function () {

    // ======================================================================
    //  1. GENERIC INITIALIZERS (RUN ON ALL PAGES)
    // ======================================================================

    /**
     * Finds all buttons with a `data-modal-target` attribute and makes them
     * open the specified modal. Also handles closing modals via a close button
     * or by clicking on the modal background.
     */
    const initAllModals = () => {
        const modalTriggers = document.querySelectorAll('[data-modal-target]');
        modalTriggers.forEach(trigger => {
            const modalId = trigger.dataset.modalTarget;
            const modal = document.getElementById(modalId);
            if (modal) {
                const closeModal = modal.querySelector('.close-modal');
                // Open modal event
                trigger.addEventListener('click', () => modal.classList.add('is-active'));
                // Close modal via the 'x' button
                if (closeModal) {
                    closeModal.addEventListener('click', () => modal.classList.remove('is-active'));
                }
            }
        });
        // Add a single listener to close modals when clicking on the background
        window.addEventListener('click', e => {
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('is-active');
            }
        });
    };

    /**
     * Handles the toggling between "Create New" and "View All" sections on a page.
     * It uses dedicated buttons and sections for a clearer user experience.
     */
    const initViewToggler = () => {
        const createBtn = document.getElementById('createBtn');
        const viewBtn = document.getElementById('viewBtn');
        const createSection = document.getElementById('createSection');
        const viewSection = document.getElementById('viewSection');

        // Only run if all required elements exist on the page
        if (!createBtn || !viewBtn || !createSection || !viewSection) {
            return;
        }

        // Set the initial state: show the "View All" section and hide the "Create" form
        viewSection.style.display = 'block';
        createSection.style.display = 'none';
        viewBtn.classList.add('is-active');
        createBtn.classList.remove('is-active');

        createBtn.addEventListener('click', (e) => {
            e.preventDefault();
            viewSection.style.display = 'none';
            createSection.style.display = 'block';
            viewBtn.classList.remove('is-active');
            createBtn.classList.add('is-active');
        });

        viewBtn.addEventListener('click', (e) => {
            e.preventDefault();
            createSection.style.display = 'none';
            viewSection.style.display = 'block';
            createBtn.classList.remove('is-active');
            viewBtn.classList.add('is-active');
        });
    };

    /**
     * Handles forms that have a `data-confirm` attribute, showing a native
     * browser confirmation prompt before allowing the form to submit.
     */
    const initConfirmForms = () => {
        document.querySelectorAll('form[data-confirm]').forEach(form => {
            form.addEventListener('submit', function (e) {
                const confirmationMessage = this.getAttribute('data-confirm');
                if (!confirm(confirmationMessage)) {
                    e.preventDefault();
                }
            });
        });
    };


    // ======================================================================
    //  2. PAGE-SPECIFIC INITIALIZERS
    // ======================================================================

    /**
     * Initializes logic specific to the Assignments Page.
     * Sets the assignment ID in the submission modal when a "Submit Work" button is clicked.
     */
    const initAssignmentsPage = () => {
        const modalAssignmentIdInput = document.getElementById('modal_assignment_id');
        if (!modalAssignmentIdInput) return;

        document.querySelectorAll('.submit-work-btn').forEach(button => {
            button.addEventListener('click', function () {
                const assignmentId = this.getAttribute('data-assignment-id');
                modalAssignmentIdInput.value = assignmentId;
            });
        });
    };

    /**
     * Initializes logic specific to the Polls Page.
     * - Allows dynamic adding and removing of poll options in the creation form.
     * - Fetches and displays poll results in a modal.
     */
    const initPollsPage = () => {
        // --- Logic for adding and removing poll options ---
        const addOptionBtn = document.getElementById('addPollOptionBtn');
        const optionsContainer = document.getElementById('pollOptionsContainer');

        if (addOptionBtn && optionsContainer) {
            // Add a new poll option
            addOptionBtn.addEventListener('click', () => {
                const optionCount = optionsContainer.children.length + 1;
                const newOption = document.createElement('div');
                newOption.className = 'poll-option-item';
                newOption.innerHTML = `
                    <input type="text" name="options[]" class="form-input" placeholder="Option ${optionCount}">
                    <button type="button" class="btn remove-option-btn">&times;</button>
                `;
                optionsContainer.appendChild(newOption);
            });

            // Remove a poll option (using event delegation)
            optionsContainer.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('remove-option-btn')) {
                    if (optionsContainer.children.length > 1) {
                        e.target.parentElement.remove();
                    } else {
                        alert("A poll must have at least one option.");
                    }
                }
            });
        }

        // --- Logic for viewing poll results ---
        const resultsModal = document.getElementById('resultsModal');
        const resultsContainer = document.getElementById('resultsContainer');
        const resultsTitle = document.getElementById('resultsTitle');

        document.querySelectorAll('.view-results-btn').forEach(button => {
            button.addEventListener('click', function () {
                const pollId = this.dataset.pollId;
                if (!resultsModal || !resultsContainer || !resultsTitle) return;

                // Fetch and display results
                fetch(`get_poll_results.php?poll_id=${pollId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            resultsTitle.textContent = `Results for: ${data.poll_title}`;
                            resultsContainer.innerHTML = ''; // Clear previous results
                            const totalVotes = data.results.reduce((sum, item) => sum + item.vote_count, 0);

                            if (data.results.length > 0 && totalVotes > 0) {
                                data.results.forEach(option => {
                                    const percentage = ((option.vote_count / totalVotes) * 100).toFixed(1);
                                    resultsContainer.innerHTML += `
                                        <div class="result-item">
                                            <div class="result-label">
                                                <span>${option.option_text}</span>
                                                <span>${percentage}% (${option.vote_count} votes)</span>
                                            </div>
                                            <div class="result-bar-container">
                                                <div class="result-bar" style="width: ${percentage}%"></div>
                                            </div>
                                        </div>`;
                                });
                            } else {
                                resultsContainer.innerHTML = '<p>No votes have been cast in this poll yet.</p>';
                            }
                        } else {
                            resultsContainer.innerHTML = `<p>Error: ${data.message || 'Could not load results.'}</p>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching poll results:', error);
                        if (resultsContainer) {
                            resultsContainer.innerHTML = '<p>An unexpected error occurred while fetching results.</p>';
                        }
                    });
            });
        });
    };


    // ======================================================================
    //  3. SCRIPT EXECUTION ROUTER
    // ======================================================================

    // Run generic initializers on all pages
    initAllModals();
    initViewToggler();
    initConfirmForms();

    // Run page-specific logic by checking for a unique element on that page
    if (document.querySelector('.assignment-view')) {
        initAssignmentsPage();
    }
    if (document.querySelector('.polls-view-section')) {
        initPollsPage();
    }

});