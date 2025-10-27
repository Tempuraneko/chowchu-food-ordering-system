document.addEventListener("DOMContentLoaded", function() {
    let rows = document.querySelectorAll(".clickable-row");
    
    rows.forEach(row => {
        row.addEventListener("click", function(event) {
            // Prevent redirection if clicking inside a checkbox or link
            if (!event.target.closest("a") && !event.target.closest("input[type='checkbox']")) {
                window.location.href = this.dataset.href;
            }
        });
    });
});
