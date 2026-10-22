
/* ==========================================
   STUDENTHUB - PRACTICAL 4 AND PRACTICAL 5
   ========================================== */


/* ==========================================
   PRACTICAL 4: HAMBURGER MENU
   ========================================== */

const menuButton = document.getElementById("menuButton");
const navigation = document.getElementById("navigation");

if (menuButton && navigation) {
    menuButton.addEventListener("click", function () {
        navigation.classList.toggle("show-menu");

        const isExpanded =
            navigation.classList.contains("show-menu");

        menuButton.setAttribute("aria-expanded", isExpanded);
    });
}


/* ==========================================
   PRACTICAL 4: DARK AND LIGHT THEME
   ========================================== */

const themeButton = document.getElementById("themeButton");
const savedTheme = localStorage.getItem("studentHubTheme");

if (savedTheme === "dark") {
    document.body.classList.add("dark-theme");
}

if (themeButton) {

    if (savedTheme === "dark") {
        themeButton.textContent = "Switch to Light Theme";
    } else {
        themeButton.textContent = "Switch to Dark Theme";
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
}


/* ==========================================
   PRACTICAL 4: NOTIFICATION BANNER
   ========================================== */

const notification = document.getElementById("notification");

const closeNotification =
    document.getElementById("closeNotification");

if (notification) {
    notification.hidden = false;
}

if (notification && closeNotification) {

    closeNotification.addEventListener("click", function () {
        notification.hidden = true;
    });
}


/* ==========================================
   PRACTICAL 4: CONTENT SLIDER
   ========================================== */

const slides = document.querySelectorAll(".slide");

const previousSlide =
    document.getElementById("previousSlide");

const nextSlide =
    document.getElementById("nextSlide");

let currentSlide = 0;


// Display the selected slide
function showSlide(index) {

    if (slides.length === 0) {
        return;
    }

    slides[currentSlide].hidden = true;

    currentSlide = (index + slides.length) % slides.length;

    slides[currentSlide].hidden = false;
}


// Initially show only the first slide
if (slides.length > 0) {

    slides.forEach(function (slide, index) {
        slide.hidden = index !== 0;
    });
}


// Next button
if (nextSlide && slides.length > 0) {

    nextSlide.addEventListener("click", function () {
        showSlide(currentSlide + 1);
    });
}


// Previous button
if (previousSlide && slides.length > 0) {

    previousSlide.addEventListener("click", function () {
        showSlide(currentSlide - 1);
    });
}


/* ==========================================
   PRACTICAL 4: COLLAPSIBLE FAQ
   ========================================== */

const faqQuestions =
    document.querySelectorAll(".faq-question");

faqQuestions.forEach(function (question) {

    question.addEventListener("click", function () {

        const answer =
            question.parentElement.nextElementSibling;

        if (answer) {

            answer.hidden = !answer.hidden;

            question.setAttribute(
                "aria-expanded",
                !answer.hidden
            );
        }
    });
});


/* ==========================================
   PRACTICAL 4: MODAL POPUP
   ========================================== */

const openModal = document.getElementById("openModal");

const closeModal = document.getElementById("closeModal");

const infoModal = document.getElementById("infoModal");


// Open modal
if (openModal && infoModal) {

    openModal.addEventListener("click", function () {
        infoModal.showModal();
    });
}


// Close modal
if (closeModal && infoModal) {

    closeModal.addEventListener("click", function () {
        infoModal.close();
    });
}


/* ==========================================
   PRACTICAL 5: STUDENT DATA MANAGEMENT
   ========================================== */


// Get registration form elements
const studentForm = document.getElementById("studentForm");

const formMessage = document.getElementById("formMessage");


// Get student records page elements
const searchStudent = document.getElementById("searchStudent");

const studentTableBody =
    document.getElementById("studentTableBody");

const recordMessage = document.getElementById("recordMessage");


/* ==========================================
   GET SAVED STUDENT RECORDS
   ========================================== */

function getStudents() {

    const savedStudents =
        localStorage.getItem("studentRecords");

    if (savedStudents === null) {
        return [];
    }

    try {

        const students = JSON.parse(savedStudents);

        if (Array.isArray(students)) {
            return students;
        }

        return [];

    } catch (error) {

        return [];
    }
}


/* ==========================================
   SAVE STUDENT RECORDS
   ========================================== */

function saveStudents(students) {

    localStorage.setItem(
        "studentRecords",
        JSON.stringify(students)
    );
}


/* ==========================================
   PRACTICAL 5: STUDENT REGISTRATION
   ========================================== */

if (studentForm) {

    studentForm.addEventListener("submit", function (event) {

        event.preventDefault();

        const roll =
            document.getElementById("roll").value.trim();

        const name =
            document.getElementById("name").value.trim();

        const email =
            document.getElementById("email").value.trim();

        const course =
            document.getElementById("course").value;

        const password =
            document.getElementById("password").value;


        // Check required fields
        if (!roll || !name || !email || !course || !password) {

            formMessage.textContent =
                "Please fill in all fields.";

            return;
        }


        // Validate student name
        if (!/^[a-zA-Z ]+$/.test(name)) {

            formMessage.textContent =
                "Name must contain only letters and spaces.";

            return;
        }


        // Validate email address
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {

            formMessage.textContent =
                "Please enter a valid email address.";

            return;
        }


        // Validate password length
        if (password.length < 6) {

            formMessage.textContent =
                "Password must contain at least 6 characters.";

            return;
        }


        // Get existing student records
        const students = getStudents();


        // Check duplicate roll number
        const duplicate = students.some(function (student) {

            return student.roll.toLowerCase() ===
                   roll.toLowerCase();
        });


        if (duplicate) {

            formMessage.textContent =
                "This roll number is already registered.";

            return;
        }


        // Create a new student record
        const newStudent = {
            roll: roll,
            name: name,
            email: email,
            course: course
        };


        // Add the new student
        students.push(newStudent);


        // Save and redirect
        try {

            saveStudents(students);

            formMessage.textContent =
                "Registration successful!";

            studentForm.reset();

            window.location.href = "students.html";

        } catch (error) {

            formMessage.textContent =
                "Unable to save student data in this browser.";
        }
    });
}


/* ==========================================
   PRACTICAL 5: DISPLAY STUDENT RECORDS
   ========================================== */

function displayStudents() {

    // Run only on the student records page
    if (!studentTableBody) {
        return;
    }


    // Get saved records
    const students = getStudents();


    // Get search input
    const searchText = searchStudent
        ? searchStudent.value.trim().toLowerCase()
        : "";


    // Clear existing table rows
    studentTableBody.replaceChildren();


    // Filter records by name or roll number
    const filteredStudents = students.filter(function (student) {

        return student.name.toLowerCase().includes(searchText) ||
               student.roll.toLowerCase().includes(searchText);
    });


    // Show a message when no records exist
    if (filteredStudents.length === 0) {

        recordMessage.textContent =
            "No student records found.";

        return;
    }


    // Show matching record count
    recordMessage.textContent =
        "Total matching students: " + filteredStudents.length;


    // Create a table row for each student
    filteredStudents.forEach(function (student) {

        const row = document.createElement("tr");


        // Student details
        const values = [
            student.roll,
            student.name,
            student.email,
            student.course
        ];


        // Add student details to the row
        values.forEach(function (value) {

            const cell = document.createElement("td");

            cell.textContent = value;

            row.appendChild(cell);
        });


        // Create the action cell
        const actionCell = document.createElement("td");


        // Create the Delete button
        const deleteButton = document.createElement("button");

        deleteButton.textContent = "Delete";

        deleteButton.type = "button";


        // Delete the selected student
        deleteButton.addEventListener("click", function () {

            const updatedStudents = getStudents().filter(
                function (item) {

                    return item.roll.toLowerCase() !==
                           student.roll.toLowerCase();
                }
            );


            try {

                saveStudents(updatedStudents);

                displayStudents();

            } catch (error) {

                recordMessage.textContent =
                    "Unable to delete this record.";
            }
        });


        // Add button to the action cell
        actionCell.appendChild(deleteButton);


        // Add action cell to the row
        row.appendChild(actionCell);


        // Add row to the table
        studentTableBody.appendChild(row);
    });
}


/* ==========================================
   PRACTICAL 5: SEARCH STUDENTS
   ========================================== */

if (searchStudent) {

    searchStudent.addEventListener("input", function () {

        displayStudents();
    });
}


/* ==========================================
   LOAD STUDENT RECORDS WHEN PAGE OPENS
   ========================================== */

displayStudents();
