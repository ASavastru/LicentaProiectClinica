let date = new Date();
let year = date.getFullYear();
let month = date.getMonth();

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
            const dateToCheck = new Date(year, month, Number(dayCELL.innerText)+1)
            console.log(dateToCheck);
            checkDate(dateToCheck);

        })
    })
}

function checkDate(date){
    const dataToSend = new FormData();
    dataToSend.set("dateToCheck", "@"+Math.round(date.getTime()/1000));
    axios.post("appointment/checkDate", dataToSend).then(function (response){
        if (response.data.status === true) {
            let div
            div = "<ul>"
            for(let i = 8; i <= 20; i++){
                let flag = false;
                let firstName = null;
                let lastName = null;
                let cnp = null;
                let isInsured = null;
                for (let count = 0; count <= response.data.count; count++){
                    if (i == response.data.timestart[count]){
                        flag = true;
                        firstName = response.data.patientfirstname[count];
                        lastName = response.data.patientlastname[count];
                        cnp = response.data.cnp[count];
                        isInsured = response.data.isInsured[count];
                        if (isInsured)
                            isInsured = 'Insurance Valid & Up to Date';
                    }
                }
                if (flag == true){
                    div += `<li>`+i+`:00<div class="div-patient-first-name">`+firstName+` `+lastName+`, `+cnp+`, `+isInsured+`</div></li>`;
                }
                else {
                    div += `<li>`+i+`:00<div></div></li>`;
                }
            }
            div += "</ul>"
            document.getElementById("main-day-container").innerHTML = div;
        }
    })
}