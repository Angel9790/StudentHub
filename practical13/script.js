
const api = "api.php";

const form = document.getElementById("studentForm");
const idInput = document.getElementById("id");
const nameInput = document.getElementById("name");
const emailInput = document.getElementById("email");
const courseInput = document.getElementById("course");

const table = document.getElementById("studentTable");
const message = document.getElementById("message");
const searchInput = document.getElementById("search");
const totalStudents = document.getElementById("totalStudents");

const saveButton = document.getElementById("saveButton");
const cancelButton = document.getElementById("cancelButton");
const refreshButton = document.getElementById("refreshButton");
const formHeading = document.getElementById("formHeading");

let students = [];
let messageTimer;

function showMessage(text, type = "info") {
    clearTimeout(messageTimer);

    message.textContent = text;
    message.className = "message show " + type;

    messageTimer = setTimeout(function() {
        message.textContent = "";
        message.className = "message";
    }, 5000);
}

async function apiRequest(method = "GET", data = null) {
    const options = {
        method: method,
        headers: {
            "Accept": "application/json"
        }
    };

    if (data !== null) {
        options.headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(data);
    }

    const response = await fetch(api, options);

    let result;

    try {
        result = await response.json();
    } catch (error) {
        throw new Error("Invalid server response. Check the PHP files.");
    }

    if (!response.ok || !result.success) {
        throw new Error(result.message || "Something went wrong.");
    }

    return result;
}

function displayStudents() {
    table.textContent = "";

    const searchText = searchInput.value.trim().toLowerCase();

    const filteredStudents = students.filter(function(student) {
        return (
            String(student.id).includes(searchText) ||
            student.name.toLowerCase().includes(searchText) ||
            student.email.toLowerCase().includes(searchText) ||
            student.course.toLowerCase().includes(searchText)
        );
    });

    if (filteredStudents.length === 0) {
        const row = document.createElement("tr");
        const cell = document.createElement("td");

        cell.colSpan = 5;
        cell.className = "empty-state";
        cell.textContent = students.length === 0
            ? "No student records found. Add your first student above."
            : "No students match your search.";

        row.appendChild(cell);
        table.appendChild(row);
        return;
    }

    filteredStudents.forEach(function(student) {
        const row = document.createElement("tr");

       
        const idCell = document.createElement("td");
        const idBadge = document.createElement("span");

        idBadge.className = "id-badge";
        idBadge.textContent = student.id;
        idCell.appendChild(idBadge);
        row.appendChild(idCell);

        const nameCell = document.createElement("td");
        nameCell.className = "student-name";
        nameCell.textContent = student.name;
        row.appendChild(nameCell);

        const emailCell = document.createElement("td");
        emailCell.textContent = student.email;
        row.appendChild(emailCell);

        const courseCell = document.createElement("td");
        courseCell.textContent = student.course;
        row.appendChild(courseCell);

        const actionsCell = document.createElement("td");

        const editButton = document.createElement("button");
        editButton.type = "button";
        editButton.className = "edit-btn";
        editButton.textContent = "Edit";

        editButton.addEventListener("click", function() {
            editStudent(student.id);
        });

        const deleteButton = document.createElement("button");
        deleteButton.type = "button";
        deleteButton.className = "delete-btn";
        deleteButton.textContent = "Delete";

        deleteButton.addEventListener("click", function() {
            deleteStudent(student.id);
        });

        actionsCell.append(editButton, deleteButton);
        row.appendChild(actionsCell);

        table.appendChild(row);
    });
}

async function loadStudents() {
    table.innerHTML =
        '<tr><td colspan="5" class="empty-state">Loading students...</td></tr>';

    refreshButton.disabled = true;

    try {
        const result = await apiRequest("GET");

        students = result.data;
        totalStudents.textContent = students.length;

        displayStudents();
    } catch (error) {
        table.innerHTML =
            '<tr><td colspan="5" class="empty-state">Unable to load students.</td></tr>';

        showMessage(error.message, "error");
    } finally {
        refreshButton.disabled = false;
    }
}

function clearForm() {
    form.reset();
    idInput.value = "";

    formHeading.textContent = "Add New Student";
    saveButton.textContent = "+ Save Student";
}

function editStudent(id) {
    const student = students.find(function(item) {
        return Number(item.id) === Number(id);
    });

    if (!student) {
        showMessage("Student not found.", "error");
        return;
    }

    idInput.value = student.id;
    nameInput.value = student.name;
    emailInput.value = student.email;
    courseInput.value = student.course;

    formHeading.textContent = "Edit Student";
    saveButton.textContent = "Update Student";

    document.getElementById("studentForm").scrollIntoView({
        behavior: "smooth",
        block: "center"
    });

    nameInput.focus();
    showMessage("Editing student ID " + student.id, "info");
}

form.addEventListener("submit", async function(event) {
    event.preventDefault();

    const id = idInput.value;

    const data = {
        name: nameInput.value.trim(),
        email: emailInput.value.trim(),
        course: courseInput.value.trim()
    };

    if (!data.name || !data.email || !data.course) {
        showMessage("Please fill in all fields.", "error");
        return;
    }

    if (!emailInput.validity.valid) {
        showMessage("Please enter a valid email address.", "error");
        emailInput.focus();
        return;
    }

    const method = id ? "PUT" : "POST";

    if (id) {
        data.id = Number(id);
    }

    saveButton.disabled = true;
    saveButton.textContent = id ? "Updating..." : "Saving...";

    try {
        const result = await apiRequest(method, data);

        clearForm();
        await loadStudents();

        showMessage(result.message, "success");
    } catch (error) {
        showMessage(error.message, "error");
    } finally {
        saveButton.disabled = false;

        if (idInput.value) {
            saveButton.textContent = "Update Student";
        } else {
            saveButton.textContent = "+ Save Student";
        }
    }
});

async function deleteStudent(id) {
    const student = students.find(function(item) {
        return Number(item.id) === Number(id);
    });

    if (!student) {
        showMessage("Student not found.", "error");
        return;
    }

    const confirmed = confirm(
        "Are you sure you want to delete " + student.name + "?"
    );

    if (!confirmed) {
        return;
    }

    try {
        const result = await apiRequest("DELETE", {
            id: Number(id)
        });

        if (Number(idInput.value) === Number(id)) {
            clearForm();
        }

        await loadStudents();
        showMessage(result.message, "success");
    } catch (error) {
        showMessage(error.message, "error");
    }
}

searchInput.addEventListener("input", function() {
    displayStudents();
});

cancelButton.addEventListener("click", function() {
    clearForm();
    showMessage("Form cleared.", "info");
});

refreshButton.addEventListener("click", function() {
    loadStudents();
});

loadStudents();
