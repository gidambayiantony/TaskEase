document.addEventListener("DOMContentLoaded", function() {
    const toggleModeBtn = document.querySelector('.toggle-mode');
    if (toggleModeBtn) {
        toggleModeBtn.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
        });
    }

    const editButtons = document.querySelectorAll('.edit-todo');
    if (editButtons.length) {
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const title = this.dataset.title;
                const priority = this.dataset.priority;
                const due_date = this.dataset.due_date;
                const description = this.dataset.description;
                const category = this.dataset.category;
                const subtasks = this.dataset.subtasks;

                document.getElementById('edit-id').value = id;
                document.getElementById('edit-title').value = title;
                document.getElementById('edit-priority').value = priority;
                document.getElementById('edit-due_date').value = due_date;
                document.getElementById('edit-description').value = description;
                document.getElementById('edit-category').value = category;
                document.getElementById('edit-subtasks').value = subtasks;

                $('#editModal').modal('show');
            });
        });
    }

    // Handle theme change
    const themeSelector = document.getElementById('theme');
    if (themeSelector) {
        themeSelector.addEventListener('change', function() {
            const theme = this.value;
            document.body.classList.remove('light', 'dark');
            document.body.classList.add(theme);
        });
    }

    // Handle background image upload
    const backgroundImageInput = document.getElementById('background_image');
    if (backgroundImageInput) {
        backgroundImageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.body.style.backgroundImage = `url(${e.target.result})`;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Drag and Drop for reordering
    const todoList = document.getElementById('todo-list');
    if (todoList) {
        $('#todo-list').sortable({
            update: function(event, ui) {
                let order = $(this).sortable('toArray', { attribute: 'data-id' });
                console.log(order); // Send this array to the server to save the new order
            }
        });
    }
});
