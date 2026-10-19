const menuButton = document.getElementById("menuButton");
const navigation = document.getElementById("navigation");

menuButton.addEventListener("click", function () {
    navigation.classList.toggle("show-menu");

    const isExpanded = navigation.classList.contains("show-menu");
    menuButton.setAttribute("aria-expanded", isExpanded);
});


const themeButton = document.getElementById("themeButton");

const savedTheme = localStorage.getItem("studentHubTheme");

if (savedTheme === "dark") {
    document.body.classList.add("dark-theme");
    themeButton.textContent = "Switch to Light Theme";
}

themeButton.addEventListener("click", function () {
    document.body.classList.toggle("dark-theme");

    if (document.body.classList.contains("dark-theme")) {
        localStorage.setItem("studentHubTheme", "dark");
        themeButton.textContent = "Switch to Light Theme";
    } else {
        localStorage.setItem("studentHubTheme", "light");
        themeButton.textContent = "Switch to Dark Theme";
    }
});


const notification = document.getElementById("notification");
const closeNotification = document.getElementById("closeNotification");

notification.hidden = false;

closeNotification.addEventListener("click", function () {
    notification.hidden = true;
});

const slides = document.querySelectorAll(".slide");
const previousSlide = document.getElementById("previousSlide");
const nextSlide = document.getElementById("nextSlide");

let currentSlide = 0;

function showSlide(index) {
    slides[currentSlide].hidden = true;

    currentSlide = (index + slides.length) % slides.length;

    slides[currentSlide].hidden = false;
}

nextSlide.addEventListener("click", function () {
    showSlide(currentSlide + 1);
});

previousSlide.addEventListener("click", function () {
    showSlide(currentSlide - 1);
});


const faqQuestions = document.querySelectorAll(".faq-question");

faqQuestions.forEach(function (question) {
    question.addEventListener("click", function () {
        const answer = question.parentElement.nextElementSibling;

        answer.hidden = !answer.hidden;

        question.setAttribute("aria-expanded", !answer.hidden);
    });
});


const openModal = document.getElementById("openModal");
const closeModal = document.getElementById("closeModal");
const infoModal = document.getElementById("infoModal");

openModal.addEventListener("click", function () {
    infoModal.showModal();
});

closeModal.addEventListener("click", function () {
    infoModal.close();
});