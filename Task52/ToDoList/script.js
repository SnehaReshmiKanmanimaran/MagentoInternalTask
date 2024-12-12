const inputBox = document.getElementById("input-box");
const listContainer = document.getElementById("list-container");

// Success message container to display when all tasks are complete
const messageContainer = document.createElement("div");
messageContainer.id = "messageContainer";
messageContainer.style.display = "none"; // Initially hidden
document.body.appendChild(messageContainer); // Add to the body

function addTask() {
    if (inputBox.value === '') {
        alert("You must add something!");
    } else {
        let li = document.createElement("li");
        li.innerHTML = inputBox.value;
        listContainer.appendChild(li);

        let span = document.createElement("span");
        span.innerHTML = "\u00d7"; // Close button
        li.appendChild(span);
        checkAllTasksCompleted();
    }
    inputBox.value = ""; // Clear input after adding task
    saveData();
    checkAllTasksCompleted(); // Check tasks after adding a new one
}

listContainer.addEventListener("click", function (e) {
    if (e.target.tagName === "LI") {
        e.target.classList.toggle("checked");
        saveData();
        checkAllTasksCompleted(); // Check tasks when clicked
    } else if (e.target.tagName === "SPAN") {
        e.target.parentElement.remove();
        saveData();
        // checkAllTasksCompleted(); // Check tasks after deletion
    }
}, false);

// Save tasks to local storage
function saveData() {
    localStorage.setItem("data", listContainer.innerHTML);
}

// Show tasks from local storage
function showTask() {
    listContainer.innerHTML = localStorage.getItem("data") || "";
}
showTask();

// Check if all tasks are marked as completed
function checkAllTasksCompleted() {
    const tasks = document.querySelectorAll("#list-container li");
    const allCompleted = Array.from(tasks).every(task => task.classList.contains("checked"));

    // Display message if all tasks are completed, hide if not
    if (allCompleted && tasks.length > 0) {
        displaySuccessMessage();
    } else {
        messageContainer.style.display = "none"; // Hide the message if not all tasks are completed
    }
}

function displaySuccessMessage() {
    messageContainer.innerHTML = `
        <div style="text-align: center; padding: 20px; position: relative;">
            <h3>Congratulations! All tasks are completed!</h3>
            <img src="images/cartoon.jpeg" alt="Success Cartoon" style="width:100px;">
            <button id="closeMessage" style="
                position: absolute;
                top: 10px;
                right: 10px;
                background-color: transparent;
                border: none;
                font-size: 18px;
                cursor: pointer;
                color: #333;
            ">&times;</button>
        </div>
    `;
    messageContainer.style.display = "block"; // Show the success message
    triggerConfetti();

    // Close button functionality
    const closeMessageButton = document.getElementById("closeMessage");
    closeMessageButton.addEventListener("click", function() {
        messageContainer.style.display = "none"; // Hide the success message
    });
}

function triggerConfetti() {
    // Fire confetti from random positions
    for (let i = 0; i < 300; i++) {
        confetti({
            particleCount: 1,
            angle: 60 + Math.random() * 120, // Random angle between 60 and 180 degrees
            spread: 70,
            origin: {
                x: Math.random(), // Random x position
                y: Math.random() - 0.2 // Random y position, slightly above the bottom
            }
        });
    }
}
