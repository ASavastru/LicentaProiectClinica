// Function to retrieve the timekeeping records
async function retrieveTimekeepingRecords() {
    try {
        const response = await axios.get('/timekeeping/read');
        const { timekeepingRecords } = response.data;
        renderTimekeepingRecords(timekeepingRecords);
    } catch (error) {
        console.error('Error:', error);
    }
}

// Function to render the timekeeping records in the DOM
function renderTimekeepingRecords(timekeepingRecords) {
    const timekeepingContainer = document.getElementById('timekeeping-container');

    // Clear the container before rendering
    timekeepingContainer.innerHTML = '';

    if (timekeepingRecords.length === 0) {
        const noRecordsMessage = document.createElement('p');
        noRecordsMessage.textContent = 'No timekeeping records found.';
        timekeepingContainer.appendChild(noRecordsMessage);
    } else {
        const ul = document.createElement('ul');
        timekeepingRecords.forEach(record => {
            const li = document.createElement('li');
            const day = document.createElement('span');
            const date = document.createElement('span');
            const start = document.createElement('span');
            const end = document.createElement('span');

            const InfoStart = record.start.split(/[T+]/, 3);
            const InfoEnd = record.end.split(/[T+]/, 3);
            const checkedDate = new Date(InfoStart[0]);

            const weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

            day.textContent = weekday[checkedDate.getDay()];
            date.textContent = InfoStart[0];
            start.textContent = InfoStart[1];
            end.textContent = InfoEnd[1];

            li.appendChild(day);
            li.appendChild(date);
            li.appendChild(start);
            li.appendChild(end);
            ul.appendChild(li);
        });
        timekeepingContainer.appendChild(ul);
    }
}

// Call the function to retrieve the timekeeping records
retrieveTimekeepingRecords();

// Function to handle the form submission
async function handleFormSubmit(event) {
    event.preventDefault();

    const form = event.target;
    try {
        const response = await axios.post('/timekeeping/create', new FormData(form));
        // Check if the response indicates a duplicate record
        if (response.data.duplicateRecord) {
            displayErrorMessage('A timekeeping record already exists for the same date.');
        } else {
            // Retrieve the updated timekeeping records after successful submission
            retrieveTimekeepingRecords();
        }
    } catch (error) {
        console.error('Error:', error);
        displayErrorMessage('An error occurred while submitting the form.');
    }
}

// Function to display an error message
function displayErrorMessage(message) {
    const errorContainer = document.getElementById('error-container');
    errorContainer.textContent = message;
    errorContainer.classList.add('show');
}

// Call the function to retrieve previous timekeeping records
retrieveTimekeepingRecords();

// Attach event listener to the form submission
const timekeepingForm = document.getElementById('timekeeping-form');
timekeepingForm.addEventListener('submit', handleFormSubmit);
