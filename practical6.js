
const dataType = document.getElementById("dataType");
const searchBox = document.getElementById("searchBox");
const categoryFilter = document.getElementById("categoryFilter");
const sortOrder = document.getElementById("sortOrder");

const dataContainer = document.getElementById("dataContainer");
const loadingMessage = document.getElementById("loadingMessage");
const resultCount = document.getElementById("resultCount");
const pageInfo = document.getElementById("pageInfo");

const previousPage = document.getElementById("previousPage");
const nextPage = document.getElementById("nextPage");

let allRecords = [];
let currentPage = 1;
const recordsPerPage = 5;


async function loadData() {

    const fileName = dataType.value + ".json";

    loadingMessage.textContent = "Loading data...";
    dataContainer.replaceChildren();
    resultCount.textContent = "";
    pageInfo.textContent = "";

    try {

        const response = await fetch(fileName);

        if (!response.ok) {
            throw new Error("Unable to load " + fileName);
        }

        const records = await response.json();

        if (!Array.isArray(records)) {
            throw new Error("The JSON data must be an array.");
        }

        allRecords = records;
        currentPage = 1;

        createCategoryOptions();
        displayData();

        loadingMessage.textContent = "Data loaded successfully.";

    } catch (error) {

        allRecords = [];
        categoryFilter.replaceChildren();

        const option = document.createElement("option");
        option.value = "";
        option.textContent = "All categories/courses";
        categoryFilter.appendChild(option);

        dataContainer.replaceChildren();
        resultCount.textContent = "";
        pageInfo.textContent = "";

        loadingMessage.textContent =
            "Error loading data. Check the JSON file and run Live Server.";

        console.error(error);
    }
}


function getRecordDetails(record) {

    if (dataType.value === "students") {

        return {
            title: record.name,
            subtitle: "Roll Number: " + record.roll,
            details: "Year: " + record.year,
            category: record.course
        };

    } else if (dataType.value === "events") {

        return {
            title: record.title,
            subtitle: "Date: " + record.date,
            details: record.description,
            category: record.category
        };

    } else {

        return {
            title: record.question,
            subtitle: "Category: " + record.category,
            details: record.answer,
            category: record.category
        };
    }
}


function createCategoryOptions() {

    categoryFilter.replaceChildren();

    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = "All categories/courses";

    categoryFilter.appendChild(defaultOption);

    const categories = [];

    allRecords.forEach(function (record) {

        const details = getRecordDetails(record);

        if (!categories.includes(details.category)) {
            categories.push(details.category);
        }
    });

    categories.sort();

    categories.forEach(function (category) {

        const option = document.createElement("option");

        option.value = category;
        option.textContent = category;

        categoryFilter.appendChild(option);
    });
}

function getFilteredRecords() {

    const searchText = searchBox.value.trim().toLowerCase();
    const selectedCategory = categoryFilter.value;

    let filteredRecords = allRecords.filter(function (record) {

        const details = getRecordDetails(record);

        const searchableText = [
            details.title,
            details.subtitle,
            details.details,
            details.category
        ].join(" ").toLowerCase();

        const matchesSearch =
            searchableText.includes(searchText);

        const matchesCategory =
            selectedCategory === "" ||
            details.category === selectedCategory;

        return matchesSearch && matchesCategory;
    });

    filteredRecords.sort(function (a, b) {

        const titleA = getRecordDetails(a).title.toLowerCase();
        const titleB = getRecordDetails(b).title.toLowerCase();

        if (sortOrder.value === "asc") {
            return titleA.localeCompare(titleB);
        }

        return titleB.localeCompare(titleA);
    });

    return filteredRecords;
}


function displayData() {

    const filteredRecords = getFilteredRecords();

    const totalRecords = filteredRecords.length;

    const totalPages = Math.ceil(totalRecords / recordsPerPage);

    if (totalPages === 0) {
        currentPage = 1;
    } else if (currentPage > totalPages) {
        currentPage = totalPages;
    }

    const startIndex = (currentPage - 1) * recordsPerPage;

    const pageRecords = filteredRecords.slice(
        startIndex,
        startIndex + recordsPerPage
    );

    dataContainer.replaceChildren();

    resultCount.textContent =
        "Matching records: " + totalRecords;

    if (totalRecords === 0) {

        const message = document.createElement("p");
        message.textContent = "No matching records found.";

        dataContainer.appendChild(message);

    } else {

        pageRecords.forEach(function (record) {

            const details = getRecordDetails(record);

            const card = document.createElement("article");
            card.className = "data-card";

            const title = document.createElement("h3");
            title.textContent = details.title;

            const subtitle = document.createElement("p");
            subtitle.textContent = details.subtitle;

            const description = document.createElement("p");
            description.textContent = details.details;

            const category = document.createElement("p");
            category.textContent = "Category/Course: " + details.category;

            card.appendChild(title);
            card.appendChild(subtitle);
            card.appendChild(description);
            card.appendChild(category);

            dataContainer.appendChild(card);
        });
    }

    pageInfo.textContent =
        "Page " + currentPage + " of " + totalPages;

    previousPage.disabled = currentPage <= 1;
    nextPage.disabled =
        totalPages === 0 || currentPage >= totalPages;
}


searchBox.addEventListener("input", function () {

    currentPage = 1;
    displayData();
});

categoryFilter.addEventListener("change", function () {

    currentPage = 1;
    displayData();
});

sortOrder.addEventListener("change", function () {

    currentPage = 1;
    displayData();
});

dataType.addEventListener("change", function () {

    searchBox.value = "";
    categoryFilter.value = "";
    sortOrder.value = "asc";

    loadData();
});


previousPage.addEventListener("click", function () {

    if (currentPage > 1) {
        currentPage--;
        displayData();
    }
});

nextPage.addEventListener("click", function () {

    const totalPages = Math.ceil(
        getFilteredRecords().length / recordsPerPage
    );

    if (currentPage < totalPages) {
        currentPage++;
        displayData();
    }
});

loadData();
