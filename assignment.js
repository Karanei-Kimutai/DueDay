document.addEventListener('DOMContentLoaded', function() {
    // Get all necessary elements
    const createBtn = document.querySelector('.create-btn');
    const viewBtn = document.querySelector('.view-btn');
    const createForm = document.querySelector('.assignment-form');
    const viewSection = document.querySelector('.assignment-view');
    
    // Set initial state - show create form by default
    createForm.style.display = 'block';
    viewSection.style.display = 'none';
    
    // Toggle between create and view assignments
    createBtn.addEventListener('click', function() {
        createForm.style.display = 'block';
        viewSection.style.display = 'none';
        this.classList.add('active-btn');
        viewBtn.classList.remove('active-btn');
    });
    
    viewBtn.addEventListener('click', function() {
        createForm.style.display = 'none';
        viewSection.style.display = 'block';
        this.classList.add('active-btn');
        createBtn.classList.remove('active-btn');
    });
    
    // Toggle comment sections
    const commentBtns = document.querySelectorAll('.comment-btn');
    commentBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const commentSection = this.closest('.assignment-card').querySelector('.comment-section');
            commentSection.style.display = commentSection.style.display === 'none' ? 'block' : 'none';
        });
    });
    
    // Submission modal
    const submitBtns = document.querySelectorAll('.submit-btn');
    const modal = document.querySelector('.modal');
    const closeModal = document.querySelector('.close-modal');
    
    submitBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            modal.style.display = 'block';
        });
    });
    
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Post comment functionality
    const postCommentBtns = document.querySelectorAll('.post-comment-btn');
    postCommentBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const commentInput = this.previousElementSibling;
            const commentText = commentInput.value.trim();
            
            if (commentText) {
                const commentThread = this.closest('.comment-section').querySelector('.comment-thread');
                const newComment = document.createElement('div');
                newComment.className = 'comment';
                newComment.innerHTML = `<span class="comment-author">You:</span> <span class="comment-text">${commentText}</span>`;
                commentThread.appendChild(newComment);
                commentInput.value = '';
            }
        });
    });
});