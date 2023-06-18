// Function to retrieve the list of users
async function retrieveUserList() {
    try {
        const response = await axios.post('/user/list');
        const { users } = response.data;
        renderUserList(users);
    } catch (error) {
        console.error('Error:', error);
    }
}

// Function to render the user list in the DOM
function renderUserList(users) {
    const userListContainer = document.getElementById('user-list');
    const ul = document.createElement('ul');

    users.forEach(user => {
        const li = document.createElement('li');
        const firstName = document.createElement('span');
        const lastName = document.createElement('span');

        firstName.textContent = 'First Name: ' + user.firstName;
        lastName.textContent = 'Last Name: ' + user.lastName;

        li.appendChild(firstName);
        li.appendChild(lastName);

        ul.appendChild(li);
    });

    userListContainer.appendChild(ul);
}

// Call the function to retrieve the user list
retrieveUserList();
