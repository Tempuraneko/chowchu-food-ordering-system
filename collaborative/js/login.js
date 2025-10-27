$(() => {

    // Autofocus
    $('form :input:not(button):first').focus();
    $('.err:first').prev().focus();
    $('.err.first').prev().find(':input:first').focus();

    // Initiate GET request
    $('[data-get]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.get;
        location = url || location;
    });

    // Initiate POST request
    $('[data-post]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.post;
        const f = $('<form>').appendTo(document.body)[0];
        f.method = 'POST';
        f.action = url || location;
        f.submit();
    });

    // Reset form
    $('[type=reset]').on('click', e => {
        e.preventDefault();
        location = location;
    });

    // Auto uppercase
    $('[data-upper]').on('input', e => {
        const a = e.target.selectionStart;
        const b = e.target.selectionEnd;
        e.target.value = e.target.value.toUpperCase();
        e.target.setSelectionRange(a, b);
    });


});

//password show and hide
document.addEventListener("DOMContentLoaded", () => {
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");
    const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");
    const confirmPasswordInput = document.getElementById("confirmedPassword");

    // Toggle Password Visibility
    togglePassword.addEventListener("click", () => {
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
        passwordInput.setAttribute("type", type);
        togglePassword.textContent = type === "password" ? "\u{1F441}" : "\u{1F576}"; // Eye or crossed-eye emoji
    });

    // Toggle Confirm Password Visibility
    toggleConfirmPassword.addEventListener("click", () => {
        const type = confirmPasswordInput.getAttribute("type") === "password" ? "text" : "password";
        confirmPasswordInput.setAttribute("type", type);
        toggleConfirmPassword.textContent = type === "password" ? "\u{1F441}" : "\u{1F576}"; // Eye or crossed-eye emoji
    });
});