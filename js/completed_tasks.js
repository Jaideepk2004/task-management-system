function renderCompletedTasks() {
    const taskCardsContainer = document.querySelector('.task-cards');
    taskCardsContainer.innerHTML = ''; // Clear existing content

    const completedTasks = tasks.filter(task => task.completed); // Filter only completed tasks

    if (completedTasks.length === 0) {
        taskCardsContainer.innerHTML = '<p class="no-tasks">No completed tasks found.</p>';
        return;
    }

    completedTasks.forEach(task => {
        const taskCard = document.createElement('div');
        taskCard.classList.add('task-card');

        taskCard.innerHTML = `
            <div class="task-title">${task.title}</div>
            <div class="task-description">${task.description}</div>
            <div class="mini-card">
                <div class="task-meta">
                    <span class="task-date">${task.dueDate}</span>
                    <span class="task-priority priority-${task.priority}">${task.priority}</span>
                </div>
                <div class="task-actions">
                    <button class="task-action" onclick="deleteTask(${task.id})">
                        <i class="fa-solid fa-trash" style="color: #f22323bf;"></i>
                    </button>
                </div>
            </div>
        `;

        taskCardsContainer.appendChild(taskCard);
    });
}
