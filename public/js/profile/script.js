document.getElementById("avatar").addEventListener("change", function (e) {
    if (e.target.files && e.target.files[0]) {
        const file = e.target.files[0];

        // Verificar tamanho do arquivo (máximo 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert("A imagem deve ter no máximo 2MB");
            this.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            document.querySelector(".avatar-wrapper img").src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Handle the position dropdown "Other" option
document.addEventListener("DOMContentLoaded", function () {
    const positionSelect = document.getElementById("position");
    const customPositionInput = document.getElementById("custom_position");

    if (positionSelect && customPositionInput) {
        positionSelect.addEventListener("change", function () {
            if (this.value === "Outro") {
                customPositionInput.style.display = "block";
                customPositionInput.focus();
            } else {
                customPositionInput.style.display = "none";
                customPositionInput.value = "";
            }
        });
    }
});

// Task Completion Heatmap
document.addEventListener("DOMContentLoaded", function () {
    // Sample data - in a real app, this would come from the backend
    // Format: { 'YYYY-MM-DD': count }
    const taskCompletionData = {
        // This is sample data - in a real implementation, you would fetch this from your backend
        "2025-01-01": 2,
        "2025-01-02": 0,
        "2025-01-03": 1,
        "2025-01-15": 3,
        "2025-01-23": 5,
        "2025-02-05": 4,
        "2025-02-10": 2,
        "2025-02-15": 1,
        "2025-02-28": 3,
        "2025-03-01": 2,
        "2025-03-10": 7,
        "2025-03-15": 4,
        "2025-03-25": 3,
        "2025-04-01": 1,
        "2025-04-05": 6,
        "2025-04-08": 2,
        "2025-04-09": 3,
        "2025-04-10": 1,
    };

    // Generate the heatmap
    generateHeatmap(taskCompletionData);

    // Handle filter buttons
    const filterButtons = document.querySelectorAll(".filter-btn");
    filterButtons.forEach((button) => {
        button.addEventListener("click", function () {
            // Remove active class from all buttons
            filterButtons.forEach((btn) => btn.classList.remove("active"));

            // Add active class to clicked button
            this.classList.add("active");

            // Apply filter
            const filter = this.getAttribute("data-filter");
            applyFilter(filter, taskCompletionData);
        });
    });
});

function generateHeatmap(data) {
    const container = document.getElementById("heatmap-container");
    container.innerHTML = "";

    // Create month labels
    const monthLabels = document.createElement("div");
    monthLabels.className = "month-labels";
    const months = [
        "Jan",
        "Fev",
        "Mar",
        "Abr",
        "Mai",
        "Jun",
        "Jul",
        "Ago",
        "Set",
        "Out",
        "Nov",
        "Dez",
    ];
    months.forEach((month) => {
        const label = document.createElement("div");
        label.textContent = month;
        monthLabels.appendChild(label);
    });
    container.appendChild(monthLabels);

    // Create grid
    const grid = document.createElement("div");
    grid.className = "heatmap-grid";

    // Add day labels (Mon-Sun)
    const dayLabels = ["Seg", "Ter", "Qua", "Qui", "Sex", "Sáb", "Dom"];
    dayLabels.forEach((day) => {
        const label = document.createElement("div");
        label.className = "heatmap-label";
        label.textContent = day;
        grid.appendChild(label);

        // For each day of the week, create 53 cells (for 53 weeks in a year max)
        for (let week = 0; week < 53; week++) {
            const cell = document.createElement("div");
            cell.className = "heatmap-day";

            // Calculate the date for this cell
            const currentDate = new Date();
            const startOfYear = new Date(currentDate.getFullYear(), 0, 1);
            const dayOfWeek = dayLabels.indexOf(day);

            // Adjust to get the right day of the week
            const dayOffset =
                dayOfWeek -
                startOfYear.getDay() +
                (startOfYear.getDay() === 0 ? -6 : 1);
            const cellDate = new Date(startOfYear);
            cellDate.setDate(startOfYear.getDate() + dayOffset + week * 7);

            // Skip future dates
            if (cellDate > currentDate) {
                cell.style.visibility = "hidden";
                grid.appendChild(cell);
                continue;
            }

            // Format date as YYYY-MM-DD for lookup
            const dateStr = cellDate.toISOString().split("T")[0];

            // Set cell color based on task count
            const count = data[dateStr] || 0;
            let level = 0;

            if (count > 0) {
                if (count <= 1) level = 1;
                else if (count <= 3) level = 2;
                else if (count <= 5) level = 3;
                else level = 4;

                cell.classList.add(`level-${level}`);

                // Add tooltip
                const tooltip = document.createElement("div");
                tooltip.className = "heatmap-tooltip";
                tooltip.textContent = `${count} ${
                    count === 1 ? "tarefa" : "tarefas"
                } em ${formatDate(cellDate)}`;
                cell.appendChild(tooltip);
            }

            grid.appendChild(cell);
        }
    });

    container.appendChild(grid);
}

function applyFilter(filter, data) {
    let filteredData = {};
    const currentDate = new Date();

    switch (filter) {
        case "year":
            // Show full year data
            filteredData = data;
            break;

        case "month":
            // Filter to current month only
            const currentMonth = currentDate.getMonth();
            const currentYear = currentDate.getFullYear();

            Object.keys(data).forEach((dateStr) => {
                const date = new Date(dateStr);
                if (
                    date.getMonth() === currentMonth &&
                    date.getFullYear() === currentYear
                ) {
                    filteredData[dateStr] = data[dateStr];
                }
            });
            break;

        case "week":
            // Filter to current week only
            const startOfWeek = new Date(currentDate);
            startOfWeek.setDate(
                currentDate.getDate() -
                    currentDate.getDay() +
                    (currentDate.getDay() === 0 ? -6 : 1)
            );
            startOfWeek.setHours(0, 0, 0, 0);

            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6);
            endOfWeek.setHours(23, 59, 59, 999);

            Object.keys(data).forEach((dateStr) => {
                const date = new Date(dateStr);
                if (date >= startOfWeek && date <= endOfWeek) {
                    filteredData[dateStr] = data[dateStr];
                }
            });
            break;
    }

    // Regenerate heatmap with filtered data
    generateHeatmap(filteredData);
}

function formatDate(date) {
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}
