// Task array
let tasks = [];
let currentFilter = 'all';
let progressChartInstance = null; // Store chart instance to prevent duplication

const tasksContainer = document.getElementById('tasksContainer');
const addTaskButton = document.getElementById('addTaskBtn');
const taskModal = document.getElementById('taskModal');
const closeBtn = document.querySelector('.close-btn');
const submitButton = document.getElementById('submitTaskBtn');
const filterButtons = document.querySelectorAll('.filter-btn');

const taskIdInput = document.getElementById('taskId');
const taskTitleInput = document.getElementById('taskTitle');
const taskDescriptionInput = document.getElementById('taskDescription');
const taskPriorityInput = document.getElementById('taskPriority');
const taskDueDateInput = document.getElementById('taskDueDate');

const totalTasksElement = document.getElementById("total-tasks");
const completedTasksElement = document.getElementById("completed-tasks");
const inProgressElement = document.getElementById("in-progress");
const openTasksElement = document.getElementById("open-tasks");

// Open Modal for New Task
addTaskButton.addEventListener('click', () => {
    taskIdInput.value = '';
    taskTitleInput.value = '';
    taskDescriptionInput.value = '';
    taskPriorityInput.value = 'low';
    taskDueDateInput.value = '';
    submitButton.textContent = 'Create Task';
    taskModal.style.display = 'flex';
});

// Close Modal
function closeTaskModal() {
    taskModal.style.display = 'none';
}

// Submit Task
submitButton.addEventListener('click', () => {
    const id = taskIdInput.value ? parseInt(taskIdInput.value) : Date.now();
    const title = taskTitleInput.value;
    const description = taskDescriptionInput.value;
    const priority = taskPriorityInput.value;
    const dueDate = taskDueDateInput.value;

    if (!title) {
        alert('Task title is required!');
        return;
    }

    const taskIndex = tasks.findIndex(task => task.id === id);
    if (taskIndex > -1) {
        tasks[taskIndex] = { id, title, description, priority, dueDate, completed: false };
    } else {
        tasks.push({ id, title, description, priority, dueDate, completed: false });
    }

    closeTaskModal();
    renderTasks();
});

// Render Tasks
function renderTasks() {
    tasksContainer.innerHTML = '';

    const filteredTasks = tasks.filter(task => 
        currentFilter === 'all' || task.priority === currentFilter
    );

    filteredTasks.forEach(task => {
        const taskCard = document.createElement('div');
        taskCard.classList.add('task-card');

        const priorityClass = {
            low: 'priority-low',
            medium: 'priority-medium',
            high: 'priority-high'
        }[task.priority] || '';

        taskCard.innerHTML = `
            <div class="task-title">${task.title}</div>
            <div class="task-description">${task.description}</div>
            <div class="mini-card">
                <div class="task-meta">
                    <span class="task-date">${task.dueDate}</span>
                    <span class="task-priority ${priorityClass}">${task.priority}</span>
                </div>
                <div class="task-actions">
                    <button class="task-action" onclick="toggleTaskCompletion(${task.id})">
                        ${task.completed ? '<i id="star-yes" class="fa-solid fa-star" style="color: #ffbb00bf;"></i>' : '<i id="star-no" class="fa-solid fa-star" style="color: #d2c9c9a1;"></i>'}
                    </button>
                    <button class="task-action" onclick="editTask(${task.id})"><i class="fa-solid fa-pen-to-square" style="color: #007bff82;"></i></button>
                    <button class="task-action" onclick="deleteTask(${task.id})"> <i class="fa-solid fa-trash" style="color: #f22323bf;"></i></button>
                </div>
            </div>
        `;

        tasksContainer.appendChild(taskCard);
    });

    updateTaskStatsAndChart();
}

// Edit Task
function editTask(id) {
    const task = tasks.find(t => t.id === id);
    if (!task) return;

    taskIdInput.value = task.id;
    taskTitleInput.value = task.title;
    taskDescriptionInput.value = task.description;
    taskPriorityInput.value = task.priority;
    taskDueDateInput.value = task.dueDate;
    submitButton.textContent = 'Update Task';
    taskModal.style.display = 'flex';
}

// Delete Task
function deleteTask(id) {
    tasks = tasks.filter(task => task.id !== id);
    renderTasks();
}

// Toggle Completion
function toggleTaskCompletion(id) {
    const task = tasks.find(t => t.id === id);
    if (task) task.completed = !task.completed;
    renderTasks();
}

// Filter Tasks
filterButtons.forEach(button => {
    button.addEventListener('click', () => {
        document.querySelector('.filter-btn.active').classList.remove('active');
        button.classList.add('active');
        currentFilter = button.dataset.filter;
        renderTasks();
    });
});

function updateTaskStatsAndChart() {
    const totalTasks = tasks.length;
    const completedTasks = tasks.filter(task => task.completed).length;
    const inProgressTasks = tasks.filter(task => !task.completed).length;
    const openTasks = tasks.filter(task => task.status === "pending").length;

    if (totalTasksElement) totalTasksElement.textContent = totalTasks;
    if (completedTasksElement) completedTasksElement.textContent = completedTasks;
    if (inProgressElement) inProgressElement.textContent = inProgressTasks;
    if (openTasksElement) openTasksElement.textContent = openTasks;

    // Update Progress Chart
    const ctx = document.getElementById("progressChart").getContext("2d");

    if (progressChartInstance) {
        progressChartInstance.destroy(); // Destroy previous chart before creating a new one
    }

    progressChartInstance = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["Completed", "Pending"],
            datasets: [{
                data: [completedTasks, openTasks + inProgressTasks],
                backgroundColor: ["#8BCE89", "#EB4E31"]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });
}

// Initial Render
renderTasks();
