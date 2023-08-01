document.addEventListener('DOMContentLoaded', function () {
  const calendarContainer = document.getElementById('calendar');

  // Sample data - Replace this with your own event data
  const events = [
    { date: '2023-07-15', title: 'Event 1' },
    { date: '2023-07-22', title: 'Event 2' },
    { date: '2023-07-28', title: 'Event 3' },
  ];

  const currentDate = new Date();
  const currentYear = currentDate.getFullYear();
  const currentMonth = currentDate.getMonth();
  const today = currentDate.getDate();
  const lastDayToShow = today + 40;

  const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
  let dayOfWeek = firstDayOfMonth.getDay(); // Initialize dayOfWeek based on the starting day of the month

  let calendarHTML = '<table>';
  calendarHTML += '<tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>';

  let day = today;

  while (day <= lastDayToShow) {
    const dateToDisplay = new Date(currentYear, currentMonth, day);

    if (dayOfWeek === 0) {
      calendarHTML += '<tr>';
    }

    const formattedDate = `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
    const event = events.find((event) => event.date === formattedDate);
    const cellContent = event ? `<span class="event">${event.title}</span><br>${day}` : day;

    calendarHTML += `<td>${cellContent}</td>`;

    if (dayOfWeek === 6) {
      calendarHTML += '</tr>';
    }

    day++;
    dayOfWeek = (dayOfWeek + 1) % 7; // Update dayOfWeek for the next day
  }

  // Add empty cells to complete the last week if needed
  while (dayOfWeek !== 0) {
    calendarHTML += '<td></td>';
    dayOfWeek = (dayOfWeek + 1) % 7;
  }

  calendarHTML += '</tr>';
  calendarHTML += '</table>';

  calendarContainer.innerHTML = calendarHTML;
});
