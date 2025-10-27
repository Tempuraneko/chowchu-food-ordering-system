let darkmode = localStorage.getItem('darkmode');
const themeSwitch = document.getElementById('theme-switch');

const enableDarkmode = () => {
  document.body.classList.add('darkmode');
  localStorage.setItem('darkmode', 'active');
};

const disableDarkmode = () => {
  document.body.classList.remove('darkmode');
  localStorage.setItem('darkmode', null);
};

// ✅ Check and apply dark mode on page load
if (darkmode === "active") {
  enableDarkmode();
}

// ✅ Single event listener for toggling
themeSwitch.addEventListener("click", () => {
  darkmode = localStorage.getItem('darkmode');
  if (darkmode !== "active") {
    enableDarkmode();
  } else {
    disableDarkmode();
  }
});


