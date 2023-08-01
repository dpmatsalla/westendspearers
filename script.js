document.addEventListener('DOMContentLoaded', function () {
  const calendarContainer = document.getElementById('calendar');

  // Sample data - Replace this with your own event data
  const events = [
    { date: '2023-08-15', title: 'Event 1' },
    { date: '2023-08-22', title: 'Event 2' },
    { date: '2023-08-28', title: 'Event 3' },
  ];

  const currentDate = new Date();
  const currentYear = currentDate.getFullYear();
  const currentMonth = currentDate.getMonth();
  const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
  const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);

  const daysInMonth = lastDayOfMonth.getDate();
  const startingDay = firstDayOfMonth.getDay();

  let calendarHTML = '<table>';
  calendarHTML += '<tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>';
  let day = 1;

  // Create the cells for the calendar
  for (let i = 0; i < 6; i++) {
    calendarHTML += '<tr>';

    for (let j = 0; j < 7; j++) {
      if (i === 0 && j < startingDay) {
        calendarHTML += '<td></td>';
      } else if (day > daysInMonth) {
        break;
      } else {
        const formattedDate = `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        const event = events.find((event) => event.date === formattedDate);
        const cellContent = event ? `<span class="event">${event.title}</span><br>${day}` : day;
        calendarHTML += `<td>${cellContent}</td>`;
        day++;
      }
    }

    calendarHTML += '</tr>';

    if (day > daysInMonth) {
      break;
    }
  }

  calendarHTML += '</table>';
  calendarContainer.innerHTML = calendarHTML;
});
