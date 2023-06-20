let date = new Date();
let year = date.getFullYear();
let month = date.getMonth();
let selectedDay = date.getDate()

const day = document.querySelector(".calendar-dates");

const currdate = document
    .querySelector(".calendar-current-date");

const prenexIcons = document
    .querySelectorAll(".calendar-navigation span");

const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
];

const manipulate = () => {

    let dayone = new Date(year, month, 1).getDay();
    let lastdate = new Date(year, month + 1, 0).getDate();
    let dayend = new Date(year, month, lastdate).getDay();
    let monthlastdate = new Date(year, month, 0).getDate();
    let lit = "";

    for (let i = dayone; i > 0; i--) {
        lit +=
            `<li class="inactive">${monthlastdate - i + 1}</li>`;
    }

    for (let i = 1; i <= lastdate; i++) {

        // Check if the current date is today
        let isToday = i === date.getDate()
        && month === new Date().getMonth()
        && year === new Date().getFullYear()
            ? "active"
            : "";
        lit += `<li class="${isToday} day">${i}</li>`;
    }

    for (let i = dayend; i < 6; i++) {
        lit += `<li class="inactive">${i - dayend + 1}</li>`
    }

    currdate.innerText = `${months[month]} ${year}`;
    day.innerHTML = lit;
}

manipulate();

prenexIcons.forEach(icon => {

    icon.addEventListener("click", () => {
        month = icon.id === "calendar-prev" ? month - 1 : month + 1;
        if (month < 0 || month > 11) {
            date = new Date(year, month, new Date().getDate());
            year = date.getFullYear();
            month = date.getMonth();
        } else {
            date = new Date();
        }

        manipulate();
        selectDay();
    });
});

selectDay();

const allDays = document.querySelectorAll(".day");

for (let i = 0; i < allDays.length; i++) {
    const dayCell = allDays[i];

    if (dayCell.classList.contains("active")) {
        const dateToCheck = new Date(year, month, Number(dayCell.innerText) + 1);
        checkDate(dateToCheck);
        break; // Exit the loop after finding the active day
    }
}

function selectDay() {
    document.querySelectorAll(".day").forEach(dayCELL=>{
        dayCELL.addEventListener('click', event=>{
            document.querySelectorAll(".day").forEach(dayCELLaux=>{
                dayCELLaux.classList.remove("active");
            })
            dayCELL.classList.add("active");
            const dateToCheck = new Date(year, month, Number(dayCELL.innerText)+1);
            selectedDay = Number(dayCELL.innerText);
            checkDate(dateToCheck);
        })
    })
}

function checkDate(date) {
    const dataToSend = new FormData();
    dataToSend.set("dateToCheck", "@" + Math.round(date.getTime() / 1000));
    dataToSend.set("practitionerId", practitionerIdCurrent);

    axios.post("appointment/checkDate", dataToSend).then(function (response) {
        if (response.data.status === true) {
            if (response.data.currentUserRole == "ROLE_USER") {
                let div = "<ul>";
                for (let i = 8; i <= 20; i++) {
                    let flag = false;
                    let firstName = null;
                    let lastName = null;
                    let examRoom = null;
                    for (let count = 0; count <= response.data.count; count++) {
                        // console.log(response.data.timestart[count], count, i);
                        if (i == response.data.timestart[count]) {
                            console.log("a")

                            flag = true;
                            firstName = response.data.practitionerfirstname[count];
                            lastName = response.data.practitionerlastname[count];
                            examRoom = response.data.examRoom[count];
                        }
                        if (flag === true) {
                            div += `<li>` + i + `:00<div class="div-practitioner-data">` + firstName + ` ` + lastName + ` | ` + examRoom + `</div></li>`;
                        } else {
                            div += `<li>` + i + `:00<div></div></li>`;
                        }
                    }
                    div += "</ul>";
                    document.getElementById("main-day-container").innerHTML = div;
                }
            }
            if (response.data.currentUserRole == "ROLE_PRACTITIONER" || response.data.currentUserRole == "ROLE_SECRETARY") {
                let div = "<ul>";
                for (let i = 8; i <= 20; i++) {
                    let flag = false;
                    let firstName = null;
                    let lastName = null;
                    let cnp = null;
                    let isInsured = null;
                    for (let count = 0; count <= response.data.count; count++) {
                        if (i == response.data.timestart[count]) {
                            flag = true;
                            firstName = response.data.patientfirstname[count];
                            lastName = response.data.patientlastname[count];
                            cnp = response.data.cnp[count];
                            isInsured = response.data.isInsured[count];
                            if (isInsured)
                                isInsured = 'Insurance Valid & Up to Date';
                        }
                    }
                    if (flag === true) {
                        div += `<li>` + i + `:00<div class="div-patient-data">` + firstName + ` ` + lastName + `, ` + cnp + `, ` + isInsured + `</div>
                                <div class="delete-button" onclick="deleteAppointment(`+response.data.id+`)">DELETE</div></li>`;
                    } else {
                        div += `<li>` + i + `:00<div><div class="create-button" onclick="createAppointment(`+i+`)">CREATE</div></div></li>`;
                    }
                }
                div += "</ul>";
                document.getElementById("main-day-container").innerHTML = div;
            }
    };
})}

async function createAppointment(hour){
    document.getElementById("patient-list").style.display = "block";
    document.getElementById("patient-list").childNodes.forEach(child => {
        child.addEventListener('click', event => {
            const patientId = event.target.getAttribute('data-id');
            const dataToSend = new FormData();
            dataToSend.set("practitionerId", practitionerIdCurrent);
            dataToSend.set("patientId", patientId);
            dataToSend.set("hour", hour);
            dataToSend.set("year", year);
            dataToSend.set("month", month+1);
            dataToSend.set("day", selectedDay);
            axios.post('/appointment/create', dataToSend).then(function (response){
                checkDate(new Date(year, month, selectedDay+1));
                console.log(new Date(year, month, selectedDay+1));
                document.getElementById("patient-list").style.display = "none";
                let patientList = document.getElementById("patient-list");
                let newPatientList = patientList.cloneNode(true);
                patientList.parentNode.replaceChild(newPatientList, patientList); //if u replace the parent node it deletes all the event listeners
            });
        });
    });
}

function deleteAppointment(id){
    axios.post('/appointment/delete/'+id);
    checkDate(new Date(year, month, selectedDay+1));
}

let defaultPractitioner;

async function populatePractitioners() {
    try {
        const response = await axios.post('/appointments/practitioners');
        const practitioners = response.data;

        const practitionerList = document.querySelector('#practitioner-list');

        practitioners.forEach((practitioner, index) => {
            const listItem = document.createElement('li');
            listItem.textContent = `${practitioner.firstName} ${practitioner.lastName} | Exam Room: ${practitioner.examRoom}`;
            listItem.dataset.practitionerId = practitioner.id; // Set the practitioner ID as a dataset attribute
            listItem.addEventListener('click', () => {
                practitionerIdCurrent = practitioner.id;
                handlePractitionerSelection(practitioner, listItem);
            });

            // Set the first practitioner as the default practitioner
            if (index === 0) {
                listItem.classList.add('activePractitioner');
                listItem.setAttribute('id', 'practitionerEntry');
                practitionerId = practitioner.id; // Set the default practitioner ID
            } else {
                listItem.classList.add('inactivePractitioner');
                listItem.setAttribute('id', 'practitionerEntry');
            }

            practitionerList.appendChild(listItem);
        });

        // Check if defaultPractitioner is not undefined
        if (defaultPractitioner) {
            handlePractitionerSelection(defaultPractitioner);
        }

        if (document.getElementById("practitioner-list") != null) {
            document.getElementById("practitioner-list").children[0].click();
        }

    } catch (error) {
        console.error('Error:', error);
    }
}


function handlePractitionerSelection(practitioner, listItem) {
    const selectedDay = document.querySelector('.day.active');
    const dateToCheck = new Date(year, month, Number(selectedDay.innerText) + 1);
    const activePractitioner = document.querySelector('.activePractitioner');
    const practitionerList = document.getElementById("practitioner-list");

    // Update the active/inactive classes
    practitionerList.childNodes.forEach(node => {
        if (node === listItem) {
            node.classList.add('activePractitioner');
            node.classList.remove('inactivePractitioner');
        } else {
            node.classList.remove('activePractitioner');
            node.classList.add('inactivePractitioner');
        }
    });

    checkDate(dateToCheck);
}

populatePractitioners();