// Function to get the last Sunday before today
function getLastSunday() {
  const today = new Date();
  const day = today.getDay();
  const lastSunday = new Date(today);
  lastSunday.setDate(today.getDate() - day);
  return lastSunday;
}

// Function to generate calendar cells and events
function generateCalendar() {
  const tableBody = document.querySelector("#calendar tbody");
  const lastSunday = getLastSunday();
  let currentDate = new Date(lastSunday);

  // Dummy list of events (Replace this with your actual events data)
  const events = [
    { date: "2023-07-30", event: "Event 1" },
    { date: "2023-08-03", event: "Event 2" },
    { date: "2023-08-08", event: "Event 3" },
    // Add more events here...
  ];

  // Get today's date in "YYYY-MM-DD" format
  const todayDate = new Date().toISOString().split("T")[0];

  // Generate 6 weeks (42 cells) of calendar
  for (let week = 0; week < 6; week++) {
    const row = document.createElement("tr");

    for (let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
      const cell = document.createElement("td");
      const cellDate = currentDate.toISOString().split("T")[0]; // Format: "YYYY-MM-DD"
      cell.textContent = cellDate;

      // Check if there are any events on this date
      const eventInfo = events.find(event => event.date === cellDate);
      if (eventInfo) {
        const eventElement = document.createElement("div");
        eventElement.textContent = eventInfo.event;
        cell.appendChild(eventElement);
      }

      // Add today's date style
      if (cellDate === todayDate) {
        cell.classList.add("today");
      }

      row.appendChild(cell);
      currentDate.setDate(currentDate.getDate() + 1); // Move to the next day
    }

    tableBody.appendChild(row);
  }
}

// Call the function to generate the calendar
generateCalendar();
