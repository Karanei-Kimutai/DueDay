document.addEventListener('DOMContentLoaded', function() {
    // Achievements Modal
    const achievementsBtn = document.getElementById('achievementsBtn');
    const achievementsModal = document.getElementById('achievementsModal');
    const closeModal = document.querySelector('.close-modal');
    
    achievementsBtn.addEventListener('click', function() {
        achievementsModal.classList.add('active');
    });
    
    closeModal.addEventListener('click', function() {
        achievementsModal.classList.remove('active');
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === achievementsModal) {
            achievementsModal.classList.remove('active');
        }
    });
    
    // Unit card click handlers
    const unitCards = document.querySelectorAll('.unit-card');
    unitCards.forEach(card => {
        card.addEventListener('click', function() {
            const unitName = this.querySelector('.unit-name').textContent;
            let message = `Unit: ${unitName}`;
            
            const status = this.querySelector('.unit-status');
            if (status) {
                if (status.classList.contains('assignment')) {
                    message += "\nYou have an assignment due in 2 days.";
                } else if (status.classList.contains('poll')) {
                    message += "\nThere's an active poll ending Friday.";
                }
            }
            
            alert(message);
        });
    });
});